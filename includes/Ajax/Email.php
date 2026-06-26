<?php

namespace CCSCalculator\Includes\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

class Email
{
    public function register()
    {
        add_action('wp_ajax_send_summary_email', [$this, 'send_summary_email']);
        add_action('wp_ajax_nopriv_send_summary_email', [$this, 'send_summary_email']);

        // Issues a fresh nonce so submissions work even when the calculator page
        // is served from a cache or has been open long enough for its embedded
        // nonce to expire. admin-ajax responses are never cached.
        add_action('wp_ajax_ccs_refresh_nonce', [$this, 'refresh_nonce']);
        add_action('wp_ajax_nopriv_ccs_refresh_nonce', [$this, 'refresh_nonce']);
    }

    public function refresh_nonce()
    {
        wp_send_json_success(['nonce' => wp_create_nonce('ccs_frontend')]);
    }

    public function send_summary_email()
    {
        // --- Security: verify nonce (CSRF / open-relay protection) ---
        if (!check_ajax_referer('ccs_frontend', 'nonce', false)) {
            wp_send_json_error('Security check failed. Please refresh the page and try again.');
            return;
        }

        // --- Security: honeypot (silently reject bots that fill the hidden field) ---
        if (!empty($_POST['ccs_hp'])) {
            wp_send_json_error('Submission rejected.');
            return;
        }

        // --- Security: rate limit submissions per IP to prevent flooding ---
        if ($this->is_rate_limited()) {
            wp_send_json_error('Too many submissions. Please try again in a few minutes.');
            return;
        }

        // Check form type - if HubSpot, skip sending SMTP emails (HubSpot handles it via automation)
        $form_type = get_option('ccs_form_type', 'hubspot');
        $skip_smtp_email = ($form_type === 'hubspot');

        // Sanitize input
        $user_name = sanitize_text_field($_POST['user_name'] ?? '');
        $user_email = sanitize_email($_POST['user_email'] ?? '');
        $user_phone = sanitize_text_field($_POST['user_phone'] ?? '');
        $summary_html = $this->sanitize_summary_html($_POST['summary_html'] ?? '');

        // Step 1 information
        $location = sanitize_text_field($_POST['location'] ?? '');
        $atsi_status = sanitize_text_field($_POST['atsi_status'] ?? '');
        $enrolment_option = sanitize_text_field($_POST['enrolment_option'] ?? '');

        // Consent (for privacy compliance)
        $consent_privacy = !empty($_POST['consent_privacy']) ? 1 : 0;
        $consent_contact = !empty($_POST['consent_contact']) ? 1 : 0;
        $consent_source  = sanitize_text_field($_POST['consent_source'] ?? '');

        if (empty($user_name) || empty($user_email) || empty($summary_html)) {
            wp_send_json_error('Required fields missing');
            return;
        }

        // Validate the email before it is ever used as a mail recipient
        if (!is_email($user_email)) {
            wp_send_json_error('Please enter a valid email address');
            return;
        }

        // Save as CPT
        $post_id = wp_insert_post([
            'post_type' => 'ccs_submission',
            'post_title' => $user_name . ' - ' . current_time('Y-m-d H:i'),
            'post_content' => $summary_html,
            'post_status' => 'publish',
        ]);

        if (is_wp_error($post_id)) {
            wp_send_json_error('Failed to save submission');
            return;
        }

        if ($post_id) {
            update_post_meta($post_id, 'user_name', $user_name);
            update_post_meta($post_id, 'user_email', $user_email);
            update_post_meta($post_id, 'user_phone', $user_phone);
            update_post_meta($post_id, 'submission_date', current_time('mysql'));
            
            // Save Step 1 information
            update_post_meta($post_id, 'location', $location);
            update_post_meta($post_id, 'atsi_status', $atsi_status);
            update_post_meta($post_id, 'enrolment_option', $enrolment_option);

            // Consent record (privacy compliance)
            update_post_meta($post_id, 'consent_privacy', $consent_privacy);
            update_post_meta($post_id, 'consent_contact', $consent_contact);
            update_post_meta($post_id, 'consent_source', $consent_source);
            update_post_meta($post_id, 'consent_timestamp', current_time('mysql'));

            // Phase 1A: shadow-validate the browser's numbers against the
            // server-side engine. Diagnostic only — never blocks the submission.
            $this->shadow_validate($post_id);
        } else {
            wp_send_json_error('Failed to save submission');
            return;
        }

        // Only send SMTP emails if using Custom Form (not HubSpot)
        if (!$skip_smtp_email) {
            // Send email to user
            $this->send_user_email($user_name, $user_email, $user_phone, $summary_html);

            // Send separate email to admin
            $this->send_admin_email($user_name, $user_email, $user_phone, $summary_html, $post_id, $location, $atsi_status, $enrolment_option);
        }

        wp_send_json_success([
            'message' => 'Submission saved' . (!$skip_smtp_email ? ' and emails sent!' : '!'),
            'post_id' => $post_id,
            'emails_sent' => !$skip_smtp_email
        ]);
    }
    
    private function send_user_email($user_name, $user_email, $user_phone, $summary_html)
    {
        $site_name = get_bloginfo('name');
        
        // Get email template settings from admin panel
        $header_image = get_option('ccs_email_header_image', '');
        $contact_phone = get_option('ccs_email_contact_phone', '1800 222 543');
        $contact_email = get_option('ccs_email_contact_email', get_option('admin_email'));
        $facebook = get_option('ccs_email_facebook', '');
        $twitter = get_option('ccs_email_twitter', '');
        $instagram = get_option('ccs_email_instagram', '');
        $linkedin = get_option('ccs_email_linkedin', '');
        
        // Get customizable content from admin panel
        $subject = get_option('ccs_email_template_subject', 'Your Child Care Subsidy Estimate');
        $greeting = get_option('ccs_email_template_greeting', 'Hi {firstname},');
        $intro = get_option('ccs_email_template_intro', 'Thank you for using our Child Care Subsidy Calculator. Here\'s your personalized estimate:');
        $body = get_option('ccs_email_template_body', 'This estimate is based on the information you provided and current government rates. Actual amounts may vary.');
        $footer_text = get_option('ccs_email_template_footer_text', 'If you have any questions, please don\'t hesitate to contact us.');
        
        // Get colors from admin panel
        $template_colors = get_option('ccs_email_template_colors', array(
            'primary' => '#0073aa',
            'background' => '#f8f9fa',
            'text' => '#333333',
            'accent' => '#00a32a'
        ));
        
        $primary_color = $template_colors['primary'] ?? '#0073aa';
        $background_color = $template_colors['background'] ?? '#f8f9fa';
        $text_color = $template_colors['text'] ?? '#333333';
        $accent_color = $template_colors['accent'] ?? '#00a32a';
        
        // Replace {firstname} placeholder
        $first_name = explode(' ', $user_name)[0];
        $greeting = str_replace('{firstname}', $first_name, $greeting);
        
        $headers = ['Content-Type: text/html; charset=UTF-8'];
        
        // Build social media icons
        $social_icons = '';
        if ($facebook) {
            $social_icons .= '<a href="' . esc_url($facebook) . '" style="display:inline-block; margin:0 5px;"><img src="https://cdn-icons-png.flaticon.com/32/733/733547.png" alt="Facebook" width="24" height="24"></a>';
        }
        if ($twitter) {
            $social_icons .= '<a href="' . esc_url($twitter) . '" style="display:inline-block; margin:0 5px;"><img src="https://cdn-icons-png.flaticon.com/32/733/733579.png" alt="Twitter" width="24" height="24"></a>';
        }
        if ($instagram) {
            $social_icons .= '<a href="' . esc_url($instagram) . '" style="display:inline-block; margin:0 5px;"><img src="https://cdn-icons-png.flaticon.com/32/733/733558.png" alt="Instagram" width="24" height="24"></a>';
        }
        if ($linkedin) {
            $social_icons .= '<a href="' . esc_url($linkedin) . '" style="display:inline-block; margin:0 5px;"><img src="https://cdn-icons-png.flaticon.com/32/733/733561.png" alt="LinkedIn" width="24" height="24"></a>';
        }
        
        $message = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; background-color:' . esc_attr($background_color) . ';">
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:' . esc_attr($background_color) . '; padding:20px 0;">
                <tr>
                    <td align="center">
                        <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden;">
                            
                            ' . ($header_image ? '
                            <!-- Header Image -->
                            <tr>
                                <td style="padding:0;">
                                    <img src="' . esc_url($header_image) . '" alt="' . esc_attr($site_name) . '" style="width:100%; height:auto; display:block;">
                                </td>
                            </tr>
                            ' : '') . '
                            
                            <!-- Main Content -->
                            <tr>
                                <td style="padding:30px;">
                                    <!-- Greeting -->
                                    <h2 style="color:' . esc_attr($primary_color) . '; margin:0 0 20px 0; font-size:24px;">' . esc_html($greeting) . '</h2>
                                    
                                    <!-- Introduction -->
                                    <p style="margin:0 0 20px 0; font-size:14px; color:' . esc_attr($text_color) . '; line-height:1.6;">
                                        ' . wp_kses_post($intro) . '
                                    </p>
                                    
                                    <!-- Summary Content with Email-Safe Styling -->
                                    <div style="margin:20px 0;">
                                        <style type="text/css">
                                            .c-summary-table__row { display:table; width:100%; border-bottom:1px solid #f0f0f1; padding:12px 0; }
                                            .c-summary-table__row__label { display:table-cell; width:60%; font-weight:600; color:#333; font-size:15px; }
                                            .c-summary-table__row__value { display:table-cell; width:40%; text-align:right; font-weight:700; font-size:16px; }
                                            .c-summary-table__row:last-child { border-bottom:none; }
                                        </style>
                                        ' . $summary_html . '
                                    </div>
                                    
                                    <!-- Additional Body Content -->
                                    ' . ($body ? '<p style="margin:20px 0; font-size:14px; color:' . esc_attr($text_color) . '; line-height:1.6;">' . wp_kses_post($body) . '</p>' : '') . '
                                </td>
                            </tr>
                            
                            <!-- Contact Section -->
                            ' . (($contact_phone || $contact_email) ? '
                            <tr>
                                <td style="padding:20px 30px; background:#f9f9f9; border-top:1px solid #e0e0e0;">
                                    <p style="margin:0 0 15px 0; font-size:14px; color:' . esc_attr($text_color) . '; text-align:center;">
                                        Contact us for more information
                                    </p>
                                    <div style="text-align:center;">
                                        ' . ($contact_email ? '<a href="mailto:' . esc_attr($contact_email) . '" style="display:inline-block; color:' . esc_attr($primary_color) . '; text-decoration:none; margin:0 10px;">📧 ' . esc_html($contact_email) . '</a>' : '') . '
                                        ' . ($contact_phone ? '<a href="tel:' . esc_attr(str_replace(' ', '', $contact_phone)) . '" style="display:inline-block; color:' . esc_attr($primary_color) . '; text-decoration:none; margin:0 10px;">📞 ' . esc_html($contact_phone) . '</a>' : '') . '
                                    </div>
                                </td>
                            </tr>
                            ' : '') . '
                            
                            ' . ($social_icons ? '
                            <!-- Footer with Social Media -->
                            <tr>
                                <td style="padding:20px 30px; text-align:center; background:#ffffff; border-top:1px solid #e0e0e0;">
                                    <p style="margin:0 0 10px 0; font-size:13px; color:#666;">Follow us on social media</p>
                                    <div style="margin:0;">
                                        ' . $social_icons . '
                                    </div>
                                </td>
                            </tr>
                            ' : '') . '
                            
                            <!-- Footer Text -->
                            ' . ($footer_text ? '
                            <tr>
                                <td style="padding:20px 30px; text-align:center; border-top:1px solid #e0e0e0;">
                                    <p style="margin:0; font-size:13px; color:#666;">
                                        ' . wp_kses_post($footer_text) . '
                                    </p>
                                </td>
                            </tr>
                            ' : '') . '
                            
                            <!-- Final Footer -->
                            <tr>
                                <td style="padding:15px 30px; text-align:center; background:' . esc_attr($background_color) . '; font-size:12px; color:#999;">
                                    <p style="margin:0;">
                                        © ' . date('Y') . ' ' . esc_html($site_name) . '. All rights reserved.
                                    </p>
                                </td>
                            </tr>
                            
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>';
        
        wp_mail($user_email, $subject, $message, $headers);
    }
    
    private function send_admin_email($user_name, $user_email, $user_phone, $summary_html, $post_id, $location = '', $atsi_status = '', $enrolment_option = '')
    {
        // Get admin notification recipients
        $admin_emails = get_option('ccs_admin_notification_emails', get_option('admin_email'));
        $recipients = array_filter(array_map('trim', explode("\n", $admin_emails)));
        if (empty($recipients)) {
            $recipients = [get_option('admin_email')];
        }
        
        $site_name = get_bloginfo('name');
        $edit_link = admin_url('admin.php?page=ccs-submissions&action=view&post_id=' . $post_id);
        
        // Get admin email template settings
        $subject = get_option('ccs_admin_email_subject', 'New CCS Calculation Submission - {name}');
        $subject = str_replace('{name}', $user_name, $subject);
        
        $heading = get_option('ccs_admin_email_heading', '📬 New CCS Submission');
        $intro = get_option('ccs_admin_email_intro', 'A new Child Care Subsidy calculation has been submitted.');
        $footer_text = get_option('ccs_admin_email_footer', 'This is an automated notification from your website.');
        
        $show_location = get_option('ccs_admin_email_show_location', 1);
        $show_timestamp = get_option('ccs_admin_email_show_timestamp', 1);
        
        // Get colors from admin panel
        $admin_colors = get_option('ccs_admin_email_colors', array(
            'header' => '#d63638',
            'button' => '#0073aa',
            'background' => '#f4f4f4',
            'text' => '#333333'
        ));
        
        $header_color = $admin_colors['header'] ?? '#d63638';
        $button_color = $admin_colors['button'] ?? '#0073aa';
        $background_color = $admin_colors['background'] ?? '#f4f4f4';
        $text_color = $admin_colors['text'] ?? '#333333';
        
        $headers = ['Content-Type: text/html; charset=UTF-8'];
        
        $message = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin:0; padding:0; font-family: Arial, sans-serif; background-color:' . esc_attr($background_color) . ';">
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:' . esc_attr($background_color) . '; padding:20px 0;">
                <tr>
                    <td align="center">
                        <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                            <!-- Header -->
                            <tr>
                                <td style="background:linear-gradient(135deg, ' . esc_attr($header_color) . ' 0%, #a02123 100%); padding:30px; text-align:center;">
                                    <h1 style="color:#ffffff; margin:0; font-size:28px;">' . esc_html($heading) . '</h1>
                                    <p style="color:#ffffff; margin:10px 0 0 0; opacity:0.9;">' . esc_html($site_name) . ' Admin Notification</p>
                                </td>
                            </tr>
                            
                            ' . ($intro ? '
                            <!-- Introduction -->
                            <tr>
                                <td style="padding:20px 30px 0 30px;">
                                    <p style="margin:0; font-size:14px; color:' . esc_attr($text_color) . ';">' . esc_html($intro) . '</p>
                                </td>
                            </tr>
                            ' : '') . '
                            
                            <!-- User Details -->
                            <tr>
                                <td style="padding:30px;">
                                    <h2 style="color:' . esc_attr($text_color) . '; margin:0 0 20px 0; font-size:20px;">User Information</h2>
                                    <table width="100%" cellpadding="8" cellspacing="0" style="border:1px solid #e9ecef; border-radius:4px;">
                                        <tr style="background:#f8f9fa;">
                                            <td style="font-weight:bold; color:#495057; width:30%;">Name:</td>
                                            <td style="color:#212529;">' . esc_html($user_name) . '</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight:bold; color:#495057;">Email:</td>
                                            <td style="color:#212529;"><a href="mailto:' . esc_attr($user_email) . '" style="color:' . esc_attr($button_color) . '; text-decoration:none;">' . esc_html($user_email) . '</a></td>
                                        </tr>
                                        <tr style="background:#f8f9fa;">
                                            <td style="font-weight:bold; color:#495057;">Phone:</td>
                                            <td style="color:#212529;">' . ($user_phone ? esc_html($user_phone) : 'Not provided') . '</td>
                                        </tr>
                                        ' . ($show_timestamp ? '
                                        <tr>
                                            <td style="font-weight:bold; color:#495057;">Submitted:</td>
                                            <td style="color:#212529;">' . current_time('F j, Y g:i a') . '</td>
                                        </tr>
                                        ' : '') . '
                                    </table>
                                </td>
                            </tr>
                            
                            ' . ($show_location ? '
                            <!-- Step 1 Information -->
                            <tr>
                                <td style="padding:0 30px 30px 30px;">
                                    <h2 style="color:' . esc_attr($text_color) . '; margin:0 0 20px 0; font-size:20px;">📍 Location & Details</h2>
                                    <table width="100%" cellpadding="8" cellspacing="0" style="border:1px solid #e9ecef; border-radius:4px;">
                                        <tr style="background:#f8f9fa;">
                                            <td style="font-weight:bold; color:#495057; width:40%;">Location:</td>
                                            <td style="color:#212529;">' . ($location ? esc_html($location) : 'Not provided') . '</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight:bold; color:#495057;">ATSI Status:</td>
                                            <td style="color:#212529;">' . ($atsi_status ? ucfirst(esc_html($atsi_status)) : 'Not provided') . '</td>
                                        </tr>
                                        <tr style="background:#f8f9fa;">
                                            <td style="font-weight:bold; color:#495057;">Enrolment Option:</td>
                                            <td style="color:#212529;">' . ($enrolment_option ? esc_html($enrolment_option) : 'Not provided') . '</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            ' : '') . '
                            
                            <!-- CCS Summary -->
                            <tr>
                                <td style="padding:0 30px 30px 30px;">
                                    <h2 style="color:' . esc_attr($text_color) . '; margin:0 0 20px 0; font-size:20px;">Calculation Summary</h2>
                                    <div style="background:#f8f9fa; padding:20px; border-radius:6px; border-left:4px solid ' . esc_attr($button_color) . ';">
                                        <style type="text/css">
                                            .c-summary-table__row { display:table; width:100%; border-bottom:1px solid #e0e0e0; padding:10px 0; }
                                            .c-summary-table__row__label { display:table-cell; width:60%; font-weight:600; color:#333; font-size:14px; }
                                            .c-summary-table__row__value { display:table-cell; width:40%; text-align:right; font-weight:700; font-size:15px; }
                                            .c-summary-table__row:last-child { border-bottom:none; }
                                        </style>
                                        ' . $summary_html . '
                                    </div>
                                </td>
                            </tr>
                            
                            ' . ($footer_text ? '
                            <!-- Footer -->
                            <tr>
                                <td style="background:#f8f9fa; padding:20px 30px; border-top:1px solid #e9ecef;">
                                    <p style="margin:0; font-size:13px; color:#666; text-align:center;">
                                        ' . esc_html($footer_text) . '<br>
                                        <a href="' . admin_url('edit.php?post_type=ccs_submission') . '" style="color:' . esc_attr($button_color) . ';">View All Submissions</a>
                                    </p>
                                </td>
                            </tr>
                            ' : '') . '
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>';
        
        // Send to all recipients
        foreach ($recipients as $recipient) {
            wp_mail(trim($recipient), $subject, $message, $headers);
        }
    }

    /**
     * Phase 1A shadow validation.
     *
     * Recomputes the calculation server-side with CCSEngine and compares the
     * result to the totals the browser sent. Stores the outcome as post meta and
     * (under WP_DEBUG) logs any mismatch. This NEVER alters the submission, the
     * stored summary, or the emails — it only measures server/browser parity so
     * we can trust the engine before making it authoritative (Phase 1B).
     *
     * @param int $post_id
     */
    private function shadow_validate($post_id)
    {
        try {
            if (!class_exists('CCSCalculator\\Includes\\Calculator\\CCSEngine')) {
                return;
            }
            if (empty($_POST['calc_inputs']) || empty($_POST['calc_totals'])) {
                return;
            }

            $raw_inputs = json_decode(wp_unslash($_POST['calc_inputs']), true);
            $browser    = json_decode(wp_unslash($_POST['calc_totals']), true);
            if (!is_array($raw_inputs) || !is_array($browser)) {
                return;
            }

            // Build a clean, typed input set (the engine also casts internally).
            $children = [];
            if (!empty($raw_inputs['children']) && is_array($raw_inputs['children'])) {
                foreach ($raw_inputs['children'] as $child) {
                    $children[] = [
                        'dob'           => sanitize_text_field($child['dob'] ?? ''),
                        'care_type'     => sanitize_text_field($child['care_type'] ?? 'cbdc'),
                        'hours_per_day' => (float) ($child['hours_per_day'] ?? 0),
                        'fee_per_day'   => (float) ($child['fee_per_day'] ?? 0),
                        'days_week1'    => (int) ($child['days_week1'] ?? 0),
                        'days_week2'    => (int) ($child['days_week2'] ?? 0),
                    ];
                }
            }

            $input = [
                'knows_ccs'       => !empty($raw_inputs['knows_ccs']),
                'income'          => (float) ($raw_inputs['income'] ?? 0),
                'activity_hours'  => (float) ($raw_inputs['activity_hours'] ?? 0),
                'withholding_pct' => (float) ($raw_inputs['withholding_pct'] ?? 0.05),
                'is_atsi'         => !empty($raw_inputs['is_atsi']),
                'standard_pct'    => (float) ($raw_inputs['standard_pct'] ?? 0),
                'higher_pct'      => (float) ($raw_inputs['higher_pct'] ?? 0),
                'children'        => $children,
            ];

            $policy = get_option('childcare_ccs_policy', []);
            $engine = new \CCSCalculator\Includes\Calculator\CCSEngine(is_array($policy) ? $policy : []);
            $computed = $engine->calculate($input);
            $server = $computed['totals'];

            // Store the server-computed figures as the AUTHORITATIVE record for
            // this submission (Phase 1B, Option A). The browser HTML is kept for
            // display/email because parity is verified; these stored figures are
            // the trustworthy source of truth for admin/reporting/HubSpot.
            $authoritative = [
                'standard_pct'            => round($computed['standard_pct'] * 100, 2),
                'higher_pct'              => round($computed['higher_pct'] * 100, 2),
                'ccs_hours_per_fortnight' => $computed['ccs_hours_per_fortnight'],
                'totals'                  => array_map(function ($v) {
                    return round((float) $v, 2);
                }, $server),
                'computed_at'             => current_time('mysql'),
                'engine_version'          => '1.0',
            ];
            update_post_meta($post_id, 'ccs_server_figures', wp_json_encode($authoritative));

            // Compare the key per-fortnight totals within a 1-cent tolerance.
            $keys = ['fortnightFee', 'fortnightSub', 'fortnightSubBeforeWithholding', 'outPocket'];
            $diffs = [];
            foreach ($keys as $k) {
                $b = isset($browser[$k]) ? (float) $browser[$k] : 0.0;
                $s = isset($server[$k]) ? (float) $server[$k] : 0.0;
                if (abs($b - $s) > 0.01) {
                    $diffs[$k] = ['browser' => round($b, 2), 'server' => round($s, 2)];
                }
            }

            $result = empty($diffs) ? 'match' : 'mismatch';
            update_post_meta($post_id, 'ccs_shadow_result', $result);
            if (!empty($diffs)) {
                update_post_meta($post_id, 'ccs_shadow_diffs', wp_json_encode($diffs));
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('CCS authoritative recompute mismatch (post ' . $post_id . '): ' . wp_json_encode($diffs));
                }
            }
        } catch (\Throwable $e) {
            // Never let diagnostics break a submission.
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('CCS shadow-validation error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Sanitize the calculator summary HTML.
     *
     * Restricts the submitted markup to the exact set of tags/attributes the
     * summary actually uses, so the rendered result is unchanged while any
     * script/event-handler injection is stripped before it reaches emails,
     * the database, or wp-admin.
     */
    private function sanitize_summary_html($html)
    {
        $common = ['style' => true, 'class' => true];
        $cell   = array_merge($common, [
            'colspan' => true, 'rowspan' => true, 'width' => true, 'align' => true, 'valign' => true,
        ]);

        $allowed = [
            'div'    => $common,
            'span'   => $common,
            'p'      => $common,
            'h1'     => $common,
            'h2'     => $common,
            'h3'     => $common,
            'h4'     => $common,
            'h5'     => $common,
            'strong' => $common,
            'b'      => $common,
            'em'     => $common,
            'i'      => $common,
            'small'  => $common,
            'br'     => [],
            'hr'     => $common,
            'ul'     => $common,
            'ol'     => $common,
            'li'     => $common,
            'table'  => array_merge($common, ['width' => true, 'cellpadding' => true, 'cellspacing' => true, 'border' => true]),
            'thead'  => $common,
            'tbody'  => $common,
            'tfoot'  => $common,
            'tr'     => $common,
            'td'     => $cell,
            'th'     => $cell,
        ];

        return wp_kses($html, $allowed);
    }

    /**
     * Basic per-IP rate limiting using transients to deter flooding/abuse
     * of this public endpoint. Allows up to 10 submissions per 10 minutes.
     */
    private function is_rate_limited()
    {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '';
        if (empty($ip)) {
            return false;
        }

        $key   = 'ccs_rate_' . md5($ip);
        $count = (int) get_transient($key);

        if ($count >= 10) {
            return true;
        }

        set_transient($key, $count + 1, 10 * MINUTE_IN_SECONDS);
        return false;
    }
}

