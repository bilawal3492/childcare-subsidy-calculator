<?php
/**
 * Email Settings Diagnostic Script
 * 
 * Upload this file to your WordPress root directory
 * Access it via: yourdomain.com/check-email-settings.php
 * 
 * This will show you what email settings are currently saved in the database
 */

// Load WordPress
require_once('wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Settings Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f0f0f1; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 1200px; margin: 0 auto; }
        h1 { color: #0073aa; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #0073aa; color: white; }
        .found { color: green; font-weight: bold; }
        .not-found { color: red; font-weight: bold; }
        .value { font-family: monospace; background: #f5f5f5; padding: 5px; border-radius: 3px; }
        .section { margin: 30px 0; }
        .status { padding: 10px; border-radius: 5px; margin: 20px 0; }
        .status.success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .status.error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Email Template Settings Diagnostic</h1>
        
        <?php
        // Check if Email.php has been updated
        $email_file = __DIR__ . '/includes/Ajax/Email.php';
        $email_content = file_get_contents($email_file);
        $is_updated = strpos($email_content, 'get_option(\'ccs_email_template_subject\'') !== false;
        ?>
        
        <div class="status <?php echo $is_updated ? 'success' : 'error'; ?>">
            <strong>Email.php Status:</strong> 
            <?php echo $is_updated ? '✅ Updated (using admin panel settings)' : '❌ Old version (hardcoded)'; ?>
        </div>
        
        <div class="section">
            <h2>User Email Template Settings</h2>
            <table>
                <tr>
                    <th>Setting Name</th>
                    <th>Status</th>
                    <th>Current Value</th>
                </tr>
                <?php
                $user_settings = array(
                    'ccs_email_template_subject' => 'Subject Line',
                    'ccs_email_template_greeting' => 'Greeting',
                    'ccs_email_template_intro' => 'Introduction',
                    'ccs_email_template_body' => 'Body Content',
                    'ccs_email_template_footer_text' => 'Footer Text',
                    'ccs_email_header_image' => 'Header Image URL',
                    'ccs_email_button_text' => 'Button Text',
                    'ccs_email_button_url' => 'Button URL',
                    'ccs_email_contact_phone' => 'Contact Phone',
                    'ccs_email_contact_email' => 'Contact Email',
                    'ccs_email_facebook' => 'Facebook URL',
                    'ccs_email_twitter' => 'Twitter URL',
                    'ccs_email_instagram' => 'Instagram URL',
                    'ccs_email_linkedin' => 'LinkedIn URL',
                );
                
                foreach ($user_settings as $key => $label) {
                    $value = get_option($key);
                    $exists = $value !== false && $value !== '';
                    echo '<tr>';
                    echo '<td>' . $label . '</td>';
                    echo '<td class="' . ($exists ? 'found' : 'not-found') . '">' . ($exists ? '✅ Set' : '❌ Not Set') . '</td>';
                    echo '<td class="value">' . ($exists ? esc_html(substr($value, 0, 100)) . (strlen($value) > 100 ? '...' : '') : 'Using default') . '</td>';
                    echo '</tr>';
                }
                
                // Colors
                $colors = get_option('ccs_email_template_colors');
                echo '<tr>';
                echo '<td>Colors</td>';
                echo '<td class="' . ($colors ? 'found' : 'not-found') . '">' . ($colors ? '✅ Set' : '❌ Not Set') . '</td>';
                echo '<td class="value">' . ($colors ? json_encode($colors) : 'Using defaults') . '</td>';
                echo '</tr>';
                ?>
            </table>
        </div>
        
        <div class="section">
            <h2>Admin Email Template Settings</h2>
            <table>
                <tr>
                    <th>Setting Name</th>
                    <th>Status</th>
                    <th>Current Value</th>
                </tr>
                <?php
                $admin_settings = array(
                    'ccs_admin_email_subject' => 'Subject Line',
                    'ccs_admin_email_heading' => 'Email Heading',
                    'ccs_admin_email_intro' => 'Introduction',
                    'ccs_admin_email_button_text' => 'Button Text',
                    'ccs_admin_email_footer' => 'Footer Text',
                    'ccs_admin_notification_emails' => 'Recipients',
                );
                
                foreach ($admin_settings as $key => $label) {
                    $value = get_option($key);
                    $exists = $value !== false && $value !== '';
                    echo '<tr>';
                    echo '<td>' . $label . '</td>';
                    echo '<td class="' . ($exists ? 'found' : 'not-found') . '">' . ($exists ? '✅ Set' : '❌ Not Set') . '</td>';
                    echo '<td class="value">' . ($exists ? esc_html(substr($value, 0, 100)) . (strlen($value) > 100 ? '...' : '') : 'Using default') . '</td>';
                    echo '</tr>';
                }
                
                // Admin Colors
                $admin_colors = get_option('ccs_admin_email_colors');
                echo '<tr>';
                echo '<td>Colors</td>';
                echo '<td class="' . ($admin_colors ? 'found' : 'not-found') . '">' . ($admin_colors ? '✅ Set' : '❌ Not Set') . '</td>';
                echo '<td class="value">' . ($admin_colors ? json_encode($admin_colors) : 'Using defaults') . '</td>';
                echo '</tr>';
                ?>
            </table>
        </div>
        
        <div class="section">
            <h2>📋 Action Required</h2>
            <?php
            $any_user_set = false;
            foreach ($user_settings as $key => $label) {
                if (get_option($key) !== false && get_option($key) !== '') {
                    $any_user_set = true;
                    break;
                }
            }
            
            if (!$any_user_set): ?>
                <div class="status error">
                    <strong>⚠️ No settings saved yet!</strong><br>
                    You need to:
                    <ol>
                        <li>Go to WordPress Admin → Email Template</li>
                        <li>Customize your email templates</li>
                        <li>Click "💾 Save Email Templates" button</li>
                        <li>Refresh this page to see the saved values</li>
                    </ol>
                </div>
            <?php else: ?>
                <div class="status success">
                    <strong>✅ Settings are saved!</strong><br>
                    Your email templates should now use the admin panel settings.
                    <br><br>
                    If emails still look old:
                    <ol>
                        <li>Clear WordPress cache (if using a caching plugin)</li>
                        <li>Deactivate and reactivate the plugin</li>
                        <li>Check WordPress debug.log for "CCS Email: Using NEW template system"</li>
                    </ol>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <p><a href="<?php echo admin_url('admin.php?page=child-care-email'); ?>" class="button">← Back to Email Template Editor</a></p>
        </div>
    </div>
</body>
</html>
