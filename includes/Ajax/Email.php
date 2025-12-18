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
    }

    public function send_summary_email()
    {
        // Log for debugging
        error_log('CCS: send_summary_email called');
        
        // Check form type - if HubSpot, skip sending SMTP emails (HubSpot handles it via automation)
        $form_type = get_option('ccs_form_type', 'hubspot');
        $skip_smtp_email = ($form_type === 'hubspot');
        
        error_log('CCS: Form type is: ' . $form_type . ' | Skip SMTP email: ' . ($skip_smtp_email ? 'Yes' : 'No'));
        
        // Sanitize input
        $user_name = sanitize_text_field($_POST['user_name'] ?? '');
        $user_email = sanitize_email($_POST['user_email'] ?? '');
        $user_phone = sanitize_text_field($_POST['user_phone'] ?? '');
        $summary_html = wp_kses_post($_POST['summary_html'] ?? '');
        
        // Step 1 information
        $location = sanitize_text_field($_POST['location'] ?? '');
        $atsi_status = sanitize_text_field($_POST['atsi_status'] ?? '');
        $enrolment_option = sanitize_text_field($_POST['enrolment_option'] ?? '');

        if (empty($user_name) || empty($user_email) || empty($summary_html)) {
            error_log('CCS: Missing required fields');
            wp_send_json_error('Required fields missing');
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
            error_log('CCS: Error creating post - ' . $post_id->get_error_message());
            wp_send_json_error('Failed to save submission');
            return;
        }

        if ($post_id) {
            error_log('CCS: Submission saved with ID: ' . $post_id);
            update_post_meta($post_id, 'user_name', $user_name);
            update_post_meta($post_id, 'user_email', $user_email);
            update_post_meta($post_id, 'user_phone', $user_phone);
            update_post_meta($post_id, 'submission_date', current_time('mysql'));
            
            // Save Step 1 information
            update_post_meta($post_id, 'location', $location);
            update_post_meta($post_id, 'atsi_status', $atsi_status);
            update_post_meta($post_id, 'enrolment_option', $enrolment_option);
        } else {
            error_log('CCS: Failed to create submission post');
            wp_send_json_error('Failed to save submission');
            return;
        }

        // Only send SMTP emails if using Custom Form (not HubSpot)
        if (!$skip_smtp_email) {
            // Send email to user
            $this->send_user_email($user_name, $user_email, $user_phone, $summary_html);
            
            // Send separate email to admin
            $this->send_admin_email($user_name, $user_email, $user_phone, $summary_html, $post_id, $location, $atsi_status, $enrolment_option);
            
            error_log('CCS: SMTP emails sent (Custom Form mode)');
        } else {
            error_log('CCS: SMTP emails skipped (HubSpot handles emails via automation)');
        }

        error_log('CCS: Submission complete, ID: ' . $post_id);
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
        
        // Debug log
        error_log('CCS Email: Using NEW template system');
        error_log('CCS Email Subject: ' . $subject);
        error_log('CCS Email Greeting: ' . $greeting);
        
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
}

