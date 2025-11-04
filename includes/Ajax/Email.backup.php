<?php
/**
 * BACKUP FILE - Original Hardcoded Email Templates
 * Created: 2025-10-08
 * 
 * This file contains the original hardcoded email templates before
 * they were replaced with admin panel customizable templates.
 * 
 * DO NOT USE THIS FILE - It's kept for reference only.
 */

namespace CCSCalculator\Includes\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

class Email_Backup
{
    /**
     * ORIGINAL USER EMAIL TEMPLATE (Hardcoded)
     * This was the original template sent to users
     */
    private function send_user_email_ORIGINAL($user_name, $user_email, $user_phone, $summary_html)
    {
        $site_name = get_bloginfo('name');
        $accent_color = get_option('ccs_accent_color', '#0073aa');
        
        // Get email template settings
        $header_image = get_option('ccs_email_header_image', '');
        $contact_phone = get_option('ccs_email_contact_phone', '1800 222 543');
        $contact_email = get_option('ccs_email_contact_email', get_option('admin_email'));
        $button_text = get_option('ccs_email_button_text', 'View your detailed estimate online');
        $button_url = get_option('ccs_email_button_url', home_url());
        $facebook = get_option('ccs_email_facebook', '');
        $twitter = get_option('ccs_email_twitter', '');
        $instagram = get_option('ccs_email_instagram', '');
        $linkedin = get_option('ccs_email_linkedin', '');
        
        $subject = "Your Child Care Subsidy Estimator Results";
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
        <body style="margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; background-color:#f5f5f5;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f5f5; padding:20px 0;">
                <tr>
                    <td align="center">
                        <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:0; overflow:hidden;">
                            
                            ' . ($header_image ? '
                            <!-- Header Image -->
                            <tr>
                                <td style="padding:0;">
                                    <img src="' . esc_url($header_image) . '" alt="' . esc_attr($site_name) . '" style="width:100%; height:auto; display:block;">
                                </td>
                            </tr>
                            ' : '') . '
                            
                            <!-- Cyan Header Bar -->
                            <tr>
                                <td style="background:#4ECDC4; padding:20px 30px;">
                                    <h1 style="color:#ffffff; margin:0; font-size:24px; font-weight:bold;">Your Child Care Subsidy Estimator Results</h1>
                                </td>
                            </tr>
                            
                            <!-- Introduction Text -->
                            <tr>
                                <td style="padding:25px 30px 15px 30px;">
                                    <p style="margin:0; font-size:14px; color:#333; line-height:1.6;">
                                        We have estimated your Child Care Subsidy and gap fee for child care based on the information you provided:
                                    </p>
                                </td>
                            </tr>
                            
                            <!-- Summary Content -->
                            <tr>
                                <td style="padding:0 30px 20px 30px;">
                                    ' . $summary_html . '
                                </td>
                            </tr>
                            
                            <!-- Disclaimer -->
                            <tr>
                                <td style="padding:0 30px 20px 30px;">
                                    <p style="margin:0 0 10px 0; font-size:13px; color:#666; line-height:1.5;">
                                        Remember: this is an estimate only. Changes to centre fees, the law, policy or your individual circumstances may mean that any Child Care Subsidy you actually are entitled to differs from the amount estimated.
                                    </p>
                                    <ul style="margin:0; padding-left:20px; font-size:13px; color:#666; line-height:1.6;">
                                        <li>The Government will withhold 5% of your weekly Child Care Subsidy entitlement, which is reflected in the gap fee above.</li>
                                        <li>The estimator does not take into account your possible eligibility for 100 hours additional childcare subsidy.</li>
                                        <li>To receive your subsidy, you will need to submit a claim in your MyGov account for each child. CCS will not be paid automatically.</li>
                                    </ul>
                                    <p style="margin:15px 0 0 0; font-size:13px; color:#666;">
                                        Please read the full terms of our <a href="' . esc_url($button_url) . '" style="color:#4ECDC4; text-decoration:underline;">Disclaimer and use of personal information</a>.
                                    </p>
                                </td>
                            </tr>
                            
                            <!-- CTA Button -->
                            <tr>
                                <td style="padding:10px 30px 30px 30px; text-align:center;">
                                    <a href="' . esc_url($button_url) . '" style="display:inline-block; background:#4ECDC4; color:#ffffff; padding:14px 30px; text-decoration:none; border-radius:4px; font-weight:bold; font-size:16px;">
                                        ' . esc_html($button_text) . '
                                    </a>
                                </td>
                            </tr>
                            
                            <!-- Contact Section -->
                            <tr>
                                <td style="padding:20px 30px; background:#f9f9f9; border-top:1px solid #e0e0e0;">
                                    <p style="margin:0 0 15px 0; font-size:14px; color:#333; text-align:center;">
                                        Contact our Family Services Team to find out more about potential savings.
                                    </p>
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center">
                                                <a href="mailto:' . esc_attr($contact_email) . '" style="display:inline-block; background:#FF9933; color:#ffffff; padding:12px 25px; text-decoration:none; border-radius:4px; font-weight:bold; margin:0 5px;">
                                                    📧 By email
                                                </a>
                                                <a href="tel:' . esc_attr(str_replace(' ', '', $contact_phone)) . '" style="display:inline-block; background:#FF9933; color:#ffffff; padding:12px 25px; text-decoration:none; border-radius:4px; font-weight:bold; margin:0 5px;">
                                                    📞 ' . esc_html($contact_phone) . '
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            
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
                            
                            <!-- Final Footer -->
                            <tr>
                                <td style="padding:15px 30px; text-align:center; background:#f5f5f5; font-size:12px; color:#999;">
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
    
    /**
     * ORIGINAL ADMIN EMAIL TEMPLATE (Hardcoded)
     * This was the original template sent to administrators
     */
    private function send_admin_email_ORIGINAL($user_name, $user_email, $user_phone, $summary_html, $post_id, $location = '', $atsi_status = '', $enrolment_option = '')
    {
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');
        $accent_color = get_option('ccs_accent_color', '#0073aa');
        $edit_link = admin_url('admin.php?page=ccs-submissions&action=view&post_id=' . $post_id);
        
        $subject = "New CCS Calculation Submission - " . $user_name;
        $headers = ['Content-Type: text/html; charset=UTF-8'];
        
        $message = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin:0; padding:0; font-family: Arial, sans-serif; background-color:#f4f4f4;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4; padding:20px 0;">
                <tr>
                    <td align="center">
                        <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                            <!-- Header -->
                            <tr>
                                <td style="background:linear-gradient(135deg, #d63638 0%, #a02123 100%); padding:30px; text-align:center;">
                                    <h1 style="color:#ffffff; margin:0; font-size:28px;">📬 New CCS Submission</h1>
                                    <p style="color:#ffffff; margin:10px 0 0 0; opacity:0.9;">' . $site_name . ' Admin Notification</p>
                                </td>
                            </tr>
                            
                            <!-- User Details -->
                            <tr>
                                <td style="padding:30px;">
                                    <h2 style="color:#333; margin:0 0 20px 0; font-size:20px;">User Information</h2>
                                    <table width="100%" cellpadding="8" cellspacing="0" style="border:1px solid #e9ecef; border-radius:4px;">
                                        <tr style="background:#f8f9fa;">
                                            <td style="font-weight:bold; color:#495057; width:30%;">Name:</td>
                                            <td style="color:#212529;">' . esc_html($user_name) . '</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight:bold; color:#495057;">Email:</td>
                                            <td style="color:#212529;"><a href="mailto:' . esc_attr($user_email) . '" style="color:#0073aa; text-decoration:none;">' . esc_html($user_email) . '</a></td>
                                        </tr>
                                        <tr style="background:#f8f9fa;">
                                            <td style="font-weight:bold; color:#495057;">Phone:</td>
                                            <td style="color:#212529;">' . ($user_phone ? esc_html($user_phone) : 'Not provided') . '</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight:bold; color:#495057;">Submitted:</td>
                                            <td style="color:#212529;">' . current_time('F j, Y g:i a') . '</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            
                            <!-- Step 1 Information -->
                            <tr>
                                <td style="padding:0 30px 30px 30px;">
                                    <h2 style="color:#333; margin:0 0 20px 0; font-size:20px;">📍 Location & Details</h2>
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
                            
                            <!-- CCS Summary -->
                            <tr>
                                <td style="padding:0 30px 30px 30px;">
                                    <h2 style="color:#333; margin:0 0 20px 0; font-size:20px;">Calculation Summary</h2>
                                    ' . $summary_html . '
                                </td>
                            </tr>
                            
                            <!-- Action Button -->
                            <tr>
                                <td style="padding:0 30px 30px 30px; text-align:center;">
                                    <a href="' . $edit_link . '" style="display:inline-block; background:' . $accent_color . '; color:#ffffff; padding:12px 30px; text-decoration:none; border-radius:4px; font-weight:bold;">
                                        View Full Submission in WordPress
                                    </a>
                                </td>
                            </tr>
                            
                            <!-- Footer -->
                            <tr>
                                <td style="background:#f8f9fa; padding:20px 30px; border-top:1px solid #e9ecef;">
                                    <p style="margin:0; font-size:13px; color:#666; text-align:center;">
                                        This is an automated notification from ' . $site_name . '<br>
                                        <a href="' . admin_url('edit.php?post_type=ccs_submission') . '" style="color:#0073aa;">View All Submissions</a>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>';
        
        wp_mail($admin_email, $subject, $message, $headers);
    }
}
