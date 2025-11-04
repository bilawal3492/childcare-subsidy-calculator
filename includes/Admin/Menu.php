<?php

namespace CCSCalculator\Includes\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class Menu
{
    public function register()
    {
        add_action('admin_menu', [$this, 'add_menus']);
        add_action('admin_enqueue_scripts', function($hook) {
            if (strpos($hook, 'child-care') !== false) {
                wp_enqueue_style('ccs-admin-beauty', CCS_CALCULATOR_PLUGIN_URL . 'assets/css/admin-beauty.css', [], '2.0.2');
            }
        });
    }

    public function add_menus()
    {
        // Main menu
        add_menu_page(
            'CCS Calculator',
            'CCS Calculator',
            'manage_options',
            'child-care-subsidy',
            [$this, 'childcare_ccs_main_page'],
            'dashicons-calculator',
            30
        );

        // Submenu: Dashboard (rename default)
        add_submenu_page(
            'child-care-subsidy',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'child-care-subsidy',
            [$this, 'childcare_ccs_main_page']
        );

        // Submenu: Calculator Settings
        add_submenu_page(
            'child-care-subsidy',
            'Calculator Settings',
            'Calculator Settings',
            'manage_options',
            'child-care-settings',
            [$this, 'childcare_ccs_settings_page']
        );

        // Submenu: Form Integration
        add_submenu_page(
            'child-care-subsidy',
            'Form Integration',
            'Form Integration',
            'manage_options',
            'child-care-hubspot',
            [$this, 'childcare_ccs_hubspot_page']
        );

        // Submenu: Appearance
        add_submenu_page(
            'child-care-subsidy',
            'Appearance',
            'Appearance',
            'manage_options',
            'child-care-styling',
            [$this, 'childcare_ccs_styling_page']
        );

        // Submenu: Custom CSS
        add_submenu_page(
            'child-care-subsidy',
            'Custom CSS',
            'Custom CSS',
            'manage_options',
            'child-care-custom-css',
            [$this, 'childcare_ccs_custom_css_page']
        );

        // Submenu: Submissions - Custom page to avoid CPT issues
        add_submenu_page(
            'child-care-subsidy',
            'Submissions',
            'Submissions',
            'manage_options',
            'ccs-submissions',
            [$this, 'childcare_ccs_submissions_page']
        );

        // Note: Suburbs Database menu is added by SuburbsManager class

        // Submenu: Email Template
        add_submenu_page(
            'child-care-subsidy',
            'Email Template',
            'Email Template',
            'manage_options',
            'child-care-email',
            [$this, 'childcare_ccs_email_page']
        );

        // Submenu: How to Use
        add_submenu_page(
            'child-care-subsidy',
            'How to Use',
            'How to Use',
            'manage_options',
            'child-care-shortcode',
            [$this, 'childcare_ccs_shortcode_page']
        );

        // Submenu: Changelog
        add_submenu_page(
            'child-care-settings',
            'Changelog',
            'Changelog',
            'manage_options',
            'child-care-changelog',
            [$this, 'childcare_ccs_changelog_page']
        );
    }

    public function childcare_ccs_settings_page()
    {
        if (!current_user_can('manage_options')) return;

        $defaults = [
            'income_base_threshold' => 80000,
            'income_zero_threshold' => 360000,
            'hourly_caps' => [
                'centre_based_day_care'   => 14.63,
                'family_day_care_all'     => 13.56,
                'oshc_below_school_age'   => 14.63,
                'oshc_school_age'         => 12.81,
                'in_home_family'          => 39.80
            ],
            'last_updated'          => date('Y-m-d'),
            'disclaimer_text'       => 'This is an estimate only. Final entitlements determined by Services Australia.',
        ];

        // Merge saved settings with defaults
        $policy = wp_parse_args(get_option('childcare_ccs_policy', []), $defaults);
        ?>
        <div class="ccs-admin-wrap">
        <div class="wrap ccs-settings-page">
            <h1 class="wp-heading-inline">
                <span class="dashicons dashicons-admin-settings" style="font-size:28px; width:28px; height:28px; margin-right:10px;"></span>
                Calculator Settings
            </h1>
            <p class="description" style="margin-top:10px; margin-bottom:20px;">Configure Child Care Subsidy rates, income thresholds, and hourly caps based on current government policy.</p>
            
            <form method="post" action="options.php">
                <?php settings_fields('childcare_ccs_group'); ?>
                <?php do_settings_sections('childcare_ccs_group'); ?>

                <div class="ccs-settings-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                    
                    <!-- Income Thresholds Card -->
                    <div class="ccs-settings-card" style="background:#fff; padding:25px; border:1px solid #ddd; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                        <h2 style="margin-top:0; padding-bottom:15px; border-bottom:2px solid #0073aa; color:#0073aa; font-size:18px;">
                            <span class="dashicons dashicons-money-alt" style="font-size:20px; width:20px; height:20px; margin-right:8px;"></span>
                            Income Thresholds
                        </h2>
                        
                        <div style="margin-top:20px;">
                            <label style="display:block; font-weight:600; margin-bottom:8px; color:#333;">
                                Income Base Threshold (90% subsidy)
                            </label>
                            <div style="position:relative;">
                                <span style="position:absolute; left:12px; top:10px; color:#666;">$</span>
                                <input type="number" 
                                       name="childcare_ccs_policy[income_base_threshold]" 
                                       value="<?php echo esc_attr($policy['income_base_threshold']); ?>"
                                       style="width:100%; padding:10px 10px 10px 25px; border:1px solid #ddd; border-radius:4px; font-size:14px;">
                            </div>
                            <p class="description" style="margin-top:5px; color:#666;">Maximum income for 90% subsidy rate</p>
                        </div>

                        <div style="margin-top:20px;">
                            <label style="display:block; font-weight:600; margin-bottom:8px; color:#333;">
                                Income Zero Threshold (0% subsidy)
                            </label>
                            <div style="position:relative;">
                                <span style="position:absolute; left:12px; top:10px; color:#666;">$</span>
                                <input type="number" 
                                       name="childcare_ccs_policy[income_zero_threshold]" 
                                       value="<?php echo esc_attr($policy['income_zero_threshold']); ?>"
                                       style="width:100%; padding:10px 10px 10px 25px; border:1px solid #ddd; border-radius:4px; font-size:14px;">
                            </div>
                            <p class="description" style="margin-top:5px; color:#666;">Income where subsidy drops to 0%</p>
                        </div>

                        <div style="margin-top:20px;">
                            <label style="display:block; font-weight:600; margin-bottom:8px; color:#333;">
                                Income Step ($ per 1% drop)
                            </label>
                            <div style="position:relative;">
                                <span style="position:absolute; left:12px; top:10px; color:#666;">$</span>
                                <input type="number" 
                                       name="childcare_ccs_policy[income_step]" 
                                       value="<?php echo esc_attr($policy['income_step']); ?>"
                                       style="width:100%; padding:10px 10px 10px 25px; border:1px solid #ddd; border-radius:4px; font-size:14px;">
                            </div>
                            <p class="description" style="margin-top:5px; color:#666;">Income increase per 1% subsidy decrease</p>
                        </div>

                        <div style="margin-top:20px;">
                            <label style="display:block; font-weight:600; margin-bottom:8px; color:#333;">
                                Maximum Subsidy Percentage
                            </label>
                            <input type="text" 
                                   name="childcare_ccs_policy[max_pct]" 
                                   value="<?php echo esc_attr($policy['max_pct']); ?>"
                                   placeholder="0.90"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-size:14px;">
                            <p class="description" style="margin-top:5px; color:#666;">Maximum subsidy as decimal (e.g., 0.90 for 90%)</p>
                        </div>
                    </div>

                    <!-- Hourly Caps Card -->
                    <div class="ccs-settings-card" style="background:#fff; padding:25px; border:1px solid #ddd; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                        <h2 style="margin-top:0; padding-bottom:15px; border-bottom:2px solid #0073aa; color:#0073aa; font-size:18px;">
                            <span class="dashicons dashicons-clock" style="font-size:20px; width:20px; height:20px; margin-right:8px;"></span>
                            Hourly Rate Caps
                        </h2>
                        
                        <?php 
                        $cap_labels = [
                            'centre_based_day_care' => 'Centre Based Day Care',
                            'family_day_care_all' => 'Family Day Care (All Ages)',
                            'oshc_below_school_age' => 'OSHC (Below School Age)',
                            'oshc_school_age' => 'OSHC (School Age)',
                            'in_home_family' => 'In Home Care (Family)'
                        ];
                        
                        foreach ($policy['hourly_caps'] as $key => $val): 
                        ?>
                            <div style="margin-top:20px;">
                                <label style="display:block; font-weight:600; margin-bottom:8px; color:#333;">
                                    <?php echo esc_html($cap_labels[$key] ?? str_replace('_', ' ', ucwords($key))); ?>
                                </label>
                                <div style="position:relative;">
                                    <span style="position:absolute; left:12px; top:10px; color:#666;">$</span>
                                    <input type="number" 
                                           step="0.01" 
                                           name="childcare_ccs_policy[hourly_caps][<?php echo esc_attr($key); ?>]" 
                                           value="<?php echo esc_attr($val); ?>"
                                           style="width:100%; padding:10px 10px 10px 25px; border:1px solid #ddd; border-radius:4px; font-size:14px;">
                                    <span style="position:absolute; right:12px; top:10px; color:#999; font-size:12px;">per hour</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                </div>
                <!-- Additional Settings Card -->
                <div class="ccs-settings-card" style="background:#fff; padding:25px; border:1px solid #ddd; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.05); margin-bottom:20px;">
                    <h2 style="margin-top:0; padding-bottom:15px; border-bottom:2px solid #0073aa; color:#0073aa; font-size:18px;">
                        <span class="dashicons dashicons-info" style="font-size:20px; width:20px; height:20px; margin-right:8px;"></span>
                        Additional Information
                    </h2>
                    
                    <div style="display:grid; grid-template-columns:1fr 2fr; gap:20px; margin-top:20px;">
                        <div>
                            <label style="display:block; font-weight:600; margin-bottom:8px; color:#333;">
                                Last Updated
                            </label>
                            <input type="date" 
                                   name="childcare_ccs_policy[last_updated]" 
                                   value="<?php echo esc_attr($policy['last_updated']); ?>"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-size:14px;">
                            <p class="description" style="margin-top:5px; color:#666;">Date of last policy update</p>
                        </div>

                        <div>
                            <label style="display:block; font-weight:600; margin-bottom:8px; color:#333;">
                                Disclaimer Text
                            </label>
                            <textarea name="childcare_ccs_policy[disclaimer_text]" 
                                      rows="3" 
                                      style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-size:14px; resize:vertical;"><?php echo esc_textarea($policy['disclaimer_text']); ?></textarea>
                            <p class="description" style="margin-top:5px; color:#666;">Displayed to users with calculation results</p>
                        </div>
                    </div>
                </div>

                <!-- Calculator Form Settings Card -->
                <div class="ccs-settings-card" style="background:#fff; padding:25px; border:1px solid #ddd; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.05); margin-bottom:20px;">
                    <h2 style="margin-top:0; padding-bottom:15px; border-bottom:2px solid #0073aa; color:#0073aa; font-size:18px;">
                        <span class="dashicons dashicons-admin-generic" style="font-size:20px; width:20px; height:20px; margin-right:8px;"></span>
                        Calculator Form Settings
                    </h2>
                    
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:20px;">
                        <div>
                            <label style="display:block; font-weight:600; margin-bottom:8px; color:#333;">
                                Default CCS Withholding Percentage
                            </label>
                            <select name="childcare_ccs_policy[default_withholding]" 
                                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-size:14px;">
                                <option value="5" <?php selected($policy['default_withholding'] ?? 5, 5); ?>>5%</option>
                                <option value="4" <?php selected($policy['default_withholding'] ?? 5, 4); ?>>4%</option>
                                <option value="3" <?php selected($policy['default_withholding'] ?? 5, 3); ?>>3%</option>
                                <option value="2" <?php selected($policy['default_withholding'] ?? 5, 2); ?>>2%</option>
                                <option value="1" <?php selected($policy['default_withholding'] ?? 5, 1); ?>>1%</option>
                                <option value="0" <?php selected($policy['default_withholding'] ?? 5, 0); ?>>0%</option>
                            </select>
                            <p class="description" style="margin-top:5px; color:#666;">Default withholding percentage shown to users</p>
                        </div>

                        <div>
                            <label style="display:block; font-weight:600; margin-bottom:8px; color:#333;">
                                Default Activity Hours Selection
                            </label>
                            <select name="childcare_ccs_policy[default_activity_hours]" 
                                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-size:14px;">
                                <option value="48" <?php selected($policy['default_activity_hours'] ?? 48, 48); ?>>More than 48 hours</option>
                                <option value="17" <?php selected($policy['default_activity_hours'] ?? 48, 17); ?>>17 hours to 48 hours</option>
                                <option value="8" <?php selected($policy['default_activity_hours'] ?? 48, 8); ?>>8 hours to 16 hours</option>
                            </select>
                            <p class="description" style="margin-top:5px; color:#666;">Default activity hours selection</p>
                        </div>
                    </div>

                    <div style="margin-top:20px;">
                        <h3 style="font-size:14px; font-weight:600; margin-bottom:15px; color:#333;">
                            Activity Hours to CCS Hours Mapping
                        </h3>
                        <div style="background:#f9f9f9; padding:15px; border-radius:4px; border:1px solid #e0e0e0;">
                            <table style="width:100%; border-collapse:collapse;">
                                <thead>
                                    <tr style="border-bottom:2px solid #ddd;">
                                        <th style="text-align:left; padding:10px; font-weight:600; color:#333;">Activity Hours per Fortnight</th>
                                        <th style="text-align:left; padding:10px; font-weight:600; color:#333;">CCS Hours per Fortnight</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="border-bottom:1px solid #e0e0e0;">
                                        <td style="padding:10px;">8 hours to 16 hours</td>
                                        <td style="padding:10px;">
                                            <input type="number" 
                                                   name="childcare_ccs_policy[ccs_hours_8_16]" 
                                                   value="<?php echo esc_attr($policy['ccs_hours_8_16'] ?? 36); ?>"
                                                   style="width:100px; padding:8px; border:1px solid #ddd; border-radius:4px;">
                                            <span style="margin-left:5px; color:#666;">hours</span>
                                        </td>
                                    </tr>
                                    <tr style="border-bottom:1px solid #e0e0e0;">
                                        <td style="padding:10px;">17 hours to 48 hours</td>
                                        <td style="padding:10px;">
                                            <input type="number" 
                                                   name="childcare_ccs_policy[ccs_hours_17_48]" 
                                                   value="<?php echo esc_attr($policy['ccs_hours_17_48'] ?? 72); ?>"
                                                   style="width:100px; padding:8px; border:1px solid #ddd; border-radius:4px;">
                                            <span style="margin-left:5px; color:#666;">hours</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:10px;">More than 48 hours</td>
                                        <td style="padding:10px;">
                                            <input type="number" 
                                                   name="childcare_ccs_policy[ccs_hours_48_plus]" 
                                                   value="<?php echo esc_attr($policy['ccs_hours_48_plus'] ?? 100); ?>"
                                                   style="width:100px; padding:8px; border:1px solid #ddd; border-radius:4px;">
                                            <span style="margin-left:5px; color:#666;">hours</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="description" style="margin-top:10px; color:#666;">Configure how activity hours map to subsidised CCS hours per fortnight</p>
                    </div>

                    <div style="margin-top:20px;">
                        <label style="display:block; font-weight:600; margin-bottom:8px; color:#333;">
                            Low Income Threshold (for 24 hours without activity test)
                        </label>
                        <div style="position:relative;">
                            <span style="position:absolute; left:12px; top:10px; color:#666;">$</span>
                            <input type="number" 
                                   name="childcare_ccs_policy[low_income_threshold]" 
                                   value="<?php echo esc_attr($policy['low_income_threshold'] ?? 85279); ?>"
                                   style="width:100%; padding:10px 10px 10px 25px; border:1px solid #ddd; border-radius:4px; font-size:14px;">
                        </div>
                        <p class="description" style="margin-top:5px; color:#666;">Families earning this amount or less get 24 hours subsidised care without activity test</p>
                    </div>

                    <div style="margin-top:20px;">
                        <label style="display:block; font-weight:600; margin-bottom:8px; color:#333;">
                            Higher CCS Income Threshold
                        </label>
                        <div style="position:relative;">
                            <span style="position:absolute; left:12px; top:10px; color:#666;">$</span>
                            <input type="number" 
                                   name="childcare_ccs_policy[higher_ccs_threshold]" 
                                   value="<?php echo esc_attr($policy['higher_ccs_threshold'] ?? 367563); ?>"
                                   style="width:100%; padding:10px 10px 10px 25px; border:1px solid #ddd; border-radius:4px; font-size:14px;">
                        </div>
                        <p class="description" style="margin-top:5px; color:#666;">Maximum income for families to be eligible for Higher CCS (second child and younger)</p>
                    </div>
                </div>

                <!-- Info Box -->
                <div style="background:#e7f5fe; border-left:4px solid #0073aa; padding:15px 20px; margin-bottom:20px; border-radius:4px;">
                    <p style="margin:0; color:#333;">
                        <span class="dashicons dashicons-info" style="color:#0073aa; margin-right:5px;"></span>
                        <strong>Important:</strong> These settings control the calculator's subsidy calculations. Ensure values match current Australian Government CCS policy. 
                        Visit <a href="https://www.servicesaustralia.gov.au/child-care-subsidy" target="_blank">Services Australia</a> for official rates.
                    </p>
                </div>

                <?php submit_button('Save Settings', 'primary large', 'submit', true, ['style' => 'padding:10px 30px; font-size:16px; height:auto;']); ?>
            </form>

            <!-- Rules & Instructions Section -->
            <div style="margin-top:40px; padding-top:30px; border-top:2px solid #ddd;">
                <h2 style="font-size:22px; margin-bottom:20px; color:#23282d;">
                    <span class="dashicons dashicons-book" style="font-size:24px; width:24px; height:24px; margin-right:8px;"></span>
                    Calculator Rules & Instructions
                </h2>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    
                    <!-- How CCS is Calculated -->
                    <div style="background:#fff; padding:25px; border:1px solid #ddd; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                        <h3 style="margin-top:0; color:#0073aa; font-size:16px; border-bottom:1px solid #e0e0e0; padding-bottom:10px;">
                            📊 How Child Care Subsidy is Calculated
                        </h3>
                        <ol style="margin:15px 0; padding-left:20px; line-height:1.8; color:#333;">
                            <li><strong>Income Assessment:</strong> Combined family income determines subsidy percentage (90% to 0%)</li>
                            <li><strong>Hourly Rate Cap:</strong> Maximum hourly rate government will subsidize based on care type and child age</li>
                            <li><strong>Subsidy Calculation:</strong> Lower of actual fee or hourly cap × subsidy percentage</li>
                            <li><strong>Out-of-Pocket:</strong> Actual fee minus subsidy amount</li>
                            <li><strong>5% Withholding:</strong> Government withholds 5% of subsidy until tax reconciliation</li>
                        </ol>
                    </div>

                    <!-- Income Thresholds Explained -->
                    <div style="background:#fff; padding:25px; border:1px solid #ddd; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                        <h3 style="margin-top:0; color:#0073aa; font-size:16px; border-bottom:1px solid #e0e0e0; padding-bottom:10px;">
                            💰 Income Thresholds Explained
                        </h3>
                        <ul style="margin:15px 0; padding-left:20px; line-height:1.8; color:#333;">
                            <li><strong>Base Threshold:</strong> Income up to this amount receives maximum 90% subsidy</li>
                            <li><strong>Zero Threshold:</strong> Income above this amount receives 0% subsidy</li>
                            <li><strong>Income Step:</strong> For every $5,000 above base, subsidy drops by 1%</li>
                            <li><strong>Example:</strong> $80,000 = 90%, $85,000 = 89%, $90,000 = 88%</li>
                        </ul>
                    </div>

                    <!-- Hourly Caps Guide -->
                    <div style="background:#fff; padding:25px; border:1px solid #ddd; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                        <h3 style="margin-top:0; color:#0073aa; font-size:16px; border-bottom:1px solid #e0e0e0; padding-bottom:10px;">
                            ⏰ Hourly Rate Caps Guide
                        </h3>
                        <div style="margin:15px 0; line-height:1.8; color:#333;">
                            <p style="margin:0 0 10px 0;"><strong>Centre Based Day Care:</strong> Long day care, preschool, kindergarten</p>
                            <p style="margin:0 0 10px 0;"><strong>Family Day Care:</strong> Care in educator's home (all ages)</p>
                            <p style="margin:0 0 10px 0;"><strong>OSHC (Below School Age):</strong> Before/after school care for under 6</p>
                            <p style="margin:0 0 10px 0;"><strong>OSHC (School Age):</strong> Before/after school care for 6-12 years</p>
                            <p style="margin:0 0 10px 0;"><strong>In Home Care:</strong> Care in child's home by qualified educator</p>
                        </div>
                    </div>

                    <!-- Update Instructions -->
                    <div style="background:#fff; padding:25px; border:1px solid #ddd; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                        <h3 style="margin-top:0; color:#0073aa; font-size:16px; border-bottom:1px solid #e0e0e0; padding-bottom:10px;">
                            🔄 When to Update Settings
                        </h3>
                        <ul style="margin:15px 0; padding-left:20px; line-height:1.8; color:#333;">
                            <li><strong>Government Policy Changes:</strong> Usually announced in Federal Budget (May)</li>
                            <li><strong>CPI Adjustments:</strong> Hourly caps indexed annually (July 1)</li>
                            <li><strong>Income Thresholds:</strong> May change with policy updates</li>
                            <li><strong>Always Check:</strong> <a href="https://www.servicesaustralia.gov.au/child-care-subsidy" target="_blank">Services Australia website</a></li>
                            <li><strong>Update Date:</strong> Record when settings were last updated</li>
                        </ul>
                    </div>

                    <!-- Important Notes -->
                    <div style="background:#fff3cd; padding:25px; border:1px solid #ffc107; border-radius:8px; grid-column:1/-1;">
                        <h3 style="margin-top:0; color:#856404; font-size:16px; border-bottom:1px solid #ffc107; padding-bottom:10px;">
                            ⚠️ Important Notes & Disclaimers
                        </h3>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:15px;">
                            <div>
                                <p style="margin:0 0 10px 0; color:#856404;"><strong>Calculator Limitations:</strong></p>
                                <ul style="margin:0; padding-left:20px; color:#856404; line-height:1.6;">
                                    <li>Provides estimates only, not official entitlements</li>
                                    <li>Does not include Additional Child Care Subsidy (ACCS)</li>
                                    <li>Does not account for activity test requirements</li>
                                    <li>Assumes continuous enrollment throughout fortnight</li>
                                </ul>
                            </div>
                            <div>
                                <p style="margin:0 0 10px 0; color:#856404;"><strong>User Responsibilities:</strong></p>
                                <ul style="margin:0; padding-left:20px; color:#856404; line-height:1.6;">
                                    <li>Users must apply through myGov for actual subsidy</li>
                                    <li>Final amounts determined by Services Australia</li>
                                    <li>Income must be verified through tax returns</li>
                                    <li>Activity test must be met for subsidy eligibility</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Reference -->
                    <div style="background:#f0f0f1; padding:25px; border:1px solid #c3c4c7; border-radius:8px; grid-column:1/-1;">
                        <h3 style="margin-top:0; color:#23282d; font-size:16px; border-bottom:1px solid #c3c4c7; padding-bottom:10px;">
                            📌 Quick Reference Links
                        </h3>
                        <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:15px; margin-top:15px;">
                            <a href="https://www.servicesaustralia.gov.au/child-care-subsidy" target="_blank" style="display:block; padding:15px; background:#fff; border:1px solid #ddd; border-radius:4px; text-decoration:none; color:#0073aa; text-align:center;">
                                <span class="dashicons dashicons-admin-site" style="font-size:24px; display:block; margin-bottom:5px;"></span>
                                <strong>Services Australia</strong><br>
                                <span style="font-size:12px; color:#666;">Official CCS Information</span>
                            </a>
                            <a href="https://i9.edu.au/" target="_blank" style="display:block; padding:15px; background:#fff; border:1px solid #ddd; border-radius:4px; text-decoration:none; color:#0073aa; text-align:center;">
                                <span class="dashicons dashicons-book-alt" style="font-size:24px; display:block; margin-bottom:5px;"></span>
                                <strong>i9 Education</strong><br>
                                <span style="font-size:12px; color:#666;">Policy Documentation & Support</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <style>
            .ccs-settings-page .ccs-settings-card input:focus,
            .ccs-settings-page .ccs-settings-card textarea:focus {
                border-color: #0073aa;
                box-shadow: 0 0 0 1px #0073aa;
                outline: none;
            }
            
            @media (max-width: 782px) {
                .ccs-settings-grid {
                    grid-template-columns: 1fr !important;
                }
            }
        </style>
        <?php
    }

    public function childcare_ccs_hubspot_page()
    {
        if (!current_user_can('manage_options')) return;
        ?>
        <div class="ccs-admin-wrap">
        <div class="wrap">
            <h1>HubSpot Integration Settings</h1>
            <p>Configure HubSpot form to collect user information and send calculator summary emails.</p>
            
            <form method="post" action="options.php">
                <?php settings_fields('childcare_ccs_hubspot_group'); ?>
                <?php do_settings_sections('childcare_ccs_hubspot_group'); ?>

                <h2>Form Integration Type</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_form_type">Form Type</label>
                        </th>
                        <td>
                            <select id="ccs_form_type" name="ccs_form_type" style="width: 300px;" onchange="toggleFormSettings(this.value)">
                                <?php
                                $form_type = get_option('ccs_form_type', 'hubspot');
                                ?>
                                <option value="hubspot" <?php selected($form_type, 'hubspot'); ?>>HubSpot Form</option>
                                <option value="custom" <?php selected($form_type, 'custom'); ?>>Custom Form (SMTP)</option>
                            </select>
                            <p class="description">Choose between HubSpot integration or custom form with SMTP email</p>
                        </td>
                    </tr>
                </table>

                <div id="hubspot-settings" style="display:<?php echo ($form_type === 'hubspot') ? 'block' : 'none'; ?>;">
                <h2>HubSpot Form Configuration</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_hubspot_portal_id">Portal ID</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_hubspot_portal_id"
                                   name="ccs_hubspot_portal_id" 
                                   value="<?php echo esc_attr(get_option('ccs_hubspot_portal_id', '')); ?>" 
                                   class="regular-text">
                            <p class="description">Your HubSpot Portal ID (e.g., 12345678)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_hubspot_form_id">Form ID</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_hubspot_form_id"
                                   name="ccs_hubspot_form_id" 
                                   value="<?php echo esc_attr(get_option('ccs_hubspot_form_id', '')); ?>" 
                                   class="regular-text">
                            <p class="description">Your HubSpot Form ID (e.g., abc123-def456-ghi789)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_hubspot_region">Region</label>
                        </th>
                        <td>
                            <select id="ccs_hubspot_region" name="ccs_hubspot_region">
                                <option value="na1" <?php selected(get_option('ccs_hubspot_region', 'na1'), 'na1'); ?>>North America (na1)</option>
                                <option value="eu1" <?php selected(get_option('ccs_hubspot_region', 'na1'), 'eu1'); ?>>Europe (eu1)</option>
                            </select>
                            <p class="description">Select your HubSpot account region</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_hubspot_hidden_field">Hidden Field Name</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_hubspot_hidden_field"
                                   name="ccs_hubspot_hidden_field" 
                                   value="<?php echo esc_attr(get_option('ccs_hubspot_hidden_field', 'calculate_property')); ?>" 
                                   class="regular-text">
                            <p class="description">Internal name of the hidden field in your HubSpot form for storing calculator summary (e.g., calculate_property)</p>
                        </td>
                    </tr>
                </table>

                <h2>How to Find Your HubSpot IDs</h2>
                <div style="background:#f9f9f9; padding:15px; border-left:4px solid #0073aa; margin:20px 0;">
                    <h3>Portal ID</h3>
                    <ol>
                        <li>Log in to HubSpot</li>
                        <li>Click Settings (gear icon)</li>
                        <li>Go to <strong>Account Defaults</strong></li>
                        <li>Your Portal ID is displayed at the top</li>
                    </ol>

                    <h3>Form ID</h3>
                    <ol>
                        <li>Go to <strong>Marketing > Lead Capture > Forms</strong></li>
                        <li>Click on your form</li>
                        <li>Click <strong>Share</strong></li>
                        <li>The Form ID is in the embed code: <code>formId: "abc123-def456"</code></li>
                    </ol>

                    <h3>Hidden Field Setup</h3>
                    <ol>
                        <li>In HubSpot, go to <strong>Settings > Properties > Contact Properties</strong></li>
                        <li>Create a new property (if not exists):
                            <ul>
                                <li>Name: Calculator Summary</li>
                                <li>Internal name: <code>calculate_property</code></li>
                                <li>Field type: Multi-line text</li>
                            </ul>
                        </li>
                        <li>Edit your form and add this field</li>
                        <li>Make it hidden</li>
                        <li>Enter the internal name above</li>
                    </ol>
                </div>
                </div>

                <div id="custom-form-settings" style="display:<?php echo ($form_type === 'custom') ? 'block' : 'none'; ?>;">
                <h2>Custom Form Configuration</h2>
                <div style="background:#e7f5fe; padding:15px; border-left:4px solid #0073aa; margin:20px 0;">
                    <h3>Setup Instructions</h3>
                    <ol>
                        <li>Install and activate <strong>WP Mail SMTP</strong> plugin</li>
                        <li>Configure SMTP settings (SendGrid, Mailgun, or Gmail recommended)</li>
                        <li>Test email delivery from WP Mail SMTP settings</li>
                        <li>The custom form will automatically appear on Step 4</li>
                        <li>Submissions will be saved and emails sent via SMTP</li>
                    </ol>
                    <p><strong>Note:</strong> No additional configuration needed. The form works once SMTP is configured.</p>
                </div>
                </div>

                <?php submit_button('Save Form Settings'); ?>
                
                <script>
                function toggleFormSettings(type) {
                    if (type === 'hubspot') {
                        document.getElementById('hubspot-settings').style.display = 'block';
                        document.getElementById('custom-form-settings').style.display = 'none';
                    } else {
                        document.getElementById('hubspot-settings').style.display = 'none';
                        document.getElementById('custom-form-settings').style.display = 'block';
                    }
                }
                </script>
            </form>

            <div style="background:#fff3cd; padding:15px; border-left:4px solid #ffc107; margin:20px 0;">
                <h3>📚 Documentation</h3>
                <p>For complete setup instructions, see: <code>HUBSPOT-SETUP-COMPLETE.md</code> in the plugin folder.</p>
            </div>
        </div>
        <?php
    }

    public function childcare_ccs_styling_page()
    {
        if (!current_user_can('manage_options')) return;
        
        // Enhanced settings handler - saves all CCS settings properly
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['ccs_styling_nonce'], 'ccs_styling_save')) {
            $settings_saved = 0;
            
            // Define all possible CCS settings to ensure they're all handled
            $all_ccs_settings = [
                // General & Typography
                'ccs_font_family', 'ccs_heading_size', 'ccs_heading_color', 'ccs_label_color', 'ccs_text_color', 'ccs_tooltip_icon_color',
                
                // Button Settings - Complete from Assets.php
                'ccs_button_font_size', 'ccs_button_font_weight', 'ccs_button_letter_spacing', 'ccs_button_margin',
                'ccs_button_height', 'ccs_button_width', 'ccs_button_padding', 'ccs_button_border_width',
                'ccs_button_bg_color', 'ccs_button_text_color', 'ccs_button_hover_color', 'ccs_button_hover_text_color',
                'ccs_button_border_color', 'ccs_button_border_hover_color', 'ccs_button_border_radius',
                
                // Input Field Settings - Complete from Assets.php
                'ccs_input_font_size', 'ccs_input_padding', 'ccs_input_width', 'ccs_input_border_width',
                'ccs_input_bg_color', 'ccs_input_border_color', 'ccs_input_focus_color', 'ccs_input_border_radius',
                
                // Legacy slider settings (to be deprecated)
                'ccs_slider_color', 'ccs_slider_bg_color',
                
                // Container & Layout Settings - MISSING FROM ADMIN
                'ccs_background_color', 'ccs_container_border_radius', 'ccs_container_shadow', 'ccs_container_padding',
                
                // Progress Bar Settings - MISSING FROM ADMIN
                'ccs_progress_active_color', 'ccs_progress_completed_color', 'ccs_progress_inactive_color', 'ccs_progress_line_color',
                'ccs_progress_text_color', 'ccs_progress_step_size',
                
                // Card & Summary Settings - MISSING FROM ADMIN
                'ccs_card_bg_color', 'ccs_card_border_color', 'ccs_card_border_radius',
                'ccs_summary_value_color', 'ccs_summary_label_color', 'ccs_accent_color', 'ccs_border_color',
                
                // Step 1 Settings - MISSING FROM ADMIN
                'ccs_step1_location_font_size', 'ccs_step1_location_padding', 'ccs_step1_location_width', 'ccs_step1_location_bg_color',
                'ccs_step1_dropdown_font_size', 'ccs_step1_dropdown_padding', 'ccs_step1_button_bg',
                
                // Step 2 Settings - MISSING FROM ADMIN
                'ccs_step2_income_font_size', 'ccs_step2_income_padding', 'ccs_step2_income_width', 'ccs_step2_income_bg_color', 'ccs_step2_income_border_color',
                'ccs_step2_activity_font_size', 'ccs_step2_activity_padding', 'ccs_step2_activity_width', 'ccs_step2_activity_bg_color', 'ccs_step2_activity_border_color',
                
                // Step 3 Settings
                'ccs_step3_child_count_font_size', 'ccs_step3_child_count_padding', 'ccs_step3_child_count_width', 'ccs_step3_child_count_margin',
                'ccs_step3_dob_font_size', 'ccs_step3_dob_padding', 'ccs_step3_dob_width', 'ccs_step3_dob_bg_color',
                
                // Step 4 Settings - Complete Results & Summary Settings
                'ccs_step4_card_font_size', 'ccs_step4_card_padding', 'ccs_step4_card_margin',
                'ccs_step4_child_info_font_size', 'ccs_step4_child_info_bg_color',
                'ccs_step4_total_font_size', 'ccs_step4_total_font_weight',
                
                // Summary Row Colors & Styling
                'ccs_summary_total_fee_color', 'ccs_summary_subsidy_color', 'ccs_summary_out_pocket_color',
                'ccs_summary_label_text_color', 'ccs_summary_row_padding', 'ccs_summary_row_bg_color', 'ccs_summary_border_color',
                
                // Weekly Breakdown Styling
                'ccs_weekly_header_color', 'ccs_weekly_header_font_size', 'ccs_weekly_text_font_size',
                
                // Child Detail Cards
                'ccs_child_card_bg_color', 'ccs_child_card_border_color', 'ccs_child_card_border_radius',
                'ccs_child_card_padding', 'ccs_child_card_text_font_size', 'ccs_child_card_header_color',
                
                // Period Selection & Child Select
                'ccs_period_btn_font_size', 'ccs_child_select_font_size',
                
                // Child Count & Fortnight Buttons
                'ccs_child_count_bg_color', 'ccs_child_count_text_color', 'ccs_child_count_border_color',
                'ccs_child_count_hover_bg_color', 'ccs_child_count_hover_text_color', 'ccs_child_count_hover_border_color',
                'ccs_child_count_active_bg_color', 'ccs_child_count_active_text_color', 'ccs_child_count_border_radius',
                'ccs_child_count_font_family', 'ccs_fortnight_btn_bg_color', 'ccs_fortnight_btn_text_color', 'ccs_fortnight_btn_border_color',
                'ccs_fortnight_btn_hover_bg_color', 'ccs_fortnight_btn_hover_text_color', 'ccs_fortnight_btn_hover_border_color',
                'ccs_fortnight_btn_active_bg_color', 'ccs_fortnight_btn_active_text_color', 'ccs_fortnight_btn_font_size',
                'ccs_fortnight_btn_border_radius', 'ccs_fortnight_btn_font_family',
                
                // Hours per day slider settings
                'ccs_slider_font_size', 'ccs_slider_border_color', 'ccs_slider_hover_color', 'ccs_slider_font_family',
                'ccs_slider_border_radius', 'ccs_slider_track_height', 'ccs_slider_thumb_size',
                'ccs_slider_track_color', 'ccs_slider_completed_color', 'ccs_slider_thumb_color',
                
                // Mobile Responsive Settings - MISSING FROM ADMIN
                'ccs_mobile_heading_size', 'ccs_mobile_input_font_size', 'ccs_mobile_button_font_size',
                'ccs_mobile_container_padding', 'ccs_mobile_input_padding', 'ccs_mobile_button_padding',
                'ccs_mobile_button_width', 'ccs_mobile_input_width', 'ccs_mobile_border_width', 'ccs_mobile_breakpoint',
                
                // Summary Page Specific Colors
                'ccs_summary_heading_color', 'ccs_total_fee_color', 'ccs_subsidy_color', 'ccs_out_of_pocket_color',
                'ccs_week_heading_color', 'ccs_fee_bg_color', 'ccs_subsidy_bg_color', 'ccs_out_of_pocket_bg_color',
                
                // Summary Period Buttons
                'ccs_period_btn_bg_color', 'ccs_period_btn_text_color', 'ccs_period_btn_hover_bg_color',
                'ccs_period_btn_hover_text_color', 'ccs_period_btn_active_bg_color', 'ccs_period_btn_active_text_color',
                'ccs_period_btn_border_radius',
                
                // Email Form Toggle
                'ccs_email_toggle_title', 'ccs_email_toggle_subtitle', 'ccs_email_toggle_icon_image',
                'ccs_email_toggle_bg_color', 'ccs_email_toggle_text_color', 'ccs_email_toggle_icon_color', 'ccs_email_form_bg_color',
                
                // Info Box
                'ccs_info_box_enabled', 'ccs_info_box_text', 'ccs_info_box_icon_image',
                'ccs_info_box_icon_color', 'ccs_info_box_text_color',
                
                // Custom Form
                'ccs_privacy_policy_url', 'ccs_privacy_policy_text', 'ccs_contact_checkbox_text',
                
                // Enrolment Options
                'ccs_enrolment_bg_color', 'ccs_enrolment_text_color', 'ccs_enrolment_border_color',
                'ccs_enrolment_hover_bg_color', 'ccs_enrolment_hover_text_color', 'ccs_enrolment_hover_border_color',
                'ccs_enrolment_active_bg_color', 'ccs_enrolment_active_text_color',
                
                // Navigation Buttons (Next/Back)
                'ccs_nav_next_bg_color', 'ccs_nav_next_text_color', 'ccs_nav_next_hover_color', 'ccs_nav_next_hover_text_color',
                'ccs_nav_next_border_color', 'ccs_nav_next_border_hover_color', 'ccs_nav_next_border_radius',
                'ccs_nav_next_padding', 'ccs_nav_next_font_size', 'ccs_nav_next_font_weight',
                'ccs_nav_back_bg_color', 'ccs_nav_back_text_color', 'ccs_nav_back_hover_color', 'ccs_nav_back_hover_text_color',
                'ccs_nav_back_border_color', 'ccs_nav_back_border_hover_color', 'ccs_nav_back_border_radius',
                'ccs_nav_back_padding', 'ccs_nav_back_font_size', 'ccs_nav_back_font_weight'
            ];
            
            // Save all submitted CCS settings
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'ccs_') === 0) {
                    // Sanitize based on field type
                    if (strpos($key, '_color') !== false) {
                        // Color fields - handle both hex and rgba
                        if (strpos($value, 'rgba') !== false || strpos($value, 'rgb') !== false) {
                            $sanitized_value = sanitize_text_field($value);
                        } else {
                            $sanitized_value = sanitize_hex_color($value);
                        }
                    } elseif ($key === 'ccs_info_box_text') {
                        // HTML content - use wp_kses_post
                        $sanitized_value = wp_kses_post($value);
                    } elseif ($key === 'ccs_info_box_enabled') {
                        // Checkbox - convert to 1 or 0
                        $sanitized_value = absint($value);
                    } elseif (strpos($key, '_url') !== false || strpos($key, '_image') !== false) {
                        // URLs - use esc_url_raw
                        $sanitized_value = esc_url_raw($value);
                    } elseif (is_numeric($value)) {
                        // Numeric fields - ensure they're numbers
                        $sanitized_value = absint($value);
                    } else {
                        // Text fields - general sanitization
                        $sanitized_value = sanitize_text_field($value);
                    }
                    
                    update_option($key, $sanitized_value);
                    $settings_saved++;
                }
            }
            
            // Handle checkbox for info_box_enabled (if not checked, it won't be in POST)
            if (!isset($_POST['ccs_info_box_enabled'])) {
                update_option('ccs_info_box_enabled', 0);
            }
            
            // Update timestamp to force CSS cache refresh
            update_option('ccs_settings_updated', time());
            
            // Also save any missing settings with their defaults to ensure consistency
            foreach ($all_ccs_settings as $setting) {
                if (!isset($_POST[$setting])) {
                    // Keep existing value or set reasonable default
                    $existing_value = get_option($setting);
                    if ($existing_value === false) {
                        // Set default values for new settings
                        $default_value = '';
                        if (strpos($setting, '_font_size') !== false) $default_value = '16';
                        elseif (strpos($setting, '_padding') !== false) $default_value = '12';
                        elseif (strpos($setting, '_width') !== false) $default_value = '100';
                        elseif (strpos($setting, '_color') !== false) $default_value = '#0073aa';
                        
                        if ($default_value) {
                            update_option($setting, $default_value);
                        }
                    }
                }
            }
            
            $settings_updated = true;
            $total_settings = $settings_saved;
        }
        
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        ?>
        <div class="ccs-admin-wrap">
        <div class="wrap">
            <h1>🎨 Calculator Appearance Settings</h1>
            <?php if (isset($settings_updated) && $settings_updated): ?>
                <div class="notice notice-success is-dismissible">
                    <p><strong>✅ Settings saved successfully! <?php echo isset($total_settings) ? $total_settings : 'All'; ?> styling options have been preserved across tabs.</strong></p>
                    <p><em>You can now switch between tabs freely - all your customizations are automatically saved!</em></p>
                </div>
            <?php endif; ?>
            <p>Customize every aspect of your calculator's appearance</p>
            
            <style>
                /* Scoped styles for CCS Appearance page only */
                .ccs-appearance-page .ccs-tabs {
                    margin: 20px 0;
                    border-bottom: 1px solid #ccc;
                }
                .ccs-appearance-page .ccs-tabs a {
                    display: inline-block;
                    padding: 12px 20px;
                    text-decoration: none;
                    color: #555;
                    border: 1px solid transparent;
                    border-bottom: none;
                    margin-right: 5px;
                    background: #f1f1f1;
                    border-radius: 4px 4px 0 0;
                    transition: all 0.3s;
                }
                .ccs-appearance-page .ccs-tabs a:hover {
                    background: #e8e8e8;
                }
                .ccs-appearance-page .ccs-tabs a.active {
                    background: #fff;
                    color: #0073aa;
                    border-color: #ccc;
                    border-bottom-color: #fff;
                    font-weight: 600;
                    position: relative;
                    bottom: -1px;
                }
                .ccs-appearance-page .ccs-tab-content {
                    background: #fff;
                    padding: 20px;
                    border: 1px solid #ccc;
                    border-top: none;
                    border-radius: 0 0 4px 4px;
                }
                .ccs-appearance-page .ccs-settings-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 30px;
                }
                @media (max-width: 1200px) {
                    .ccs-appearance-page .ccs-settings-grid {
                        grid-template-columns: 1fr;
                    }
                }
                .ccs-appearance-page .ccs-setting-group {
                    background: #f9f9f9;
                    padding: 20px;
                    border-radius: 8px;
                    border: 1px solid #e5e5e5;
                }
                .ccs-appearance-page .ccs-setting-group h3 {
                    margin-top: 0;
                    color: #0073aa;
                    border-bottom: 2px solid #0073aa;
                    padding-bottom: 10px;
                }
            </style>
            
            <div class="ccs-appearance-page">
            <div class="ccs-tabs">
                <a href="?page=child-care-styling&tab=general" class="<?php echo $active_tab === 'general' ? 'active' : ''; ?>">
                    🎨 General & Colors
                </a>
                <a href="?page=child-care-styling&tab=buttons" class="<?php echo $active_tab === 'buttons' ? 'active' : ''; ?>">
                    🔘 All Buttons
                </a>
                <a href="?page=child-care-styling&tab=inputs" class="<?php echo $active_tab === 'inputs' ? 'active' : ''; ?>">
                    📝 All Input Fields
                </a>
                <a href="?page=child-care-styling&tab=step1" class="<?php echo $active_tab === 'step1' ? 'active' : ''; ?>">
                    1️⃣ Step 1: Care Type
                </a>
                <a href="?page=child-care-styling&tab=step2" class="<?php echo $active_tab === 'step2' ? 'active' : ''; ?>">
                    2️⃣ Step 2: Income & Activity
                </a>
                <a href="?page=child-care-styling&tab=step3" class="<?php echo $active_tab === 'step3' ? 'active' : ''; ?>">
                    3️⃣ Step 3: Children Details
                </a>
                <a href="?page=child-care-styling&tab=step4" class="<?php echo $active_tab === 'step4' ? 'active' : ''; ?>">
                    4️⃣ Step 4: Results & Summary
                </a>
                <a href="?page=child-care-styling&tab=mobile" class="<?php echo $active_tab === 'mobile' ? 'active' : ''; ?>">
                    📱 Mobile & Responsive
                </a>
            </div>
            
            <form method="post" action="">
                <?php wp_nonce_field('ccs_styling_save', 'ccs_styling_nonce'); ?>
                <input type="hidden" name="active_tab" value="<?php echo esc_attr($active_tab); ?>">
                
                <!-- Form will be handled by our custom form processor -->
                
                <div class="ccs-tab-content">
                
                <?php if ($active_tab === 'general'): ?>
                <!-- GENERAL & COLORS TAB -->
                <h2>🎨 General Settings & Colors</h2>
                <p>Control overall appearance, typography, and color scheme.</p>
                
                <h3>Typography & Fonts</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_font_family">Font Family</label>
                        </th>
                        <td>
                            <select id="ccs_font_family" name="ccs_font_family" style="width: 300px;">
                                <?php
                                $current_font = get_option('ccs_font_family', 'Red Hat Display');
                                $fonts = [
                                    'Red Hat Display' => 'Red Hat Display',
                                    'Barlow' => 'Barlow',
                                    'Quicksand' => 'Quicksand',
                                    'IBM Plex Sans' => 'IBM Plex Sans',
                                    'Inter' => 'Inter',
                                    'Poppins' => 'Poppins',
                                    'Roboto' => 'Roboto',
                                    'Open Sans' => 'Open Sans',
                                    'Lato' => 'Lato',
                                    'Montserrat' => 'Montserrat',
                                    'System Default' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                                ];
                                foreach ($fonts as $name => $value) {
                                    $selected = ($current_font === $value) ? 'selected' : '';
                                    echo "<option value=\"$value\" $selected>$name</option>";
                                }
                                ?>
                            </select>
                            <p class="description">Choose the font for your calculator</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_heading_size">Main Heading Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_heading_size" 
                                   name="ccs_heading_size" 
                                   value="<?php echo esc_attr(get_option('ccs_heading_size', '32')); ?>" 
                                   min="16" 
                                   max="60" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for main calculator heading (default: 32px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_heading_color">Heading Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_heading_color" 
                                   name="ccs_heading_color" 
                                   value="<?php echo esc_attr(get_option('ccs_heading_color', '#1a1a1a')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for main headings</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_label_color">Label Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_label_color" 
                                   name="ccs_label_color" 
                                   value="<?php echo esc_attr(get_option('ccs_label_color', '#2c3e50')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for form labels</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_text_color">Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_text_color" 
                                   name="ccs_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_text_color', '#2c3e50')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for body text</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_tooltip_icon_color">Tooltip Icon Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_tooltip_icon_color" 
                                   name="ccs_tooltip_icon_color" 
                                   value="<?php echo esc_attr(get_option('ccs_tooltip_icon_color', '#84bd00')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for tooltip "i" icons</p>
                        </td>
                    </tr>
                </table>

                <h3>Container & Layout</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_background_color">Container Background Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_background_color" 
                                   name="ccs_background_color" 
                                   value="<?php echo esc_attr(get_option('ccs_background_color', '#ffffff')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for the entire calculator container</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_container_border_radius">Container Border Radius</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_container_border_radius" 
                                   name="ccs_container_border_radius" 
                                   value="<?php echo esc_attr(get_option('ccs_container_border_radius', '16')); ?>" 
                                   min="0" 
                                   max="50" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Roundness of container corners (default: 16px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_container_shadow">Container Shadow</label>
                        </th>
                        <td>
                            <select id="ccs_container_shadow" name="ccs_container_shadow" style="width: 200px;">
                                <?php
                                $current_shadow = get_option('ccs_container_shadow', 'medium');
                                $shadows = [
                                    'none' => 'No Shadow',
                                    'light' => 'Light Shadow',
                                    'medium' => 'Medium Shadow',
                                    'heavy' => 'Heavy Shadow'
                                ];
                                foreach ($shadows as $value => $name) {
                                    $selected = ($current_shadow === $value) ? 'selected' : '';
                                    echo "<option value=\"$value\" $selected>$name</option>";
                                }
                                ?>
                            </select>
                            <p class="description">Drop shadow effect for the container</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_container_padding">Container Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_container_padding" 
                                   name="ccs_container_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_container_padding', '35')); ?>" 
                                   min="10" 
                                   max="80" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Inner padding for the container (default: 35px)</p>
                        </td>
                    </tr>
                </table>

                <h3>Progress Bar</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_progress_active_color">Active Step Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_progress_active_color" 
                                   name="ccs_progress_active_color" 
                                   value="<?php echo esc_attr(get_option('ccs_progress_active_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for the current active step</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_progress_completed_color">Completed Step Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_progress_completed_color" 
                                   name="ccs_progress_completed_color" 
                                   value="<?php echo esc_attr(get_option('ccs_progress_completed_color', '#00a32a')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for completed steps</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_progress_inactive_color">Inactive Step Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_progress_inactive_color" 
                                   name="ccs_progress_inactive_color" 
                                   value="<?php echo esc_attr(get_option('ccs_progress_inactive_color', '#e1e8ed')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for future/inactive steps</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_progress_line_color">Progress Line Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_progress_line_color" 
                                   name="ccs_progress_line_color" 
                                   value="<?php echo esc_attr(get_option('ccs_progress_line_color', '#e1e8ed')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for the progress line background</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_progress_text_color">Progress Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_progress_text_color" 
                                   name="ccs_progress_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_progress_text_color', '#666666')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for progress step labels</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_progress_step_size">Progress Step Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_progress_step_size" 
                                   name="ccs_progress_step_size" 
                                   value="<?php echo esc_attr(get_option('ccs_progress_step_size', '40')); ?>" 
                                   min="30" 
                                   max="60" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Size of progress step circles (default: 40px)</p>
                        </td>
                    </tr>
                </table>

                <?php elseif ($active_tab === 'inputs'): ?>
                
                <!-- INPUTS TAB -->
                <h2>📝 All Input Fields & Form Elements</h2>
                <p>Customize the appearance of all form inputs, sliders, and interactive elements.</p>
                
                <div style="background:#e8f5e8; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#2e7d32;">📝 General Input Field Settings</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_input_font_size">Input Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_input_font_size" 
                                   name="ccs_input_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_input_font_size', '16')); ?>" 
                                   min="12" 
                                   max="24" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for input fields (default: 16px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_input_padding">Input Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_input_padding" 
                                   name="ccs_input_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_input_padding', '12')); ?>" 
                                   min="8" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Inner padding for input fields (default: 12px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_input_width">Input Width</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_input_width" 
                                   name="ccs_input_width" 
                                   value="<?php echo esc_attr(get_option('ccs_input_width', '100')); ?>" 
                                   min="50" 
                                   max="100" 
                                   style="width: 80px;">
                            <span>%</span>
                            <p class="description">Width of input fields as percentage (default: 100%)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_input_border_width">Input Border Width</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_input_border_width" 
                                   name="ccs_input_border_width" 
                                   value="<?php echo esc_attr(get_option('ccs_input_border_width', '2')); ?>" 
                                   min="1" 
                                   max="5" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Border thickness for input fields (default: 2px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_input_bg_color">Input Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_input_bg_color" 
                                   name="ccs_input_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_input_bg_color', '#f8f9fa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for input fields</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_input_border_color">Input Border Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_input_border_color" 
                                   name="ccs_input_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_input_border_color', '#e1e8ed')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color for input fields</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_input_focus_color">Input Focus Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_input_focus_color" 
                                   name="ccs_input_focus_color" 
                                   value="<?php echo esc_attr(get_option('ccs_input_focus_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color when input is focused</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_input_border_radius">Input Border Radius</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_input_border_radius" 
                                   name="ccs_input_border_radius" 
                                   value="<?php echo esc_attr(get_option('ccs_input_border_radius', '8')); ?>" 
                                   min="0" 
                                   max="30" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Roundness of input corners (default: 8px)</p>
                        </td>
                    </tr>
                </table>

                <?php elseif ($active_tab === 'buttons'): ?>
                
                <!-- ALL BUTTONS TAB -->
                <h2>🔘 Button Typography & Styling</h2>
                <p>Complete control over all button appearance including typography, colors, spacing, and dimensions.</p>
                
                <div style="background:#f8f9fa; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#0073aa;">📝 Typography Settings</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_font_size">Button Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_button_font_size" 
                                   name="ccs_button_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_button_font_size', '15')); ?>" 
                                   min="12" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for all buttons (default: 15px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_bg_color">Button Background Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_button_bg_color" 
                                   name="ccs_button_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_button_bg_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_text_color">Button Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_button_text_color" 
                                   name="ccs_button_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_button_text_color', '#ffffff')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color for buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_hover_color">Button Hover Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_button_hover_color" 
                                   name="ccs_button_hover_color" 
                                   value="<?php echo esc_attr(get_option('ccs_button_hover_color', '#005f8a')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color when hovering over buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_hover_text_color">Button Hover Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_button_hover_text_color" 
                                   name="ccs_button_hover_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_button_hover_text_color', '#ffffff')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color when hovering over buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_padding">Button Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_button_padding" 
                                   name="ccs_button_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_button_padding', '12')); ?>" 
                                   min="8" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Inner padding for buttons (default: 12px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_width">Button Width</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_button_width" 
                                   name="ccs_button_width" 
                                   value="<?php echo esc_attr(get_option('ccs_button_width', '120')); ?>" 
                                   min="80" 
                                   max="300" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Minimum width for buttons (default: 120px)</p>
                        </td>
                    </tr>
                </table>

                <div style="background:#e7f5fe; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#0073aa;">🎨 Border & Shape Settings</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_border_color">Button Border Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_button_border_color" 
                                   name="ccs_button_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_button_border_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color for buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_border_hover_color">Button Border Hover Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_button_border_hover_color" 
                                   name="ccs_button_border_hover_color" 
                                   value="<?php echo esc_attr(get_option('ccs_button_border_hover_color', '#005f8a')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color when hovering over buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_border_width">Button Border Width</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_button_border_width" 
                                   name="ccs_button_border_width" 
                                   value="<?php echo esc_attr(get_option('ccs_button_border_width', '2')); ?>" 
                                   min="0" 
                                   max="5" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Border thickness for buttons (default: 2px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_border_radius">Button Border Radius</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_button_border_radius" 
                                   name="ccs_button_border_radius" 
                                   value="<?php echo esc_attr(get_option('ccs_button_border_radius', '8')); ?>" 
                                   min="0" 
                                   max="50" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Roundness of button corners (default: 8px)</p>
                        </td>
                    </tr>
                </table>

                <div style="background:#fff3cd; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#856404;">⚙️ Advanced Typography</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_font_weight">Button Font Weight</label>
                        </th>
                        <td>
                            <select id="ccs_button_font_weight" name="ccs_button_font_weight" style="width: 200px;">
                                <?php
                                $button_weight = get_option('ccs_button_font_weight', '600');
                                ?>
                                <option value="400" <?php selected($button_weight, '400'); ?>>Normal (400)</option>
                                <option value="500" <?php selected($button_weight, '500'); ?>>Medium (500)</option>
                                <option value="600" <?php selected($button_weight, '600'); ?>>Semi-Bold (600)</option>
                                <option value="700" <?php selected($button_weight, '700'); ?>>Bold (700)</option>
                            </select>
                            <p class="description">Font weight for button text</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_letter_spacing">Button Letter Spacing</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_button_letter_spacing" 
                                   name="ccs_button_letter_spacing" 
                                   value="<?php echo esc_attr(get_option('ccs_button_letter_spacing', '0')); ?>" 
                                   min="-2" 
                                   max="3" 
                                   step="0.1"
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Letter spacing for button text (default: 0px)</p>
                        </td>
                    </tr>
                </table>

                <div style="background:#fff2e6; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#d63638;">📐 Spacing & Dimensions</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_margin">Button Margin</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_button_margin" 
                                   name="ccs_button_margin" 
                                   value="<?php echo esc_attr(get_option('ccs_button_margin', '5')); ?>" 
                                   min="0" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Space around buttons (default: 5px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_height">Button Height</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_button_height" 
                                   name="ccs_button_height" 
                                   value="<?php echo esc_attr(get_option('ccs_button_height', '44')); ?>" 
                                   min="30" 
                                   max="60" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Height of buttons (default: 44px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_container_border_radius">Container Border Radius</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_container_border_radius" 
                                   name="ccs_container_border_radius" 
                                   value="<?php echo esc_attr(get_option('ccs_container_border_radius', '16')); ?>" 
                                   min="0" 
                                   max="50" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Roundness of main container corners (default: 16px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_container_shadow">Container Shadow</label>
                        </th>
                        <td>
                            <select id="ccs_container_shadow" name="ccs_container_shadow" style="width: 300px;">
                                <?php
                                $shadow = get_option('ccs_container_shadow', 'medium');
                                ?>
                                <option value="none" <?php selected($shadow, 'none'); ?>>No Shadow</option>
                                <option value="light" <?php selected($shadow, 'light'); ?>>Light Shadow</option>
                                <option value="medium" <?php selected($shadow, 'medium'); ?>>Medium Shadow</option>
                                <option value="heavy" <?php selected($shadow, 'heavy'); ?>>Heavy Shadow</option>
                            </select>
                            <p class="description">Shadow depth for calculator container</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_container_padding">Container Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_container_padding" 
                                   name="ccs_container_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_container_padding', '35')); ?>" 
                                   min="10" 
                                   max="80" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Inner spacing of calculator container (default: 35px)</p>
                        </td>
                    </tr>
                </table>

                <!-- Navigation Buttons (Next/Back) -->
                <div style="background:#e8f5e9; padding:15px; border-radius:8px; margin:30px 0 20px 0;">
                    <h4 style="margin-top:0; color:#2e7d32;">➡️ Navigation Buttons (Next/Back)</h4>
                    <p style="margin-bottom:0;">Separate styling for Next and Back navigation buttons</p>
                </div>
                
                <h3>Next Button Styling</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_next_bg_color">Next Button Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_nav_next_bg_color" 
                                   name="ccs_nav_next_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_nav_next_bg_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for Next buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_next_text_color">Next Button Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_nav_next_text_color" 
                                   name="ccs_nav_next_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_nav_next_text_color', '#ffffff')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color for Next buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_next_hover_color">Next Button Hover Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_nav_next_hover_color" 
                                   name="ccs_nav_next_hover_color" 
                                   value="<?php echo esc_attr(get_option('ccs_nav_next_hover_color', '#005f8a')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color when hovering Next buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_next_hover_text_color">Next Button Hover Text</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_nav_next_hover_text_color" 
                                   name="ccs_nav_next_hover_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_nav_next_hover_text_color', '#ffffff')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color when hovering Next buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_next_border_color">Next Button Border Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_nav_next_border_color" 
                                   name="ccs_nav_next_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_nav_next_border_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color for Next buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_next_border_hover_color">Next Button Border Hover</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_nav_next_border_hover_color" 
                                   name="ccs_nav_next_border_hover_color" 
                                   value="<?php echo esc_attr(get_option('ccs_nav_next_border_hover_color', '#005f8a')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color when hovering Next buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_next_border_radius">Next Button Border Radius</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_nav_next_border_radius" 
                                   name="ccs_nav_next_border_radius" 
                                   value="<?php echo esc_attr(get_option('ccs_nav_next_border_radius', '8')); ?>" 
                                   min="0" 
                                   max="50" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Border radius for Next buttons (default: 8px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_next_padding">Next Button Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_nav_next_padding" 
                                   name="ccs_nav_next_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_nav_next_padding', '12')); ?>" 
                                   min="8" 
                                   max="25" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Padding for Next buttons (default: 12px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_next_font_size">Next Button Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_nav_next_font_size" 
                                   name="ccs_nav_next_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_nav_next_font_size', '15')); ?>" 
                                   min="12" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for Next buttons (default: 15px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_next_font_weight">Next Button Font Weight</label>
                        </th>
                        <td>
                            <select id="ccs_nav_next_font_weight" name="ccs_nav_next_font_weight" style="width: 200px;">
                                <?php
                                $nav_next_weight = get_option('ccs_nav_next_font_weight', '600');
                                ?>
                                <option value="400" <?php selected($nav_next_weight, '400'); ?>>Normal (400)</option>
                                <option value="500" <?php selected($nav_next_weight, '500'); ?>>Medium (500)</option>
                                <option value="600" <?php selected($nav_next_weight, '600'); ?>>Semi-Bold (600)</option>
                                <option value="700" <?php selected($nav_next_weight, '700'); ?>>Bold (700)</option>
                            </select>
                            <p class="description">Font weight for Next button text</p>
                        </td>
                    </tr>
                </table>

                <h3>Back Button Styling</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_back_bg_color">Back Button Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_nav_back_bg_color" 
                                   name="ccs_nav_back_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_nav_back_bg_color', '#f8f9fa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for Back buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_back_text_color">Back Button Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_nav_back_text_color" 
                                   name="ccs_nav_back_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_nav_back_text_color', '#2c3e50')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color for Back buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_back_hover_color">Back Button Hover Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_nav_back_hover_color" 
                                   name="ccs_nav_back_hover_color" 
                                   value="<?php echo esc_attr(get_option('ccs_nav_back_hover_color', '#e3f2fd')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color when hovering Back buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_back_hover_text_color">Back Button Hover Text</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_nav_back_hover_text_color" 
                                   name="ccs_nav_back_hover_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_nav_back_hover_text_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color when hovering Back buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_back_border_color">Back Button Border Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_nav_back_border_color" 
                                   name="ccs_nav_back_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_nav_back_border_color', '#e1e8ed')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color for Back buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_back_border_hover_color">Back Button Border Hover</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_nav_back_border_hover_color" 
                                   name="ccs_nav_back_border_hover_color" 
                                   value="<?php echo esc_attr(get_option('ccs_nav_back_border_hover_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color when hovering Back buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_back_border_radius">Back Button Border Radius</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_nav_back_border_radius" 
                                   name="ccs_nav_back_border_radius" 
                                   value="<?php echo esc_attr(get_option('ccs_nav_back_border_radius', '8')); ?>" 
                                   min="0" 
                                   max="50" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Border radius for Back buttons (default: 8px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_back_padding">Back Button Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_nav_back_padding" 
                                   name="ccs_nav_back_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_nav_back_padding', '12')); ?>" 
                                   min="8" 
                                   max="25" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Padding for Back buttons (default: 12px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_back_font_size">Back Button Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_nav_back_font_size" 
                                   name="ccs_nav_back_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_nav_back_font_size', '15')); ?>" 
                                   min="12" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for Back buttons (default: 15px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_nav_back_font_weight">Back Button Font Weight</label>
                        </th>
                        <td>
                            <select id="ccs_nav_back_font_weight" name="ccs_nav_back_font_weight" style="width: 200px;">
                                <?php
                                $nav_back_weight = get_option('ccs_nav_back_font_weight', '600');
                                ?>
                                <option value="400" <?php selected($nav_back_weight, '400'); ?>>Normal (400)</option>
                                <option value="500" <?php selected($nav_back_weight, '500'); ?>>Medium (500)</option>
                                <option value="600" <?php selected($nav_back_weight, '600'); ?>>Semi-Bold (600)</option>
                                <option value="700" <?php selected($nav_back_weight, '700'); ?>>Bold (700)</option>
                            </select>
                            <p class="description">Font weight for Back button text</p>
                        </td>
                    </tr>
                </table>

                <!-- Enrolment Option Buttons -->
                <div style="background:#fff3e0; padding:15px; border-radius:8px; margin:30px 0 20px 0;">
                    <h4 style="margin-top:0; color:#e65100;">📋 Enrolment Option Buttons</h4>
                    <p style="margin-bottom:0;">Styling for "Existing Family", "With Another Care Provider", "Not Currently Enrolled" buttons</p>
                </div>
                
                <h3>Default State (Unselected)</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_enrolment_bg_color">Enrolment Button Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_enrolment_bg_color" 
                                   name="ccs_enrolment_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_enrolment_bg_color', '#f8f9fa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for unselected enrolment buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_enrolment_text_color">Enrolment Button Text</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_enrolment_text_color" 
                                   name="ccs_enrolment_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_enrolment_text_color', '#2c3e50')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color for unselected enrolment buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_enrolment_border_color">Enrolment Button Border</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_enrolment_border_color" 
                                   name="ccs_enrolment_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_enrolment_border_color', '#e1e8ed')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color for unselected enrolment buttons</p>
                        </td>
                    </tr>
                </table>

                <h3>Hover State</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_enrolment_hover_bg_color">Enrolment Hover Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_enrolment_hover_bg_color" 
                                   name="ccs_enrolment_hover_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_enrolment_hover_bg_color', '#e3f2fd')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color when hovering over enrolment buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_enrolment_hover_text_color">Enrolment Hover Text</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_enrolment_hover_text_color" 
                                   name="ccs_enrolment_hover_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_enrolment_hover_text_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color when hovering over enrolment buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_enrolment_hover_border_color">Enrolment Hover Border</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_enrolment_hover_border_color" 
                                   name="ccs_enrolment_hover_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_enrolment_hover_border_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color when hovering over enrolment buttons</p>
                        </td>
                    </tr>
                </table>

                <h3>Active/Selected State</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_enrolment_active_bg_color">Enrolment Active Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_enrolment_active_bg_color" 
                                   name="ccs_enrolment_active_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_enrolment_active_bg_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for selected enrolment button</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_enrolment_active_text_color">Enrolment Active Text</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_enrolment_active_text_color" 
                                   name="ccs_enrolment_active_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_enrolment_active_text_color', '#ffffff')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color for selected enrolment button</p>
                        </td>
                    </tr>
                </table>

                <?php elseif ($active_tab === 'inputs'): ?>
                
                <!-- ALL INPUT FIELDS TAB -->
                <h2>📝 All Input Field Settings</h2>
                <p>Comprehensive styling controls for all input fields in the calculator.</p>
                
                <h3>General Input Styling</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_input_font_size">Input Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_input_font_size" 
                                   name="ccs_input_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_input_font_size', '16')); ?>" 
                                   min="12" 
                                   max="24" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for input fields (default: 16px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_input_bg_color">Input Background Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_input_bg_color" 
                                   name="ccs_input_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_input_bg_color', '#f8f9fa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for input fields</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_input_padding">Input Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_input_padding" 
                                   name="ccs_input_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_input_padding', '12')); ?>" 
                                   min="8" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Inner padding for input fields (default: 12px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_progress_line_color">Progress Line Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_progress_line_color" 
                                   name="ccs_progress_line_color" 
                                   value="<?php echo esc_attr(get_option('ccs_progress_line_color', '#e1e8ed')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for progress line</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_progress_text_color">Progress Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_progress_text_color" 
                                   name="ccs_progress_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_progress_text_color', '#666666')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for step labels and inactive text</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_progress_step_size">Step Circle Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_progress_step_size" 
                                   name="ccs_progress_step_size" 
                                   value="<?php echo esc_attr(get_option('ccs_progress_step_size', 40)); ?>" 
                                   min="30" max="60" step="2">
                            <p class="description">Size of progress step circles in pixels (30-60px)</p>
                        </td>
                    </tr>
                </table>

                <h3>Button Styling</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_padding">Button Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_button_padding" 
                                   name="ccs_button_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_button_padding', '12')); ?>" 
                                   min="8" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Padding inside buttons (default: 12px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_hover_text_color">Button Hover Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_button_hover_text_color" 
                                   name="ccs_button_hover_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_button_hover_text_color', '#ffffff')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color when hovering over buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_border_color">Button Border Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_button_border_color" 
                                   name="ccs_button_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_button_border_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color for buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_button_border_hover_color">Button Border Hover Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_button_border_hover_color" 
                                   name="ccs_button_border_hover_color" 
                                   value="<?php echo esc_attr(get_option('ccs_button_border_hover_color', '#005f8a')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color when hovering over buttons</p>
                        </td>
                    </tr>
                </table>

                <h3>Child Count Buttons (1, 2, 3, 4, 5)</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_count_bg_color">Background Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_child_count_bg_color" 
                                   name="ccs_child_count_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_child_count_bg_color', '#f8f9fa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for child count buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_count_text_color">Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_child_count_text_color" 
                                   name="ccs_child_count_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_child_count_text_color', '#2c3e50')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color for child count buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_count_border_color">Border Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_child_count_border_color" 
                                   name="ccs_child_count_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_child_count_border_color', '#e1e8ed')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color for child count buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_count_hover_bg_color">Hover Background Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_child_count_hover_bg_color" 
                                   name="ccs_child_count_hover_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_child_count_hover_bg_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color when hovering over child count buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_count_hover_text_color">Hover Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_child_count_hover_text_color" 
                                   name="ccs_child_count_hover_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_child_count_hover_text_color', '#ffffff')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color when hovering over child count buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_count_active_bg_color">Active/Selected Background Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_child_count_active_bg_color" 
                                   name="ccs_child_count_active_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_child_count_active_bg_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for selected child count button</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_count_active_text_color">Active/Selected Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_child_count_active_text_color" 
                                   name="ccs_child_count_active_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_child_count_active_text_color', '#ffffff')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color for selected child count button</p>
                        </td>
                    </tr>
                </table>

                <h3>Fortnight Days Buttons</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_fortnight_btn_bg_color">Background Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_fortnight_btn_bg_color" 
                                   name="ccs_fortnight_btn_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_fortnight_btn_bg_color', '#f8f9fa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for fortnight day buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_fortnight_btn_text_color">Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_fortnight_btn_text_color" 
                                   name="ccs_fortnight_btn_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_fortnight_btn_text_color', '#2c3e50')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color for fortnight day buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_fortnight_btn_border_color">Border Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_fortnight_btn_border_color" 
                                   name="ccs_fortnight_btn_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_fortnight_btn_border_color', '#e1e8ed')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color for fortnight day buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_fortnight_btn_active_bg_color">Active Background Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_fortnight_btn_active_bg_color" 
                                   name="ccs_fortnight_btn_active_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_fortnight_btn_active_bg_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for selected fortnight day buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_fortnight_btn_active_text_color">Active Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_fortnight_btn_active_text_color" 
                                   name="ccs_fortnight_btn_active_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_fortnight_btn_active_text_color', '#ffffff')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color for selected fortnight day buttons</p>
                        </td>
                    </tr>
                </table>


                <h2>Child Details Cards</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_card_bg_color">Card Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_card_bg_color" 
                                   name="ccs_card_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_card_bg_color', '#f8f9fa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for child detail cards</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_card_border_color">Card Border Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_card_border_color" 
                                   name="ccs_card_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_card_border_color', '#e8f4f8')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color for child detail cards</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_card_border_radius">Card Border Radius</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_card_border_radius" 
                                   name="ccs_card_border_radius" 
                                   value="<?php echo esc_attr(get_option('ccs_card_border_radius', '12')); ?>" 
                                   min="0" 
                                   max="30" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Roundness of card corners (default: 12px)</p>
                        </td>
                    </tr>
                </table>

                <h2>Summary Section</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_summary_value_color">Summary Value Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_summary_value_color" 
                                   name="ccs_summary_value_color" 
                                   value="<?php echo esc_attr(get_option('ccs_summary_value_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for calculated values in summary</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_summary_label_color">Summary Label Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_summary_label_color" 
                                   name="ccs_summary_label_color" 
                                   value="<?php echo esc_attr(get_option('ccs_summary_label_color', '#2c3e50')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for labels in summary section</p>
                        </td>
                    </tr>
                </table>

                <?php elseif ($active_tab === 'step1'): ?>
                
                <!-- STEP 1 TAB -->
                <h2>1️⃣ Step 1: Care Type Selection</h2>
                <p>Complete styling control for all Step 1 elements: location, suburbs, dropdown, ATIS, enrollment options.</p>
                
                <div style="background:#e8f4f8; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#0073aa;">📍 Location & Suburb Fields</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_step1_location_font_size">Location Field Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step1_location_font_size" 
                                   name="ccs_step1_location_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_step1_location_font_size', '16')); ?>" 
                                   min="12" 
                                   max="24" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for location input field</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step1_location_padding">Location Field Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step1_location_padding" 
                                   name="ccs_step1_location_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_step1_location_padding', '12')); ?>" 
                                   min="8" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Inner padding for location field</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step1_location_width">Location Field Width</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step1_location_width" 
                                   name="ccs_step1_location_width" 
                                   value="<?php echo esc_attr(get_option('ccs_step1_location_width', '100')); ?>" 
                                   min="50" 
                                   max="100" 
                                   style="width: 80px;">
                            <span>%</span>
                            <p class="description">Width of location input field</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step1_location_bg_color">Location Field Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_step1_location_bg_color" 
                                   name="ccs_step1_location_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_step1_location_bg_color', '#ffffff')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for location field</p>
                        </td>
                    </tr>
                </table>

                <div style="background:#f0f8f0; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#00a32a;">📋 Dropdown & Select Fields</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_step1_dropdown_font_size">Dropdown Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step1_dropdown_font_size" 
                                   name="ccs_step1_dropdown_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_step1_dropdown_font_size', '16')); ?>" 
                                   min="12" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for dropdown menus</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step1_dropdown_padding">Dropdown Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step1_dropdown_padding" 
                                   name="ccs_step1_dropdown_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_step1_dropdown_padding', '12')); ?>" 
                                   min="8" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Inner padding for dropdown fields</p>
                        </td>
                    </tr>
                </table>

                <div style="background:#fff8e1; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#d68910;">🔘 ATIS & Enrollment Option Buttons</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_accent_color">Accent Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_accent_color" 
                                   name="ccs_accent_color" 
                                   value="<?php echo esc_attr(get_option('ccs_accent_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Used for links, highlights, and special elements</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_border_color">Border Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_border_color" 
                                   name="ccs_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_border_color', '#e1e8ed')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">General border color for dividers and separators</p>
                        </td>
                    </tr>
                </table>

                <?php elseif ($active_tab === 'step2'): ?>
                
                <!-- STEP 2 TAB -->
                <h2>2️⃣ Step 2: Income & Activity Hours</h2>
                <p>Complete styling control for income and activity hours input fields with detailed customization options.</p>
                
                <div style="background:#e8f5e8; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#00a32a;">💰 Annual Income Field</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_step2_income_font_size">Income Field Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step2_income_font_size" 
                                   name="ccs_step2_income_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_step2_income_font_size', '18')); ?>" 
                                   min="14" 
                                   max="24" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for income input field (larger for importance)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step2_income_padding">Income Field Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step2_income_padding" 
                                   name="ccs_step2_income_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_step2_income_padding', '15')); ?>" 
                                   min="10" 
                                   max="25" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Inner padding for income field</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step2_income_width">Income Field Width</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step2_income_width" 
                                   name="ccs_step2_income_width" 
                                   value="<?php echo esc_attr(get_option('ccs_step2_income_width', '48')); ?>" 
                                   min="30" 
                                   max="100" 
                                   style="width: 80px;">
                            <span>%</span>
                            <p class="description">Width of income field (48% for side-by-side layout)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step2_income_bg_color">Income Field Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_step2_income_bg_color" 
                                   name="ccs_step2_income_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_step2_income_bg_color', '#f8fff8')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for income field</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step2_income_border_color">Income Field Border</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_step2_income_border_color" 
                                   name="ccs_step2_income_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_step2_income_border_color', '#00a32a')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color for income field</p>
                        </td>
                    </tr>
                </table>

                <div style="background:#e8f4f8; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#0073aa;">⏰ Activity Hours Field</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_step2_activity_font_size">Activity Hours Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step2_activity_font_size" 
                                   name="ccs_step2_activity_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_step2_activity_font_size', '18')); ?>" 
                                   min="14" 
                                   max="24" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for activity hours input field</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step2_activity_padding">Activity Hours Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step2_activity_padding" 
                                   name="ccs_step2_activity_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_step2_activity_padding', '15')); ?>" 
                                   min="10" 
                                   max="25" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Inner padding for activity hours field</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step2_activity_width">Activity Hours Width</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step2_activity_width" 
                                   name="ccs_step2_activity_width" 
                                   value="<?php echo esc_attr(get_option('ccs_step2_activity_width', '48')); ?>" 
                                   min="30" 
                                   max="100" 
                                   style="width: 80px;">
                            <span>%</span>
                            <p class="description">Width of activity hours field (48% for side-by-side layout)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step2_activity_bg_color">Activity Hours Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_step2_activity_bg_color" 
                                   name="ccs_step2_activity_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_step2_activity_bg_color', '#f8f9ff')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for activity hours field</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step2_activity_border_color">Activity Hours Border</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_step2_activity_border_color" 
                                   name="ccs_step2_activity_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_step2_activity_border_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color for activity hours field</p>
                        </td>
                    </tr>
                </table>

                <?php elseif ($active_tab === 'step3'): ?>
                
                <!-- STEP 3 TAB -->
                <h2>3️⃣ Step 3: Children Details</h2>
                <p>Complete styling control for all child-related fields: count buttons, DOB, fortnight days, weekdays, sliders, outputs, and fees.</p>
                
                <div style="background:#fff3e0; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#f57c00;">👶 Child Count Buttons (1, 2, 3, 4, 5)</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_step3_child_count_font_size">Child Count Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step3_child_count_font_size" 
                                   name="ccs_step3_child_count_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_step3_child_count_font_size', '18')); ?>" 
                                   min="14" 
                                   max="24" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for child count buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step3_child_count_padding">Child Count Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step3_child_count_padding" 
                                   name="ccs_step3_child_count_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_step3_child_count_padding', '12')); ?>" 
                                   min="8" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Inner padding for child count buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step3_child_count_width">Child Count Button Width</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step3_child_count_width" 
                                   name="ccs_step3_child_count_width" 
                                   value="<?php echo esc_attr(get_option('ccs_step3_child_count_width', '50')); ?>" 
                                   min="40" 
                                   max="80" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Width of each child count button</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step3_child_count_margin">Child Count Margin</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step3_child_count_margin" 
                                   name="ccs_step3_child_count_margin" 
                                   value="<?php echo esc_attr(get_option('ccs_step3_child_count_margin', '5')); ?>" 
                                   min="2" 
                                   max="15" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Space between child count buttons</p>
                        </td>
                    </tr>
                </table>

                <div style="background:#e8f5e8; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#2e7d32;">📅 Date of Birth Fields</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_step3_dob_font_size">DOB Field Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step3_dob_font_size" 
                                   name="ccs_step3_dob_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_step3_dob_font_size', '16')); ?>" 
                                   min="12" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for date of birth input fields</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step3_dob_padding">DOB Field Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step3_dob_padding" 
                                   name="ccs_step3_dob_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_step3_dob_padding', '12')); ?>" 
                                   min="8" 
                                   max="18" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Inner padding for DOB fields</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step3_dob_width">DOB Field Width</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step3_dob_width" 
                                   name="ccs_step3_dob_width" 
                                   value="<?php echo esc_attr(get_option('ccs_step3_dob_width', '100')); ?>" 
                                   min="60" 
                                   max="100" 
                                   style="width: 80px;">
                            <span>%</span>
                            <p class="description">Width of date of birth fields</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step3_dob_bg_color">DOB Field Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_step3_dob_bg_color" 
                                   name="ccs_step3_dob_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_step3_dob_bg_color', '#f8fff8')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for DOB fields</p>
                        </td>
                    </tr>
                </table>

                <!-- Child Count Button Hover Effects -->
                <div style="background:#fff3e0; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#f57c00;">🔘 Child Count Button Hover & Active States</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_count_bg_color">Child Count Button Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_child_count_bg_color" 
                                   name="ccs_child_count_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_child_count_bg_color', '#f8f9fa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for child count buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_count_text_color">Child Count Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_child_count_text_color" 
                                   name="ccs_child_count_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_child_count_text_color', '#2c3e50')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color for child count buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_count_border_color">Child Count Border Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_child_count_border_color" 
                                   name="ccs_child_count_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_child_count_border_color', '#e1e8ed')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color for child count buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_count_hover_bg_color">Child Count Hover Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_child_count_hover_bg_color" 
                                   name="ccs_child_count_hover_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_child_count_hover_bg_color', '#e3f2fd')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color when hovering over child count buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_count_hover_text_color">Child Count Hover Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_child_count_hover_text_color" 
                                   name="ccs_child_count_hover_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_child_count_hover_text_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color when hovering over child count buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_count_hover_border_color">Child Count Hover Border Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_child_count_hover_border_color" 
                                   name="ccs_child_count_hover_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_child_count_hover_border_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color when hovering over child count buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_count_active_bg_color">Active Child Count Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_child_count_active_bg_color" 
                                   name="ccs_child_count_active_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_child_count_active_bg_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for selected child count button</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_count_active_text_color">Active Child Count Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_child_count_active_text_color" 
                                   name="ccs_child_count_active_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_child_count_active_text_color', '#ffffff')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color for selected child count button</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_count_border_radius">Child Count Border Radius</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_child_count_border_radius" 
                                   name="ccs_child_count_border_radius" 
                                   value="<?php echo esc_attr(get_option('ccs_child_count_border_radius', '8')); ?>" 
                                   min="0" 
                                   max="25" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Roundness of child count button corners</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_count_font_family">Child Count Font Family</label>
                        </th>
                        <td>
                            <select id="ccs_child_count_font_family" name="ccs_child_count_font_family" style="width: 300px;">
                                <?php
                                $child_count_font = get_option('ccs_child_count_font_family', 'inherit');
                                $fonts = [
                                    'inherit' => 'Inherit from General Settings',
                                    'Red Hat Display' => 'Red Hat Display',
                                    'Barlow' => 'Barlow',
                                    'Quicksand' => 'Quicksand',
                                    'IBM Plex Sans' => 'IBM Plex Sans',
                                    'Inter' => 'Inter',
                                    'Poppins' => 'Poppins',
                                    'Roboto' => 'Roboto',
                                    'Open Sans' => 'Open Sans',
                                    'Lato' => 'Lato',
                                    'Montserrat' => 'Montserrat'
                                ];
                                foreach ($fonts as $name => $value) {
                                    $selected = ($child_count_font === $value) ? 'selected' : '';
                                    echo "<option value=\"$value\" $selected>$name</option>";
                                }
                                ?>
                            </select>
                            <p class="description">Font family for child count buttons</p>
                        </td>
                    </tr>
                </table>
                
                <!-- Comprehensive Fortnight Day Button Settings -->
                <div style="background:#e3f2fd; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#1976d2;">📋 Fortnight Day Buttons (Mon, Tue, Wed, Thu, Fri)</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_fortnight_btn_font_size">Fortnight Button Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_fortnight_btn_font_size" 
                                   name="ccs_fortnight_btn_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_fortnight_btn_font_size', '14')); ?>" 
                                   min="10" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for fortnight day buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_fortnight_btn_bg_color">Fortnight Button Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_fortnight_btn_bg_color" 
                                   name="ccs_fortnight_btn_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_fortnight_btn_bg_color', '#f8f9fa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for fortnight day buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_fortnight_btn_text_color">Fortnight Button Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_fortnight_btn_text_color" 
                                   name="ccs_fortnight_btn_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_fortnight_btn_text_color', '#2c3e50')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color for fortnight day buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_fortnight_btn_border_color">Fortnight Button Border Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_fortnight_btn_border_color" 
                                   name="ccs_fortnight_btn_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_fortnight_btn_border_color', '#e1e8ed')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color for fortnight day buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_fortnight_btn_hover_bg_color">Fortnight Button Hover Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_fortnight_btn_hover_bg_color" 
                                   name="ccs_fortnight_btn_hover_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_fortnight_btn_hover_bg_color', '#e3f2fd')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color when hovering over fortnight buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_fortnight_btn_hover_text_color">Fortnight Button Hover Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_fortnight_btn_hover_text_color" 
                                   name="ccs_fortnight_btn_hover_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_fortnight_btn_hover_text_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color when hovering over fortnight buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_fortnight_btn_hover_border_color">Fortnight Button Hover Border Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_fortnight_btn_hover_border_color" 
                                   name="ccs_fortnight_btn_hover_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_fortnight_btn_hover_border_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color when hovering over fortnight buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_fortnight_btn_active_bg_color">Active Fortnight Button Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_fortnight_btn_active_bg_color" 
                                   name="ccs_fortnight_btn_active_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_fortnight_btn_active_bg_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for selected fortnight day buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_fortnight_btn_active_text_color">Active Fortnight Button Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_fortnight_btn_active_text_color" 
                                   name="ccs_fortnight_btn_active_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_fortnight_btn_active_text_color', '#ffffff')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color for selected fortnight day buttons</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_fortnight_btn_border_radius">Fortnight Button Border Radius</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_fortnight_btn_border_radius" 
                                   name="ccs_fortnight_btn_border_radius" 
                                   value="<?php echo esc_attr(get_option('ccs_fortnight_btn_border_radius', '6')); ?>" 
                                   min="0" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Roundness of fortnight button corners</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_fortnight_btn_font_family">Fortnight Button Font Family</label>
                        </th>
                        <td>
                            <select id="ccs_fortnight_btn_font_family" name="ccs_fortnight_btn_font_family" style="width: 300px;">
                                <?php
                                $fortnight_font = get_option('ccs_fortnight_btn_font_family', 'inherit');
                                $fonts = [
                                    'inherit' => 'Inherit from General Settings',
                                    'Red Hat Display' => 'Red Hat Display',
                                    'Barlow' => 'Barlow',
                                    'Quicksand' => 'Quicksand',
                                    'IBM Plex Sans' => 'IBM Plex Sans',
                                    'Inter' => 'Inter',
                                    'Poppins' => 'Poppins',
                                    'Roboto' => 'Roboto',
                                    'Open Sans' => 'Open Sans',
                                    'Lato' => 'Lato',
                                    'Montserrat' => 'Montserrat'
                                ];
                                foreach ($fonts as $name => $value) {
                                    $selected = ($fortnight_font === $value) ? 'selected' : '';
                                    echo "<option value=\"$value\" $selected>$name</option>";
                                }
                                ?>
                            </select>
                            <p class="description">Font family for fortnight day buttons</p>
                        </td>
                    </tr>
                </table>

                <!-- Hours per Day Slider Settings -->
                <div style="background:#f3e5f5; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#7b1fa2;">🎚️ Hours per Day Slider</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_slider_font_size">Slider Label Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_slider_font_size" 
                                   name="ccs_slider_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_slider_font_size', '14')); ?>" 
                                   min="10" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for slider labels and values</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_slider_border_color">Slider Container Border Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_slider_border_color" 
                                   name="ccs_slider_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_slider_border_color', '#e1e8ed')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color for slider container</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_slider_hover_color">Slider Hover Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_slider_hover_color" 
                                   name="ccs_slider_hover_color" 
                                   value="<?php echo esc_attr(get_option('ccs_slider_hover_color', '#005f8a')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color when hovering over slider thumb</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_slider_font_family">Slider Font Family</label>
                        </th>
                        <td>
                            <select id="ccs_slider_font_family" name="ccs_slider_font_family" style="width: 300px;">
                                <?php
                                $slider_font = get_option('ccs_slider_font_family', 'inherit');
                                $fonts = [
                                    'inherit' => 'Inherit from General Settings',
                                    'Red Hat Display' => 'Red Hat Display',
                                    'Barlow' => 'Barlow',
                                    'Quicksand' => 'Quicksand',
                                    'IBM Plex Sans' => 'IBM Plex Sans',
                                    'Inter' => 'Inter',
                                    'Poppins' => 'Poppins',
                                    'Roboto' => 'Roboto',
                                    'Open Sans' => 'Open Sans',
                                    'Lato' => 'Lato',
                                    'Montserrat' => 'Montserrat'
                                ];
                                foreach ($fonts as $name => $value) {
                                    $selected = ($slider_font === $value) ? 'selected' : '';
                                    echo "<option value=\"$value\" $selected>$name</option>";
                                }
                                ?>
                            </select>
                            <p class="description">Font family for slider labels and values</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_slider_border_radius">Slider Container Border Radius</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_slider_border_radius" 
                                   name="ccs_slider_border_radius" 
                                   value="<?php echo esc_attr(get_option('ccs_slider_border_radius', '8')); ?>" 
                                   min="0" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Roundness of slider container corners</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_slider_track_height">Slider Track Height</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_slider_track_height" 
                                   name="ccs_slider_track_height" 
                                   value="<?php echo esc_attr(get_option('ccs_slider_track_height', '6')); ?>" 
                                   min="3" 
                                   max="12" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Height of the slider track</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_slider_thumb_size">Slider Thumb Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_slider_thumb_size" 
                                   name="ccs_slider_thumb_size" 
                                   value="<?php echo esc_attr(get_option('ccs_slider_thumb_size', '20')); ?>" 
                                   min="15" 
                                   max="30" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Size of the slider thumb/handle</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_slider_track_color">Slider Track Background Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_slider_track_color" 
                                   name="ccs_slider_track_color" 
                                   value="<?php echo esc_attr(get_option('ccs_slider_track_color', '#e1e8ed')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for slider track</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_slider_completed_color">Slider Progress Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_slider_completed_color" 
                                   name="ccs_slider_completed_color" 
                                   value="<?php echo esc_attr(get_option('ccs_slider_completed_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for completed/filled portion of slider</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_slider_thumb_color">Slider Thumb Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_slider_thumb_color" 
                                   name="ccs_slider_thumb_color" 
                                   value="<?php echo esc_attr(get_option('ccs_slider_thumb_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for slider thumb/handle</p>
                        </td>
                    </tr>
                </table>

                <?php elseif ($active_tab === 'step4'): ?>
                
                <!-- STEP 4 TAB -->
                <h2>4️⃣ Step 4: Results & Summary</h2>
                <p>Complete styling control for all summary elements: cards, child information, totals, detailed summaries, and action buttons.</p>
                
                <div style="background:#f3e5f5; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#7b1fa2;">📊 Summary Cards & Layout</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_step4_card_font_size">Summary Card Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step4_card_font_size" 
                                   name="ccs_step4_card_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_step4_card_font_size', '16')); ?>" 
                                   min="12" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for summary card text</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step4_card_padding">Summary Card Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step4_card_padding" 
                                   name="ccs_step4_card_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_step4_card_padding', '20')); ?>" 
                                   min="15" 
                                   max="30" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Inner padding for summary cards</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step4_card_margin">Summary Card Margin</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step4_card_margin" 
                                   name="ccs_step4_card_margin" 
                                   value="<?php echo esc_attr(get_option('ccs_step4_card_margin', '15')); ?>" 
                                   min="10" 
                                   max="25" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Space between summary cards</p>
                        </td>
                    </tr>
                </table>

                <div style="background:#e8f5e8; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#2e7d32;">👶 Child Information Display</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_step4_child_info_font_size">Child Info Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step4_child_info_font_size" 
                                   name="ccs_step4_child_info_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_step4_child_info_font_size', '14')); ?>" 
                                   min="12" 
                                   max="18" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for child information text</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step4_child_info_bg_color">Child Info Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_step4_child_info_bg_color" 
                                   name="ccs_step4_child_info_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_step4_child_info_bg_color', '#f8fff8')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for child information sections</p>
                        </td>
                    </tr>
                </table>

                <div style="background:#fff3e0; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#f57c00;">💰 Total & Value Display</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_step4_total_font_size">Total Amount Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_step4_total_font_size" 
                                   name="ccs_step4_total_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_step4_total_font_size', '24')); ?>" 
                                   min="18" 
                                   max="32" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for total amounts (larger for emphasis)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_step4_total_font_weight">Total Amount Font Weight</label>
                        </th>
                        <td>
                            <select id="ccs_step4_total_font_weight" name="ccs_step4_total_font_weight" style="width: 200px;">
                                <?php
                                $total_weight = get_option('ccs_step4_total_font_weight', '700');
                                ?>
                                <option value="600" <?php selected($total_weight, '600'); ?>>Semi-Bold (600)</option>
                                <option value="700" <?php selected($total_weight, '700'); ?>>Bold (700)</option>
                                <option value="800" <?php selected($total_weight, '800'); ?>>Extra Bold (800)</option>
                            </select>
                            <p class="description">Font weight for total amounts</p>
                        </td>
                    </tr>
                </table>

                <div style="background:#fff3e0; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#f57c00;">💰 Summary Row Colors & Styling</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_summary_total_fee_color">Total Fees Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_summary_total_fee_color" 
                                   name="ccs_summary_total_fee_color" 
                                   value="<?php echo esc_attr(get_option('ccs_summary_total_fee_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for total fees amount</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_summary_subsidy_color">Estimated Subsidy Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_summary_subsidy_color" 
                                   name="ccs_summary_subsidy_color" 
                                   value="<?php echo esc_attr(get_option('ccs_summary_subsidy_color', '#6B46C1')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for estimated subsidy amount</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_summary_out_pocket_color">Out of Pocket Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_summary_out_pocket_color" 
                                   name="ccs_summary_out_pocket_color" 
                                   value="<?php echo esc_attr(get_option('ccs_summary_out_pocket_color', '#4ECDC4')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for out of pocket costs amount</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_summary_label_text_color">Summary Labels Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_summary_label_text_color" 
                                   name="ccs_summary_label_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_summary_label_text_color', '#333333')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for summary row labels (Total fees, Est. subsidy, etc.)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_summary_row_padding">Summary Row Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_summary_row_padding" 
                                   name="ccs_summary_row_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_summary_row_padding', '10')); ?>" 
                                   min="5" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Padding for summary rows (default: 10px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_summary_row_bg_color">Summary Row Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_summary_row_bg_color" 
                                   name="ccs_summary_row_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_summary_row_bg_color', '#f5f5f5')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for alternating summary rows</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_summary_border_color">Summary Border Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_summary_border_color" 
                                   name="ccs_summary_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_summary_border_color', '#f5f5f5')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color for summary sections</p>
                        </td>
                    </tr>
                </table>

                <div style="background:#e8f5e9; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#2e7d32;">📊 Summary Page Specific Colors</h4>
                    <p style="margin-bottom:0;">Control colors for child cards, week breakdowns, and detailed summary sections</p>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_summary_heading_color">Child Card Heading Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_summary_heading_color" 
                                   name="ccs_summary_heading_color" 
                                   value="<?php echo esc_attr(get_option('ccs_summary_heading_color', '#84bd00')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for "Child 1", "Child 2" headings (default: green #84bd00)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_total_fee_color">Total Fee Value Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_total_fee_color" 
                                   name="ccs_total_fee_color" 
                                   value="<?php echo esc_attr(get_option('ccs_total_fee_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for total fee amounts in week breakdown (default: blue #0073aa)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_subsidy_color">Subsidy Value Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_subsidy_color" 
                                   name="ccs_subsidy_color" 
                                   value="<?php echo esc_attr(get_option('ccs_subsidy_color', '#6b46c1')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for estimated subsidy amounts (default: purple #6b46c1)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_out_of_pocket_color">Out-of-Pocket Value Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_out_of_pocket_color" 
                                   name="ccs_out_of_pocket_color" 
                                   value="<?php echo esc_attr(get_option('ccs_out_of_pocket_color', '#00bcd4')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for out-of-pocket amounts (default: cyan #00bcd4)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_week_heading_color">Week Heading Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_week_heading_color" 
                                   name="ccs_week_heading_color" 
                                   value="<?php echo esc_attr(get_option('ccs_week_heading_color', '#333333')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for "Week 1" and "Week 2" headings (default: dark gray #333333)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_fee_bg_color">Fee Section Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_fee_bg_color" 
                                   name="ccs_fee_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_fee_bg_color', 'rgba(0, 115, 170, 0.1)')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for total fee sections (default: light blue rgba(0, 115, 170, 0.1))</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_subsidy_bg_color">Subsidy Section Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_subsidy_bg_color" 
                                   name="ccs_subsidy_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_subsidy_bg_color', 'rgba(107, 70, 193, 0.1)')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for subsidy sections (default: light purple rgba(107, 70, 193, 0.1))</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_out_of_pocket_bg_color">Out-of-Pocket Section Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_out_of_pocket_bg_color" 
                                   name="ccs_out_of_pocket_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_out_of_pocket_bg_color', 'rgba(0, 188, 212, 0.1)')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for out-of-pocket sections (default: light cyan rgba(0, 188, 212, 0.1))</p>
                        </td>
                    </tr>
                </table>

                <div style="background:#f3e5f5; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#7b1fa2;">🔘 Summary Period Buttons</h4>
                    <p style="margin-bottom:0;">Styling for "Fortnightly", "Weekly", "Monthly", "Yearly" buttons</p>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_period_btn_bg_color">Period Button Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_period_btn_bg_color" 
                                   name="ccs_period_btn_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_period_btn_bg_color', '#84bd00')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for period buttons (default: green #84bd00)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_period_btn_text_color">Period Button Text</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_period_btn_text_color" 
                                   name="ccs_period_btn_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_period_btn_text_color', '#ffffff')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color for period buttons (default: white #ffffff)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_period_btn_hover_bg_color">Period Button Hover Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_period_btn_hover_bg_color" 
                                   name="ccs_period_btn_hover_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_period_btn_hover_bg_color', '#6fa000')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color when hovering (default: darker green #6fa000)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_period_btn_hover_text_color">Period Button Hover Text</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_period_btn_hover_text_color" 
                                   name="ccs_period_btn_hover_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_period_btn_hover_text_color', '#ffffff')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color when hovering (default: white #ffffff)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_period_btn_active_bg_color">Period Button Active Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_period_btn_active_bg_color" 
                                   name="ccs_period_btn_active_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_period_btn_active_bg_color', '#5a8500')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for selected/active button (default: darkest green #5a8500)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_period_btn_active_text_color">Period Button Active Text</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_period_btn_active_text_color" 
                                   name="ccs_period_btn_active_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_period_btn_active_text_color', '#ffffff')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color for selected/active button (default: white #ffffff)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_period_btn_border_radius">Period Button Border Radius</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_period_btn_border_radius" 
                                   name="ccs_period_btn_border_radius" 
                                   value="<?php echo esc_attr(get_option('ccs_period_btn_border_radius', '8')); ?>" 
                                   min="0" 
                                   max="50" 
                                   style="width: 80px;">
                            <span class="description">px (default: 8px)</span>
                        </td>
                    </tr>
                </table>

                <div style="background:#fff3e0; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#e65100;">💡 Info Box (Above Email Form)</h4>
                    <p style="margin-bottom:0;">Informational message with lightbulb icon shown above the email form</p>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_info_box_enabled">Enable Info Box</label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" 
                                       id="ccs_info_box_enabled" 
                                       name="ccs_info_box_enabled" 
                                       value="1" 
                                       <?php checked(get_option('ccs_info_box_enabled', 1), 1); ?>>
                                Show info box on summary page
                            </label>
                            <p class="description">Toggle to show/hide the informational message box</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_info_box_text">Info Box Text</label>
                        </th>
                        <td>
                            <textarea id="ccs_info_box_text" 
                                      name="ccs_info_box_text" 
                                      rows="4" 
                                      style="width: 100%; max-width: 600px;"><?php echo esc_textarea(get_option('ccs_info_box_text', 'From January 2026, all families who are eligible for CCS can attend a minimum of 3 days per week (or 72 hours per fortnight) of subsidised care, regardless of their activity level.')); ?></textarea>
                            <p class="description">Message text displayed in the info box. HTML allowed.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_info_box_icon_image">Custom Icon Image</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_info_box_icon_image" 
                                   name="ccs_info_box_icon_image" 
                                   value="<?php echo esc_attr(get_option('ccs_info_box_icon_image', '')); ?>" 
                                   style="width: 400px;">
                            <button type="button" class="button ccs-upload-image-btn" data-target="ccs_info_box_icon_image">Upload Image</button>
                            <button type="button" class="button ccs-remove-image-btn" data-target="ccs_info_box_icon_image">Remove</button>
                            <p class="description">Upload a custom icon image (recommended: 60x60px). Leave empty to use default SVG lightbulb.</p>
                            <?php if(get_option('ccs_info_box_icon_image')): ?>
                            <div style="margin-top:10px;">
                                <img src="<?php echo esc_url(get_option('ccs_info_box_icon_image')); ?>" style="max-width:60px; height:auto; border:1px solid #ddd; padding:5px;">
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_info_box_icon_color">Lightbulb Icon Color (SVG only)</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_info_box_icon_color" 
                                   name="ccs_info_box_icon_color" 
                                   value="<?php echo esc_attr(get_option('ccs_info_box_icon_color', '#f7b731')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for the default SVG lightbulb icon (only applies if no custom image is uploaded)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_info_box_text_color">Info Box Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_info_box_text_color" 
                                   name="ccs_info_box_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_info_box_text_color', '#333333')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color for the info message (default: dark gray #333333)</p>
                        </td>
                    </tr>
                </table>

                <div style="background:#e3f2fd; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#1565c0;">📝 Custom Form Settings</h4>
                    <p style="margin-bottom:0;">Privacy policy and checkbox text for the custom email form</p>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_privacy_policy_url">Privacy Policy URL</label>
                        </th>
                        <td>
                            <input type="url" 
                                   id="ccs_privacy_policy_url" 
                                   name="ccs_privacy_policy_url" 
                                   value="<?php echo esc_attr(get_option('ccs_privacy_policy_url', '#')); ?>" 
                                   style="width: 100%; max-width: 500px;">
                            <p class="description">URL for the Privacy Policy link (e.g., https://yoursite.com/privacy-policy)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_privacy_policy_text">Privacy Policy Checkbox Text</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_privacy_policy_text" 
                                   name="ccs_privacy_policy_text" 
                                   value="<?php echo esc_attr(get_option('ccs_privacy_policy_text', "I agree to Goodstart's Privacy Policy*")); ?>" 
                                   style="width: 100%; max-width: 500px;">
                            <p class="description">Text for the privacy policy checkbox. "Privacy Policy" will be automatically linked.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_contact_checkbox_text">Contact Checkbox Text</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_contact_checkbox_text" 
                                   name="ccs_contact_checkbox_text" 
                                   value="<?php echo esc_attr(get_option('ccs_contact_checkbox_text', 'I would like to be contacted to find out more about potential savings.')); ?>" 
                                   style="width: 100%; max-width: 500px;">
                            <p class="description">Text for the optional contact checkbox</p>
                        </td>
                    </tr>
                </table>

                <div style="background:#fff8e1; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#f57f17;">📧 Email Form Toggle Section</h4>
                    <p style="margin-bottom:0;">Collapsible "Email me my results" section styling and text</p>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_email_toggle_title">Toggle Title</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_email_toggle_title" 
                                   name="ccs_email_toggle_title" 
                                   value="<?php echo esc_attr(get_option('ccs_email_toggle_title', 'Email me my results')); ?>" 
                                   style="width: 400px;">
                            <p class="description">Main heading text for the toggle (default: "Email me my results")</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_email_toggle_subtitle">Toggle Subtitle (Optional)</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_email_toggle_subtitle" 
                                   name="ccs_email_toggle_subtitle" 
                                   value="<?php echo esc_attr(get_option('ccs_email_toggle_subtitle', '')); ?>" 
                                   style="width: 400px;">
                            <p class="description">Optional subtitle text shown when form is expanded</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_email_toggle_icon_image">Custom Email Icon Image</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_email_toggle_icon_image" 
                                   name="ccs_email_toggle_icon_image" 
                                   value="<?php echo esc_attr(get_option('ccs_email_toggle_icon_image', '')); ?>" 
                                   style="width: 400px;">
                            <button type="button" class="button ccs-upload-image-btn" data-target="ccs_email_toggle_icon_image">Upload Image</button>
                            <button type="button" class="button ccs-remove-image-btn" data-target="ccs_email_toggle_icon_image">Remove</button>
                            <p class="description">Upload a custom email icon image (recommended: 24x24px). Leave empty to use default SVG icon.</p>
                            <?php if(get_option('ccs_email_toggle_icon_image')): ?>
                            <div style="margin-top:10px;">
                                <img src="<?php echo esc_url(get_option('ccs_email_toggle_icon_image')); ?>" style="max-width:24px; height:auto; border:1px solid #ddd; padding:5px;">
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_email_toggle_bg_color">Toggle Header Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_email_toggle_bg_color" 
                                   name="ccs_email_toggle_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_email_toggle_bg_color', '#d9d9d9')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for the toggle header (default: light gray #d9d9d9)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_email_toggle_text_color">Toggle Text Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_email_toggle_text_color" 
                                   name="ccs_email_toggle_text_color" 
                                   value="<?php echo esc_attr(get_option('ccs_email_toggle_text_color', '#333333')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Text color for the toggle header (default: dark gray #333333)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_email_toggle_icon_color">Email Icon Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_email_toggle_icon_color" 
                                   name="ccs_email_toggle_icon_color" 
                                   value="<?php echo esc_attr(get_option('ccs_email_toggle_icon_color', '#f7941d')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for the email icon (✉) (default: orange #f7941d)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_email_form_bg_color">Form Content Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_email_form_bg_color" 
                                   name="ccs_email_form_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_email_form_bg_color', '#f9f9f9')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for the form content area (default: light gray #f9f9f9)</p>
                        </td>
                    </tr>
                </table>

                <div style="background:#e8f5e8; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#00a32a;">📊 Weekly Breakdown Styling</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_weekly_header_color">Week Header Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_weekly_header_color" 
                                   name="ccs_weekly_header_color" 
                                   value="<?php echo esc_attr(get_option('ccs_weekly_header_color', '#333333')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for "Week 1" and "Week 2" headers</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_weekly_header_font_size">Week Header Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_weekly_header_font_size" 
                                   name="ccs_weekly_header_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_weekly_header_font_size', '16')); ?>" 
                                   min="14" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for week headers</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_weekly_text_font_size">Weekly Text Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_weekly_text_font_size" 
                                   name="ccs_weekly_text_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_weekly_text_font_size', '16')); ?>" 
                                   min="12" 
                                   max="18" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for weekly breakdown text</p>
                        </td>
                    </tr>
                </table>

                <div style="background:#fce4ec; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#c2185b;">👶 Child Detail Cards</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_card_bg_color">Child Card Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_child_card_bg_color" 
                                   name="ccs_child_card_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_child_card_bg_color', '#ffffff')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for child detail cards</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_card_border_color">Child Card Border Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_child_card_border_color" 
                                   name="ccs_child_card_border_color" 
                                   value="<?php echo esc_attr(get_option('ccs_child_card_border_color', '#f5f5f5')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Border color for child detail cards</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_card_border_radius">Child Card Border Radius</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_child_card_border_radius" 
                                   name="ccs_child_card_border_radius" 
                                   value="<?php echo esc_attr(get_option('ccs_child_card_border_radius', '5')); ?>" 
                                   min="0" 
                                   max="15" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Border radius for child detail cards</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_card_padding">Child Card Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_child_card_padding" 
                                   name="ccs_child_card_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_child_card_padding', '20')); ?>" 
                                   min="15" 
                                   max="30" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Padding inside child detail cards</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_card_text_font_size">Child Card Text Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_child_card_text_font_size" 
                                   name="ccs_child_card_text_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_child_card_text_font_size', '16')); ?>" 
                                   min="12" 
                                   max="18" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for child card text</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_card_header_color">Child Card Header Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_child_card_header_color" 
                                   name="ccs_child_card_header_color" 
                                   value="<?php echo esc_attr(get_option('ccs_child_card_header_color', '#333333')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for child card headers (Child 1, Child 2, etc.)</p>
                        </td>
                    </tr>
                </table>

                <div style="background:#e3f2fd; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#1976d2;">🔘 Period Selection Buttons</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_period_btn_font_size">Period Button Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_period_btn_font_size" 
                                   name="ccs_period_btn_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_period_btn_font_size', '14')); ?>" 
                                   min="12" 
                                   max="18" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for period buttons (Fortnightly, Weekly, etc.)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_child_select_font_size">Child Select Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_child_select_font_size" 
                                   name="ccs_child_select_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_child_select_font_size', '16')); ?>" 
                                   min="12" 
                                   max="18" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Font size for child select dropdown and label</p>
                        </td>
                    </tr>
                </table>

                <div style="background:#e3f2fd; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#1976d2;">🔘 Action Buttons (Show Total, Get Summary)</h4>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_card_bg_color">Summary Card Background</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_card_bg_color" 
                                   name="ccs_card_bg_color" 
                                   value="<?php echo esc_attr(get_option('ccs_card_bg_color', '#f8f9fa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Background color for summary cards</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_summary_value_color">Summary Value Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_summary_value_color" 
                                   name="ccs_summary_value_color" 
                                   value="<?php echo esc_attr(get_option('ccs_summary_value_color', '#0073aa')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color for dollar amounts and values in summary</p>
                        </td>
                    </tr>
                </table>

                <?php elseif ($active_tab === 'mobile'): ?>
                
                <!-- MOBILE & RESPONSIVE TAB -->
                <h2>📱 Mobile & Responsive Design</h2>
                <p>Control how your calculator appears on mobile devices and different screen sizes.</p>

                <h3>Mobile Typography</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_mobile_heading_size">Mobile Heading Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_mobile_heading_size" 
                                   name="ccs_mobile_heading_size" 
                                   value="<?php echo esc_attr(get_option('ccs_mobile_heading_size', '24')); ?>" 
                                   min="18" 
                                   max="36" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Heading size on mobile devices (default: 24px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_mobile_input_font_size">Mobile Input Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_mobile_input_font_size" 
                                   name="ccs_mobile_input_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_mobile_input_font_size', '16')); ?>" 
                                   min="14" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Input field font size on mobile (default: 16px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_mobile_button_font_size">Mobile Button Font Size</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_mobile_button_font_size" 
                                   name="ccs_mobile_button_font_size" 
                                   value="<?php echo esc_attr(get_option('ccs_mobile_button_font_size', '14')); ?>" 
                                   min="12" 
                                   max="18" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Button font size on mobile (default: 14px)</p>
                        </td>
                    </tr>
                </table>

                <h3>Mobile Spacing & Layout</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_mobile_container_padding">Mobile Container Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_mobile_container_padding" 
                                   name="ccs_mobile_container_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_mobile_container_padding', '20')); ?>" 
                                   min="10" 
                                   max="40" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Container padding on mobile devices (default: 20px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_mobile_input_padding">Mobile Input Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_mobile_input_padding" 
                                   name="ccs_mobile_input_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_mobile_input_padding', '14')); ?>" 
                                   min="10" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Input field padding on mobile (default: 14px)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_mobile_button_padding">Mobile Button Padding</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_mobile_button_padding" 
                                   name="ccs_mobile_button_padding" 
                                   value="<?php echo esc_attr(get_option('ccs_mobile_button_padding', '14')); ?>" 
                                   min="10" 
                                   max="20" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Button padding on mobile devices (default: 14px)</p>
                        </td>
                    </tr>
                </table>

                <h3>Mobile Button & Input Sizing</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_mobile_button_width">Mobile Button Width</label>
                        </th>
                        <td>
                            <select id="ccs_mobile_button_width" name="ccs_mobile_button_width" style="width: 200px;">
                                <?php
                                $mobile_button_width = get_option('ccs_mobile_button_width', 'full');
                                ?>
                                <option value="auto" <?php selected($mobile_button_width, 'auto'); ?>>Auto Width</option>
                                <option value="full" <?php selected($mobile_button_width, 'full'); ?>>Full Width (100%)</option>
                                <option value="half" <?php selected($mobile_button_width, 'half'); ?>>Half Width (50%)</option>
                            </select>
                            <p class="description">Button width behavior on mobile devices</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_mobile_input_width">Mobile Input Width</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_mobile_input_width" 
                                   name="ccs_mobile_input_width" 
                                   value="<?php echo esc_attr(get_option('ccs_mobile_input_width', '100')); ?>" 
                                   min="80" 
                                   max="100" 
                                   style="width: 80px;">
                            <span>%</span>
                            <p class="description">Input field width on mobile (default: 100%)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_mobile_border_width">Mobile Border Width</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_mobile_border_width" 
                                   name="ccs_mobile_border_width" 
                                   value="<?php echo esc_attr(get_option('ccs_mobile_border_width', '2')); ?>" 
                                   min="1" 
                                   max="4" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Border width for buttons and inputs on mobile (default: 2px)</p>
                        </td>
                    </tr>
                </table>

                <h3>Mobile Breakpoint</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_mobile_breakpoint">Mobile Breakpoint</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="ccs_mobile_breakpoint" 
                                   name="ccs_mobile_breakpoint" 
                                   value="<?php echo esc_attr(get_option('ccs_mobile_breakpoint', '768')); ?>" 
                                   min="480" 
                                   max="1024" 
                                   style="width: 80px;">
                            <span>px</span>
                            <p class="description">Screen width below which mobile styles apply (default: 768px)</p>
                        </td>
                    </tr>
                </table>

                <?php endif; ?>
                
                </div><!-- .ccs-tab-content -->

                <?php submit_button('Save Styling Settings', 'primary', 'submit', false, ['style' => 'margin-top: 20px; font-size: 16px; padding: 10px 30px;']); ?>
            </form>
            
            <?php
            // Force enqueue color picker scripts and media uploader directly on this page
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_media();
            wp_print_styles('wp-color-picker');
            wp_print_scripts('wp-color-picker');
            ?>

            <script type="text/javascript">
            jQuery(document).ready(function($) {
                // WordPress Media Uploader for images
                var mediaUploader;
                
                $('.ccs-upload-image-btn').on('click', function(e) {
                    e.preventDefault();
                    var button = $(this);
                    var targetField = button.data('target');
                    
                    // If the uploader object has already been created, reopen the dialog
                    if (mediaUploader) {
                        mediaUploader.open();
                        return;
                    }
                    
                    // Create the media uploader
                    mediaUploader = wp.media({
                        title: 'Choose Image',
                        button: {
                            text: 'Use this image'
                        },
                        multiple: false
                    });
                    
                    // When an image is selected, run a callback
                    mediaUploader.on('select', function() {
                        var attachment = mediaUploader.state().get('selection').first().toJSON();
                        $('#' + targetField).val(attachment.url);
                        
                        // Show preview if it exists
                        var preview = $('#' + targetField).closest('td').find('img');
                        if (preview.length) {
                            preview.attr('src', attachment.url);
                        } else {
                            // Create preview
                            var maxWidth = targetField.includes('email') ? '24px' : '60px';
                            $('#' + targetField).closest('td').find('.description').after(
                                '<div style="margin-top:10px;"><img src="' + attachment.url + '" style="max-width:' + maxWidth + '; height:auto; border:1px solid #ddd; padding:5px;"></div>'
                            );
                        }
                    });
                    
                    // Open the uploader dialog
                    mediaUploader.open();
                });
                
                // Remove image button
                $('.ccs-remove-image-btn').on('click', function(e) {
                    e.preventDefault();
                    var button = $(this);
                    var targetField = button.data('target');
                    
                    $('#' + targetField).val('');
                    $('#' + targetField).closest('td').find('img').parent().remove();
                });
                
                // Try to manually load color picker if not available
                function loadColorPickerFallback() {
                    if (typeof $.fn.wpColorPicker === 'undefined') {
                        // Load CSS
                        if (!$('link[href*="wp-color-picker"]').length) {
                            $('<link rel="stylesheet" href="' + ajaxurl.replace('admin-ajax.php', 'css/color-picker.min.css') + '">').appendTo('head');
                        }
                        
                        // Try to load script
                        $.getScript(ajaxurl.replace('admin-ajax.php', 'js/color-picker.min.js'))
                            .done(function() {
                                setTimeout(initColorPickers, 100);
                            });
                    }
                }
                
                // Initialize color pickers
                function initColorPickers() {
                    if (typeof $.fn.wpColorPicker !== 'undefined') {
                        
                        $('.ccs-color-picker').each(function() {
                            var $this = $(this);
                            
                            // Skip if already initialized
                            if ($this.hasClass('wp-color-picker')) {
                                return;
                            }
                            
                            // Start with basic options that always work
                            var basicOptions = {
                                defaultColor: false,
                                change: function(event, ui) {
                                    var color = ui.color.toString();
                                    $(this).val(color).trigger('change');
                                },
                                clear: function() {
                                    $(this).val('').trigger('change');
                                },
                                hide: true,
                                palettes: [
                                    '#0073aa', '#005f8a', '#00a32a', '#6B46C1', '#4ECDC4',
                                    '#f57c00', '#c2185b', '#1976d2', '#333333', '#666666',
                                    '#f5f5f5', '#ffffff', '#000000', '#ff0000', '#00ff00'
                                ]
                            };
                            
                            try {
                                $this.wpColorPicker(basicOptions);
                            } catch(e) {
                                // Silently fail
                            }
                        });
                    }
                }
                
                // Initialize with multiple fallbacks to ensure it works
                function tryInitColorPickers() {
                    if (typeof $.fn.wpColorPicker !== 'undefined') {
                        initColorPickers();
                    } else {
                        loadColorPickerFallback();
                    }
                }
                
                // Try to initialize immediately
                tryInitColorPickers();
                
                // Also try after a delay
                setTimeout(function() {
                    if (typeof $.fn.wpColorPicker !== 'undefined') {
                        initColorPickers();
                    } else {
                        loadColorPickerFallback();
                    }
                }, 1000);
                
                // Final attempt after window load
                $(window).on('load', function() {
                    setTimeout(function() {
                        if (typeof $.fn.wpColorPicker !== 'undefined') {
                            initColorPickers();
                        }
                    }, 500);
                });
                
                // Add format info and validation
                setTimeout(function() {
                    $('.ccs-color-picker').each(function() {
                        var $input = $(this);
                        var $wrapper = $input.closest('.wp-picker-container');
                        
                        if ($wrapper.length && !$wrapper.find('.color-format-info').length) {
                            // Add format info
                            var $formatInfo = $('<div class="color-format-info" style="margin-top: 5px; font-size: 11px; color: #666;">Supports: HEX, RGB, RGBA with alpha transparency</div>');
                            $wrapper.append($formatInfo);
                            
                            // Add validation on input change
                            $input.on('input', function() {
                                var value = $(this).val();
                                var isValid = isValidColor(value);
                                
                                if (isValid) {
                                    $(this).css('border-color', '');
                                    $formatInfo.css('color', '#666').text('Supports: HEX, RGB, RGBA with alpha transparency');
                                } else if (value.trim() !== '') {
                                    $(this).css('border-color', '#dc3232');
                                    $formatInfo.css('color', '#dc3232').text('Invalid color format. Use HEX (#ff0000), RGB (rgb(255,0,0)), or RGBA (rgba(255,0,0,0.5))');
                                }
                            });
                        }
                    });
                }, 500);
                
                // Color validation function
                function isValidColor(color) {
                    if (!color || color.trim() === '') return true;
                    
                    color = color.trim();
                    
                    // Check HEX format
                    if (/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(color)) {
                        return true;
                    }
                    
                    // Check RGB format
                    if (/^rgb\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)$/.test(color)) {
                        var matches = color.match(/\d+/g);
                        return matches.every(function(val) {
                            return parseInt(val) >= 0 && parseInt(val) <= 255;
                        });
                    }
                    
                    // Check RGBA format
                    if (/^rgba\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(0|1|0?\.\d+)\s*\)$/.test(color)) {
                        var matches = color.match(/(\d+(?:\.\d+)?)/g);
                        if (matches.length >= 4) {
                            var r = parseInt(matches[0]);
                            var g = parseInt(matches[1]);
                            var b = parseInt(matches[2]);
                            var a = parseFloat(matches[3]);
                            return r >= 0 && r <= 255 && g >= 0 && g <= 255 && b >= 0 && b <= 255 && a >= 0 && a <= 1;
                        }
                    }
                    
                    return false;
                }
            });
            </script>
            
            <style type="text/css">
            /* Enhanced Color Picker Styling */
            .wp-picker-container {
                display: inline-block;
                margin-bottom: 15px;
                vertical-align: top;
            }
            
            .ccs-color-picker {
                width: 140px !important;
                font-family: monospace !important;
                font-size: 12px !important;
            }
            
            .wp-picker-input-wrap {
                margin-bottom: 5px;
            }
            
            .color-format-info {
                font-style: italic;
                line-height: 1.3;
                margin-top: 3px !important;
            }
            
            /* Alpha Color Picker Enhancements */
            .wp-picker-container .wp-color-result {
                height: 30px !important;
                border-radius: 4px !important;
            }
            
            .wp-picker-container .wp-color-result:after {
                font-size: 12px !important;
                line-height: 28px !important;
            }
            
            /* Color picker popup styling */
            .wp-picker-container .wp-picker-holder {
                position: absolute;
                z-index: 100000;
            }
            
            /* Form table spacing for color pickers */
            .form-table td .wp-picker-container {
                margin-right: 10px;
            }
            
            /* Validation styling */
            .ccs-color-picker.invalid {
                border-color: #dc3232 !important;
                box-shadow: 0 0 2px rgba(220, 50, 50, 0.8) !important;
            }
            </style>

            <div style="background:#f0f8f0; padding:20px; border-left:4px solid #00a32a; margin-top:30px; border-radius:8px;">
                <h3 style="margin-top:0;">✅ Enhanced Settings Handler Status</h3>
                <p><strong>Total Settings:</strong> 100+ comprehensive styling options</p>
                <p><strong>Handler Status:</strong> <span style="color: #00a32a;">✅ Enhanced handler with proper sanitization and validation</span></p>
                <p><strong>Data Persistence:</strong> <span style="color: #00a32a;">✅ All settings preserved across tab switches</span></p>
                <p><strong>Security:</strong> <span style="color: #00a32a;">✅ Nonce verification and proper sanitization</span></p>
                <p style="margin-bottom:0;"><em>Switch freely between all tabs - your settings are automatically saved and validated!</em></p>
            </div>

            <div style="background:#e7f5fe; padding:20px; border-left:4px solid #0073aa; margin-top:20px; border-radius:8px;">
                <h3 style="margin-top:0;">💡 Quick Tips</h3>
                <ul style="margin-bottom:0;">
                    <li><strong>Preview Changes:</strong> After saving, visit your calculator page to see the changes</li>
                    <li><strong>Clear Cache:</strong> You may need to clear your browser cache to see updates</li>
                    <li><strong>Color Codes:</strong> Use hex codes (e.g., #0073aa) for precise colors</li>
                    <li><strong>Responsive:</strong> All settings work on desktop, tablet, and mobile</li>
                    <li><strong>Tab Switching:</strong> All settings are automatically preserved when switching between tabs</li>
                </ul>
            </div>
            </div><!-- .ccs-appearance-page -->
        </div><!-- .wrap -->

        <script>
        jQuery(document).ready(function($) {
            // Auto-hide success messages after 5 seconds
            $('.notice-success').delay(5000).fadeOut();
        });
        </script>
        <?php
    }

    public function childcare_ccs_custom_css_page()
    {
        if (!current_user_can('manage_options')) return;
        
        // Handle form submission
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['ccs_custom_css_nonce'], 'ccs_custom_css_save')) {
            $custom_css = isset($_POST['ccs_custom_css']) ? wp_strip_all_tags($_POST['ccs_custom_css']) : '';
            update_option('ccs_custom_css', $custom_css);
            echo '<div class="notice notice-success is-dismissible"><p>Custom CSS saved successfully!</p></div>';
        }
        
        // Get current custom CSS
        $custom_css = get_option('ccs_custom_css', '');
        ?>
        <div class="ccs-admin-wrap">
        <div class="wrap ccs-custom-css-page">
            <h1 class="wp-heading-inline">
                <span class="dashicons dashicons-editor-code" style="font-size:28px; width:28px; height:28px; margin-right:10px;"></span>
                Custom CSS
            </h1>
            <p class="description" style="margin-top:10px; margin-bottom:20px;">Add custom CSS to override existing styles for the CCS Calculator. This CSS will be applied to the frontend calculator.</p>
            
            <form method="post" action="">
                <?php wp_nonce_field('ccs_custom_css_save', 'ccs_custom_css_nonce'); ?>
                
                <div class="ccs-settings-card" style="background:#fff; padding:25px; border:1px solid #ddd; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.05); margin-bottom:20px;">
                    <h2 style="margin-top:0; padding-bottom:15px; border-bottom:2px solid #0073aa; color:#0073aa; font-size:18px;">
                        <span class="dashicons dashicons-admin-customizer" style="font-size:20px; width:20px; height:20px; margin-right:8px;"></span>
                        CSS Editor
                    </h2>
                    
                    <div style="margin-top:20px;">
                        <label style="display:block; font-weight:600; margin-bottom:8px; color:#333;">
                            Custom CSS Code
                        </label>
                        <textarea 
                            name="ccs_custom_css" 
                            rows="20" 
                            style="width:100%; padding:15px; border:1px solid #ddd; border-radius:4px; font-family:monospace; font-size:13px; line-height:1.6; background:#f9f9f9;"
                            placeholder="/* Add your custom CSS here */&#10;&#10;.ccs-calculator {&#10;    /* Your styles */&#10;}"><?php echo esc_textarea($custom_css); ?></textarea>
                        <p class="description" style="margin-top:8px; color:#666;">
                            Write your custom CSS here. This will be loaded after all other calculator styles, allowing you to override any existing styles.
                        </p>
                    </div>
                </div>
                
                <div style="background:#e7f5fe; padding:15px; border-left:4px solid #0073aa; margin-bottom:20px;">
                    <h3 style="margin-top:0; color:#0073aa;">💡 Tips for Custom CSS</h3>
                    <ul style="margin:10px 0; padding-left:20px;">
                        <li>Use <code>.ccs-calculator</code> as the main wrapper class</li>
                        <li>Target specific steps with <code>.ccs-step-1</code>, <code>.ccs-step-2</code>, etc.</li>
                        <li>Override button styles with <code>.ccs-button</code></li>
                        <li>Modify input fields with <code>.ccs-input</code></li>
                        <li>Use <code>!important</code> if needed to override existing styles</li>
                    </ul>
                </div>
                
                <div style="background:#fff3cd; padding:15px; border-left:4px solid #ffc107; margin-bottom:20px;">
                    <h3 style="margin-top:0; color:#856404;">⚠️ Important Notes</h3>
                    <ul style="margin:10px 0; padding-left:20px;">
                        <li>Test your CSS thoroughly on different devices and browsers</li>
                        <li>Invalid CSS may break the calculator's appearance</li>
                        <li>This CSS applies to the frontend calculator only, not the admin area</li>
                        <li>Clear your browser cache after saving to see changes</li>
                    </ul>
                </div>
                
                <?php submit_button('Save Custom CSS', 'primary', 'submit', false); ?>
            </form>
        </div>
        </div>
        
        <style>
        .ccs-custom-css-page textarea:focus {
            border-color: #0073aa;
            outline: none;
            box-shadow: 0 0 0 1px #0073aa;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            // Auto-hide success messages after 5 seconds
            $('.notice-success').delay(5000).fadeOut();
        });
        </script>
        <?php
    }

    public function childcare_ccs_email_page()
    {
        if (!current_user_can('manage_options')) return;
        
        // Get current template settings
        $template_colors = get_option('ccs_email_template_colors', array(
            'primary' => '#0073aa',
            'secondary' => '#005f8a', 
            'background' => '#f8f9fa',
            'text' => '#333333',
            'accent' => '#00a32a'
        ));
        
        ?>
        <div class="wrap">
            <h1>📧 Email Template Editor</h1>
            <p>Customize your email templates with live preview. Changes appear instantly.</p>
            
            <!-- WordPress Standard Tabs -->
            <h2 class="nav-tab-wrapper">
                <a href="#user-template" class="nav-tab nav-tab-active" data-template="user">User Email</a>
                <a href="#admin-template" class="nav-tab" data-template="admin">Admin Notification</a>
            </h2>
            
            <form method="post" action="options.php" id="email-template-form">
                <?php settings_fields('childcare_ccs_email_group'); ?>
                <?php do_settings_sections('childcare_ccs_email_group'); ?>
                
                <!-- User Email Editor -->
                <div class="ccs-email-editor-wrapper ccs-template-content active" id="user-template">
                    <!-- Settings Sidebar -->
                    <div class="ccs-editor-sidebar">
                        <div class="ccs-sidebar-header">
                            <h3>✏️ Edit Template</h3>
                            <p>Make changes and see them live</p>
                        </div>
                        
                        <!-- Header Image Section -->
                        <details class="ccs-section" open>
                            <summary>🖼️ Header Image</summary>
                            <div class="ccs-section-content">
                                <div class="ccs-image-upload-area" id="header-image-dropzone">
                                    <?php if (get_option('ccs_email_header_image')): ?>
                                        <img src="<?php echo esc_url(get_option('ccs_email_header_image')); ?>" class="preview-image" id="header-preview-img">
                                        <button type="button" class="button-link remove-image" id="remove-header-image">✕ Remove</button>
                                    <?php else: ?>
                                        <div class="upload-placeholder">
                                            <span class="dashicons dashicons-format-image"></span>
                                            <p>Click or drag image here</p>
                                            <small>Recommended: 600px × 170px</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <input type="hidden" id="ccs_email_header_image" name="ccs_email_header_image" value="<?php echo esc_attr(get_option('ccs_email_header_image', '')); ?>">
                                <button type="button" class="button button-secondary button-small" id="upload_header_image" style="width:100%; margin-top:10px;">
                                    <span class="dashicons dashicons-upload"></span> Choose Image
                                </button>
                            </div>
                        </details>
                        
                        <!-- Email Content Section -->
                        <details class="ccs-section" open>
                            <summary>📝 Email Content</summary>
                            <div class="ccs-section-content">
                                <div class="ccs-field">
                                    <label for="ccs_email_template_subject">Subject Line</label>
                                    <input type="text" 
                                           id="ccs_email_template_subject" 
                                           name="ccs_email_template_subject" 
                                           value="<?php echo esc_attr(get_option('ccs_email_template_subject', 'Your Child Care Subsidy Estimate')); ?>" 
                                           class="ccs-input">
                                </div>
                                
                                <div class="ccs-field">
                                    <label for="ccs_email_template_greeting">Greeting</label>
                                    <input type="text" 
                                           id="ccs_email_template_greeting" 
                                           name="ccs_email_template_greeting" 
                                           value="<?php echo esc_attr(get_option('ccs_email_template_greeting', 'Hi {firstname},')); ?>" 
                                           class="ccs-input"
                                           placeholder="Hi {firstname},">
                                    <small>Use {firstname} for personalization</small>
                                </div>
                                
                                <div class="ccs-field">
                                    <label for="ccs_email_template_intro">Introduction</label>
                                    <textarea id="ccs_email_template_intro" 
                                              name="ccs_email_template_intro" 
                                              class="ccs-textarea" 
                                              rows="3"><?php echo esc_textarea(get_option('ccs_email_template_intro', 'Thank you for using our Child Care Subsidy Calculator. Here\'s your personalized estimate:')); ?></textarea>
                                </div>
                                
                                <div class="ccs-field">
                                    <label for="ccs_email_template_body">Additional Content</label>
                                    <textarea id="ccs_email_template_body" 
                                              name="ccs_email_template_body" 
                                              class="ccs-textarea" 
                                              rows="4"><?php echo esc_textarea(get_option('ccs_email_template_body', 'This estimate is based on the information you provided and current government rates. Actual amounts may vary.')); ?></textarea>
                                </div>
                                
                                <div class="ccs-field">
                                    <label for="ccs_email_template_footer_text">Footer Text</label>
                                    <textarea id="ccs_email_template_footer_text" 
                                              name="ccs_email_template_footer_text" 
                                              class="ccs-textarea" 
                                              rows="2"><?php echo esc_textarea(get_option('ccs_email_template_footer_text', 'If you have any questions, please don\'t hesitate to contact us.')); ?></textarea>
                                </div>
                                
                                <div class="ccs-field-group">
                                    <label class="ccs-checkbox">
                                        <input type="checkbox" name="ccs_email_template_show_summary" value="1" <?php checked(get_option('ccs_email_template_show_summary', 1), 1); ?>>
                                        <span>Show calculation summary</span>
                                    </label>
                                    <label class="ccs-checkbox">
                                        <input type="checkbox" name="ccs_email_template_show_breakdown" value="1" <?php checked(get_option('ccs_email_template_show_breakdown', 1), 1); ?>>
                                        <span>Show detailed breakdown</span>
                                    </label>
                                </div>
                            </div>
                        </details>
                        
                        <!-- Colors Section -->
                        <details class="ccs-section">
                            <summary>🎨 Colors</summary>
                            <div class="ccs-section-content">
                                <div class="ccs-color-grid">
                                    <div class="ccs-color-item">
                                        <label>Primary</label>
                                        <input type="text" name="ccs_email_template_colors[primary]" value="<?php echo esc_attr($template_colors['primary'] ?? '#0073aa'); ?>" class="ccs-color-picker">
                                    </div>
                                    <div class="ccs-color-item">
                                        <label>Accent</label>
                                        <input type="text" name="ccs_email_template_colors[accent]" value="<?php echo esc_attr($template_colors['accent'] ?? '#00a32a'); ?>" class="ccs-color-picker">
                                    </div>
                                    <div class="ccs-color-item">
                                        <label>Background</label>
                                        <input type="text" name="ccs_email_template_colors[background]" value="<?php echo esc_attr($template_colors['background'] ?? '#f8f9fa'); ?>" class="ccs-color-picker">
                                    </div>
                                    <div class="ccs-color-item">
                                        <label>Text</label>
                                        <input type="text" name="ccs_email_template_colors[text]" value="<?php echo esc_attr($template_colors['text'] ?? '#333333'); ?>" class="ccs-color-picker">
                                    </div>
                                </div>
                            </div>
                        </details>
                        
                        <!-- Contact Info Section -->
                        <details class="ccs-section">
                            <summary>📞 Contact Information</summary>
                            <div class="ccs-section-content">
                                <div class="ccs-field">
                                    <label for="ccs_email_contact_phone">Phone</label>
                                    <input type="text" 
                                           id="ccs_email_contact_phone" 
                                           name="ccs_email_contact_phone" 
                                           value="<?php echo esc_attr(get_option('ccs_email_contact_phone', '1800 222 543')); ?>" 
                                           class="ccs-input">
                                </div>
                                <div class="ccs-field">
                                    <label for="ccs_email_contact_email">Email</label>
                                    <input type="email" 
                                           id="ccs_email_contact_email" 
                                           name="ccs_email_contact_email" 
                                           value="<?php echo esc_attr(get_option('ccs_email_contact_email', get_option('admin_email'))); ?>" 
                                           class="ccs-input">
                                </div>
                            </div>
                        </details>
                        
                        <!-- Social Media Section -->
                        <details class="ccs-section">
                            <summary>📱 Social Media</summary>
                            <div class="ccs-section-content">
                                <div class="ccs-field">
                                    <label for="ccs_email_facebook">Facebook</label>
                                    <input type="url" 
                                           id="ccs_email_facebook" 
                                           name="ccs_email_facebook" 
                                           value="<?php echo esc_attr(get_option('ccs_email_facebook', '')); ?>" 
                                           class="ccs-input"
                                           placeholder="https://facebook.com/yourpage">
                                </div>
                                <div class="ccs-field">
                                    <label for="ccs_email_twitter">Twitter/X</label>
                                    <input type="url" 
                                           id="ccs_email_twitter" 
                                           name="ccs_email_twitter" 
                                           value="<?php echo esc_attr(get_option('ccs_email_twitter', '')); ?>" 
                                           class="ccs-input"
                                           placeholder="https://twitter.com/yourhandle">
                                </div>
                                <div class="ccs-field">
                                    <label for="ccs_email_instagram">Instagram</label>
                                    <input type="url" 
                                           id="ccs_email_instagram" 
                                           name="ccs_email_instagram" 
                                           value="<?php echo esc_attr(get_option('ccs_email_instagram', '')); ?>" 
                                           class="ccs-input"
                                           placeholder="https://instagram.com/yourhandle">
                                </div>
                                <div class="ccs-field">
                                    <label for="ccs_email_linkedin">LinkedIn</label>
                                    <input type="url" 
                                           id="ccs_email_linkedin" 
                                           name="ccs_email_linkedin" 
                                           value="<?php echo esc_attr(get_option('ccs_email_linkedin', '')); ?>" 
                                           class="ccs-input"
                                           placeholder="https://linkedin.com/company/yourcompany">
                                </div>
                            </div>
                        </details>
                    </div>
                    
                    <!-- Live Preview Panel -->
                    <div class="ccs-preview-panel">
                        <div class="ccs-preview-header">
                            <h3>👁️ Live Preview</h3>
                            <div class="ccs-preview-actions">
                                <button type="button" class="button button-secondary" id="send-test-email">
                                    <span class="dashicons dashicons-email"></span> Send Test
                                </button>
                            </div>
                        </div>
                        <div class="ccs-preview-container">
                            <div class="ccs-email-preview" id="email-live-preview">
                                <!-- Live preview will be generated here -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Admin Email Editor -->
                <div class="ccs-email-editor-wrapper ccs-template-content" id="admin-template">
                    <!-- Settings Sidebar -->
                    <div class="ccs-editor-sidebar">
                        <div class="ccs-sidebar-header">
                            <h3>✏️ Edit Admin Notification</h3>
                            <p>Customize admin notification email</p>
                        </div>
                        
                        <!-- Admin Email Content Section -->
                        <details class="ccs-section" open>
                            <summary>📝 Email Content</summary>
                            <div class="ccs-section-content">
                                <div class="ccs-field">
                                    <label for="ccs_admin_email_subject">Subject Line</label>
                                    <input type="text" 
                                           id="ccs_admin_email_subject" 
                                           name="ccs_admin_email_subject" 
                                           value="<?php echo esc_attr(get_option('ccs_admin_email_subject', 'New CCS Calculation Submission - {name}')); ?>" 
                                           class="ccs-input">
                                    <small>Use {name} for user's name</small>
                                </div>
                                
                                <div class="ccs-field">
                                    <label for="ccs_admin_email_heading">Email Heading</label>
                                    <input type="text" 
                                           id="ccs_admin_email_heading" 
                                           name="ccs_admin_email_heading" 
                                           value="<?php echo esc_attr(get_option('ccs_admin_email_heading', '📬 New CCS Submission')); ?>" 
                                           class="ccs-input">
                                </div>
                                
                                <div class="ccs-field">
                                    <label for="ccs_admin_email_intro">Introduction Text</label>
                                    <textarea id="ccs_admin_email_intro" 
                                              name="ccs_admin_email_intro" 
                                              class="ccs-textarea" 
                                              rows="2"><?php echo esc_textarea(get_option('ccs_admin_email_intro', 'A new Child Care Subsidy calculation has been submitted.')); ?></textarea>
                                </div>
                                
                                <div class="ccs-field">
                                    <label for="ccs_admin_email_footer">Footer Text</label>
                                    <textarea id="ccs_admin_email_footer" 
                                              name="ccs_admin_email_footer" 
                                              class="ccs-textarea" 
                                              rows="2"><?php echo esc_textarea(get_option('ccs_admin_email_footer', 'This is an automated notification from your website.')); ?></textarea>
                                </div>
                                
                                <div class="ccs-field-group">
                                    <label class="ccs-checkbox">
                                        <input type="checkbox" name="ccs_admin_email_show_location" value="1" <?php checked(get_option('ccs_admin_email_show_location', 1), 1); ?>>
                                        <span>Show location details</span>
                                    </label>
                                    <label class="ccs-checkbox">
                                        <input type="checkbox" name="ccs_admin_email_show_timestamp" value="1" <?php checked(get_option('ccs_admin_email_show_timestamp', 1), 1); ?>>
                                        <span>Show submission timestamp</span>
                                    </label>
                                </div>
                            </div>
                        </details>
                        
                        <!-- Admin Colors Section -->
                        <details class="ccs-section">
                            <summary>🎨 Colors</summary>
                            <div class="ccs-section-content">
                                <?php
                                $admin_colors = get_option('ccs_admin_email_colors', array(
                                    'header' => '#d63638',
                                    'button' => '#0073aa',
                                    'background' => '#f4f4f4',
                                    'text' => '#333333'
                                ));
                                ?>
                                <div class="ccs-color-grid">
                                    <div class="ccs-color-item">
                                        <label>Header Color</label>
                                        <input type="text" name="ccs_admin_email_colors[header]" value="<?php echo esc_attr($admin_colors['header'] ?? '#d63638'); ?>" class="ccs-color-picker">
                                    </div>
                                    <div class="ccs-color-item">
                                        <label>Button Color</label>
                                        <input type="text" name="ccs_admin_email_colors[button]" value="<?php echo esc_attr($admin_colors['button'] ?? '#0073aa'); ?>" class="ccs-color-picker">
                                    </div>
                                    <div class="ccs-color-item">
                                        <label>Background</label>
                                        <input type="text" name="ccs_admin_email_colors[background]" value="<?php echo esc_attr($admin_colors['background'] ?? '#f4f4f4'); ?>" class="ccs-color-picker">
                                    </div>
                                    <div class="ccs-color-item">
                                        <label>Text Color</label>
                                        <input type="text" name="ccs_admin_email_colors[text]" value="<?php echo esc_attr($admin_colors['text'] ?? '#333333'); ?>" class="ccs-color-picker">
                                    </div>
                                </div>
                            </div>
                        </details>
                        
                        <!-- Admin Email Recipients -->
                        <details class="ccs-section">
                            <summary>📧 Recipients</summary>
                            <div class="ccs-section-content">
                                <div class="ccs-field">
                                    <label for="ccs_admin_notification_emails">Email Addresses</label>
                                    <textarea id="ccs_admin_notification_emails" 
                                              name="ccs_admin_notification_emails" 
                                              class="ccs-textarea" 
                                              rows="3" 
                                              placeholder="admin@example.com&#10;manager@example.com"><?php echo esc_textarea(get_option('ccs_admin_notification_emails', get_option('admin_email'))); ?></textarea>
                                    <small>One email per line. Leave blank to use site admin email.</small>
                                </div>
                            </div>
                        </details>
                    </div>
                    
                    <!-- Live Preview Panel -->
                    <div class="ccs-preview-panel">
                        <div class="ccs-preview-header">
                            <h3>👁️ Live Preview</h3>
                            <div class="ccs-preview-actions">
                                <button type="button" class="button button-secondary" id="send-test-admin-email">
                                    <span class="dashicons dashicons-email"></span> Send Test
                                </button>
                            </div>
                        </div>
                        <div class="ccs-preview-container">
                            <div class="ccs-email-preview" id="admin-email-live-preview">
                                <!-- Live preview will be generated here -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="ccs-form-actions">
                    <?php submit_button('💾 Save Email Templates', 'primary', 'submit', false, ['style' => 'font-size: 16px; padding: 10px 30px;']); ?>
                    <button type="button" class="button button-secondary" id="reset-template">🔄 Reset to Default</button>
                </div>
            </form>
        </div>

        <!-- Email Template Editor Styles -->
        <style>
        /* WordPress Standard Tabs */
        .nav-tab-wrapper {
            margin-bottom: 0;
        }
        
        /* Template Content - jQuery handles visibility */
        .ccs-template-content {
            /* jQuery .hide() and .show() will control visibility */
        }
        
        /* Email Editor Grid Layout */
        .ccs-email-editor-wrapper {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        
        /* Sidebar Styles */
        .ccs-editor-sidebar {
            background: #fff;
            border: 1px solid #dcdcde;
            border-radius: 8px;
            overflow: hidden;
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }
        
        .ccs-sidebar-header {
            padding: 20px;
            background: linear-gradient(135deg, #0073aa 0%, #005177 100%);
            color: #fff;
            border-bottom: 1px solid #dcdcde;
        }
        
        .ccs-sidebar-header h3 {
            margin: 0 0 5px 0;
            font-size: 18px;
            color: #fff;
        }
        
        .ccs-sidebar-header p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }
        
        /* Collapsible Sections */
        .ccs-section {
            border-bottom: 1px solid #f0f0f1;
        }
        
        .ccs-section summary {
            padding: 15px 20px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            color: #1d2327;
            list-style: none;
            user-select: none;
            background: #fff;
            transition: background 0.2s;
        }
        
        .ccs-section summary::-webkit-details-marker {
            display: none;
        }
        
        .ccs-section summary:hover {
            background: #f9f9f9;
        }
        
        .ccs-section summary::before {
            content: '▶';
            display: inline-block;
            margin-right: 8px;
            font-size: 10px;
            transition: transform 0.2s;
        }
        
        .ccs-section[open] summary::before {
            transform: rotate(90deg);
        }
        
        .ccs-section-content {
            padding: 15px 20px 20px;
            background: #fafafa;
        }
        
        /* Form Fields */
        .ccs-field {
            margin-bottom: 15px;
        }
        
        .ccs-field:last-child {
            margin-bottom: 0;
        }
        
        .ccs-field label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #1d2327;
            margin-bottom: 6px;
        }
        
        .ccs-field small {
            display: block;
            font-size: 11px;
            color: #646970;
            margin-top: 4px;
        }
        
        .ccs-input,
        .ccs-textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #8c8f94;
            border-radius: 4px;
            font-size: 13px;
            transition: border-color 0.2s;
        }
        
        .ccs-input:focus,
        .ccs-textarea:focus {
            border-color: #0073aa;
            outline: none;
            box-shadow: 0 0 0 1px #0073aa;
        }
        
        .ccs-textarea {
            resize: vertical;
            font-family: inherit;
        }
        
        /* Checkbox Group */
        .ccs-field-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .ccs-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 13px;
        }
        
        .ccs-checkbox input[type="checkbox"] {
            margin: 0;
        }
        
        /* Color Grid */
        .ccs-color-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        
        .ccs-color-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        
        .ccs-color-item label {
            font-size: 12px;
            font-weight: 600;
            color: #1d2327;
        }
        
        .ccs-color-item .ccs-color-picker {
            width: 100% !important;
            height: 36px;
        }
        
        /* Image Upload Area */
        .ccs-image-upload-area {
            position: relative;
            border: 2px dashed #c3c4c7;
            border-radius: 6px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #fff;
        }
        
        .ccs-image-upload-area:hover {
            border-color: #0073aa;
            background: #f6f7f7;
        }
        
        .upload-placeholder {
            color: #646970;
        }
        
        .upload-placeholder .dashicons {
            font-size: 48px;
            width: 48px;
            height: 48px;
            color: #c3c4c7;
            margin-bottom: 10px;
        }
        
        .upload-placeholder p {
            margin: 0 0 5px 0;
            font-size: 13px;
            font-weight: 500;
        }
        
        .upload-placeholder small {
            font-size: 11px;
            color: #8c8f94;
        }
        
        .preview-image {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            display: block;
        }
        
        .remove-image {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 12px;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .remove-image:hover {
            background: rgba(220, 53, 69, 0.9);
        }
        
        /* Preview Panel */
        .ccs-preview-panel {
            background: #fff;
            border: 1px solid #dcdcde;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: calc(100vh - 200px);
        }
        
        .ccs-preview-header {
            padding: 15px 20px;
            background: #f6f7f7;
            border-bottom: 1px solid #dcdcde;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .ccs-preview-header h3 {
            margin: 0;
            font-size: 16px;
            color: #1d2327;
        }
        
        .ccs-preview-actions {
            display: flex;
            gap: 10px;
        }
        
        .ccs-preview-container {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f0f0f1;
        }
        
        .ccs-email-preview {
            background: #fff;
            border-radius: 6px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            min-height: 500px;
        }
        
        /* Form Actions */
        .ccs-form-actions {
            margin-top: 20px;
            padding: 20px;
            background: #fff;
            border: 1px solid #dcdcde;
            border-radius: 8px;
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .ccs-email-editor-wrapper {
                grid-template-columns: 1fr;
            }
            
            .ccs-editor-sidebar {
                max-height: none;
            }
            
            .ccs-preview-panel {
                max-height: 600px;
            }
        }
        
        /* Loading animation */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        </style>

        <script>
        jQuery(document).ready(function($) {
            // Debug: Check if wp.media is available
            console.log('Email Template Editor loaded');
            console.log('wp.media available:', typeof wp !== 'undefined' && typeof wp.media !== 'undefined');
            
            // Template switching with WordPress standard tabs
            $('.nav-tab').on('click', function(e) {
                e.preventDefault();
                var template = $(this).data('template');
                var targetId = $(this).attr('href');
                
                console.log('Tab clicked:', template, targetId);
                
                // Update tab active state
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                
                // Update content visibility
                $('.ccs-template-content').removeClass('active').hide();
                $(targetId).addClass('active').show();
                
                console.log('Active template:', targetId);
                console.log('Visible templates:', $('.ccs-template-content.active').length);
                
                // Re-initialize color pickers for the active template
                setTimeout(function() {
                    initColorPickers();
                    if (template === 'user') {
                        updateLivePreview();
                    } else {
                        updateAdminPreview();
                    }
                }, 100);
            });
            
            // Ensure only user template is visible on load
            console.log('Initializing templates...');
            $('.ccs-template-content').removeClass('active').hide();
            $('#user-template').addClass('active').show();
            console.log('Initial state set - only user template should be visible');
            
            // Initialize color pickers
            function initColorPickers() {
                $('.ccs-color-picker').each(function() {
                    var $this = $(this);
                    if (!$this.hasClass('wp-color-picker')) {
                        try {
                            $this.wpColorPicker({
                                defaultColor: false,
                                change: function(event, ui) {
                                    var color = ui.color.toString();
                                    $(this).val(color).trigger('change');
                                    updateLivePreview();
                                },
                                clear: function() {
                                    $(this).val('').trigger('change');
                                    updateLivePreview();
                                },
                                hide: true,
                                palettes: [
                                    '#0073aa', '#005f8a', '#00a32a', '#6B46C1', '#4ECDC4',
                                    '#f57c00', '#c2185b', '#1976d2', '#333333', '#666666',
                                    '#f5f5f5', '#ffffff', '#000000', '#ff0000', '#00ff00'
                                ]
                            });
                        } catch(e) {
                            console.log('Color picker init error:', e);
                        }
                    }
                });
            }
            
            // Initialize color pickers
            initColorPickers();
            
            // Media uploader for header image
            var mediaUploader;
            
            // Handle button click
            $(document).on('click', '#upload_header_image', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var $btn = $(this);
                var originalText = $btn.html();
                $btn.html('<span class="dashicons dashicons-update-alt" style="animation: spin 1s linear infinite;"></span> Opening...');
                $btn.prop('disabled', true);
                
                setTimeout(function() {
                    $btn.html(originalText);
                    $btn.prop('disabled', false);
                }, 500);
                
                openMediaUploader();
            });
            
            // Handle dropzone click
            $(document).on('click', '#header-image-dropzone', function(e) {
                e.preventDefault();
                e.stopPropagation();
                openMediaUploader();
            });
            
            function openMediaUploader() {
                // Check if wp.media is available
                if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                    alert('Media uploader is not available. Please refresh the page and try again.');
                    console.error('wp.media is not defined');
                    return;
                }
                
                // If the media frame already exists, reopen it
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                
                // Create the media frame
                mediaUploader = wp.media({
                    title: 'Choose Email Header Image',
                    button: {
                        text: 'Use this image'
                    },
                    multiple: false,
                    library: {
                        type: 'image'
                    }
                });
                
                // When an image is selected, run a callback
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    
                    console.log('Image selected:', attachment.url);
                    
                    // Set the hidden input value
                    $('#ccs_email_header_image').val(attachment.url);
                    
                    // Update the preview in the upload area
                    var $dropzone = $('#header-image-dropzone');
                    $dropzone.html(
                        '<img src="' + attachment.url + '" class="preview-image" id="header-preview-img">' +
                        '<button type="button" class="button-link remove-image" id="remove-header-image">✕ Remove</button>'
                    );
                    
                    // Update live preview
                    updateLivePreview();
                });
                
                // Open the media frame
                mediaUploader.open();
            }
            
            // Remove header image
            $(document).on('click', '#remove-header-image', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                $('#ccs_email_header_image').val('');
                $('#header-image-dropzone').html(
                    '<div class="upload-placeholder">' +
                    '<span class="dashicons dashicons-format-image"></span>' +
                    '<p>Click or drag image here</p>' +
                    '<small>Recommended: 600px × 170px</small>' +
                    '</div>'
                );
                
                updateLivePreview();
            });
            
            // Live preview updates
            function updateLivePreview() {
                var previewHtml = generateEmailPreview();
                $('#email-live-preview').html(previewHtml);
            }
            
            // Generate email preview HTML
            function generateEmailPreview() {
                var colors = {
                    primary: $('input[name="ccs_email_template_colors[primary]"]').val() || '#0073aa',
                    background: $('input[name="ccs_email_template_colors[background]"]').val() || '#f8f9fa',
                    text: $('input[name="ccs_email_template_colors[text]"]').val() || '#333333',
                    accent: $('input[name="ccs_email_template_colors[accent]"]').val() || '#00a32a'
                };
                
                var headerImage = $('#ccs_email_header_image').val();
                var greeting = $('#ccs_email_template_greeting').val() || 'Hi {firstname},';
                var intro = $('#ccs_email_template_intro').val() || 'Thank you for using our Child Care Subsidy Calculator. Here\'s your personalized estimate:';
                var body = $('#ccs_email_template_body').val() || 'This estimate is based on the information you provided and current government rates. Actual amounts may vary.';
                var footerText = $('#ccs_email_template_footer_text').val() || 'If you have any questions, please don\'t hesitate to contact us.';
                
                var html = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">';
                
                // Header with logo
                if (headerImage) {
                    html += '<div style="text-align: center; margin-bottom: 30px;">';
                    html += '<img src="' + headerImage + '" style="max-width: 100%; height: auto;" alt="Header">';
                    html += '</div>';
                }
                
                // Email body container
                html += '<div style="background-color: ' + colors.background + '; padding: 30px; border-radius: 8px;">';
                
                // Greeting
                var displayGreeting = greeting.replace('{firstname}', 'John');
                html += '<h2 style="color: ' + colors.primary + '; margin: 0 0 20px 0; font-size: 24px;">' + displayGreeting + '</h2>';
                
                // Introduction
                html += '<p style="color: ' + colors.text + '; line-height: 1.6; margin: 0 0 20px 0;">' + intro + '</p>';
                
                // Sample calculation results
                html += '<div style="background: #fff; padding: 25px; border-radius: 8px; margin: 25px 0; border-left: 4px solid ' + colors.accent + '; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">';
                html += '<h3 style="color: ' + colors.primary + '; margin: 0 0 15px 0; font-size: 20px;">Your Estimate Summary</h3>';
                html += '<div style="margin: 15px 0;">';
                html += '<div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f1;">';
                html += '<span style="color: ' + colors.text + ';">Total Fees:</span>';
                html += '<strong style="color: ' + colors.text + ';">$450.00 per fortnight</strong>';
                html += '</div>';
                html += '<div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f1;">';
                html += '<span style="color: ' + colors.text + ';">Estimated Subsidy:</span>';
                html += '<strong style="color: ' + colors.accent + ';">$315.00 per fortnight</strong>';
                html += '</div>';
                html += '<div style="display: flex; justify-content: space-between; padding: 15px 0; margin-top: 10px;">';
                html += '<span style="color: ' + colors.text + '; font-size: 18px;"><strong>Out-of-pocket:</strong></span>';
                html += '<strong style="color: ' + colors.primary + '; font-size: 20px;">$135.00 per fortnight</strong>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                
                // Additional body content
                if (body) {
                    html += '<p style="color: ' + colors.text + '; line-height: 1.6; margin: 20px 0;">' + body + '</p>';
                }
                
                html += '</div>'; // End email body container
                
                // Footer
                html += '<div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e1e8ed; text-align: center;">';
                
                if (footerText) {
                    html += '<p style="color: #646970; margin: 0 0 15px 0; font-size: 14px;">' + footerText + '</p>';
                }
                
                var phone = $('#ccs_email_contact_phone').val();
                var email = $('#ccs_email_contact_email').val();
                if (phone || email) {
                    html += '<p style="margin: 10px 0; font-size: 14px; color: #646970;">';
                    if (phone) html += '<span style="margin: 0 10px;">📞 ' + phone + '</span>';
                    if (email) html += '<span style="margin: 0 10px;">✉️ ' + email + '</span>';
                    html += '</p>';
                }
                
                // Social media icons
                var facebook = $('#ccs_email_facebook').val();
                var twitter = $('#ccs_email_twitter').val();
                var instagram = $('#ccs_email_instagram').val();
                var linkedin = $('#ccs_email_linkedin').val();
                
                if (facebook || twitter || instagram || linkedin) {
                    html += '<div style="margin-top: 20px;">';
                    if (facebook) html += '<a href="' + facebook + '" style="display: inline-block; margin: 0 8px; color: #0073aa; text-decoration: none; font-size: 20px;">📘</a>';
                    if (twitter) html += '<a href="' + twitter + '" style="display: inline-block; margin: 0 8px; color: #0073aa; text-decoration: none; font-size: 20px;">🐦</a>';
                    if (instagram) html += '<a href="' + instagram + '" style="display: inline-block; margin: 0 8px; color: #0073aa; text-decoration: none; font-size: 20px;">📷</a>';
                    if (linkedin) html += '<a href="' + linkedin + '" style="display: inline-block; margin: 0 8px; color: #0073aa; text-decoration: none; font-size: 20px;">💼</a>';
                    html += '</div>';
                }
                
                html += '</div>'; // End footer
                html += '</div>'; // End main container
                
                return html;
            }
            
            // Generate admin email preview HTML
            function generateAdminPreview() {
                var colors = {
                    header: $('input[name="ccs_admin_email_colors[header]"]').val() || '#d63638',
                    button: $('input[name="ccs_admin_email_colors[button]"]').val() || '#0073aa',
                    background: $('input[name="ccs_admin_email_colors[background]"]').val() || '#f4f4f4',
                    text: $('input[name="ccs_admin_email_colors[text]"]').val() || '#333333'
                };
                
                var heading = $('#ccs_admin_email_heading').val() || '📬 New CCS Submission';
                var intro = $('#ccs_admin_email_intro').val() || 'A new Child Care Subsidy calculation has been submitted.';
                var footerText = $('#ccs_admin_email_footer').val() || 'This is an automated notification from your website.';
                
                var html = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: ' + colors.background + '; padding: 20px;">';
                
                // Header
                html += '<div style="background: linear-gradient(135deg, ' + colors.header + ' 0%, #a02123 100%); padding: 30px; text-align: center; border-radius: 8px 8px 0 0;">';
                html += '<h1 style="color: #ffffff; margin: 0; font-size: 28px;">' + heading + '</h1>';
                html += '<p style="color: #ffffff; margin: 10px 0 0 0; opacity: 0.9;">Admin Notification</p>';
                html += '</div>';
                
                // Content container
                html += '<div style="background: #fff; padding: 30px; border-radius: 0 0 8px 8px;">';
                
                // Introduction
                if (intro) {
                    html += '<p style="color: ' + colors.text + '; margin: 0 0 20px 0; font-size: 14px;">' + intro + '</p>';
                }
                
                // User Information
                html += '<h2 style="color: #333; margin: 0 0 15px 0; font-size: 20px;">User Information</h2>';
                html += '<table style="width: 100%; border: 1px solid #e9ecef; border-radius: 4px; margin-bottom: 25px;">';
                html += '<tr style="background: #f8f9fa;"><td style="padding: 10px; font-weight: bold; color: #495057; width: 30%;">Name:</td><td style="padding: 10px; color: #212529;">John Smith</td></tr>';
                html += '<tr><td style="padding: 10px; font-weight: bold; color: #495057;">Email:</td><td style="padding: 10px;"><a href="mailto:john@example.com" style="color: #0073aa; text-decoration: none;">john@example.com</a></td></tr>';
                html += '<tr style="background: #f8f9fa;"><td style="padding: 10px; font-weight: bold; color: #495057;">Phone:</td><td style="padding: 10px; color: #212529;">0412 345 678</td></tr>';
                html += '<tr><td style="padding: 10px; font-weight: bold; color: #495057;">Submitted:</td><td style="padding: 10px; color: #212529;">October 8, 2025 10:45 am</td></tr>';
                html += '</table>';
                
                // Location Details
                html += '<h2 style="color: #333; margin: 0 0 15px 0; font-size: 20px;">📍 Location & Details</h2>';
                html += '<table style="width: 100%; border: 1px solid #e9ecef; border-radius: 4px; margin-bottom: 25px;">';
                html += '<tr style="background: #f8f9fa;"><td style="padding: 10px; font-weight: bold; color: #495057; width: 40%;">Location:</td><td style="padding: 10px; color: #212529;">Sydney, NSW</td></tr>';
                html += '<tr><td style="padding: 10px; font-weight: bold; color: #495057;">ATSI Status:</td><td style="padding: 10px; color: #212529;">No</td></tr>';
                html += '<tr style="background: #f8f9fa;"><td style="padding: 10px; font-weight: bold; color: #495057;">Enrolment Option:</td><td style="padding: 10px; color: #212529;">Full-time</td></tr>';
                html += '</table>';
                
                // Calculation Summary
                html += '<h2 style="color: #333; margin: 0 0 15px 0; font-size: 20px;">Calculation Summary</h2>';
                html += '<div style="background: #f8f9fa; padding: 20px; border-radius: 6px; border-left: 4px solid ' + colors.button + '; margin-bottom: 25px;">';
                html += '<p style="margin: 5px 0;"><strong>Total Fees:</strong> $450.00 per fortnight</p>';
                html += '<p style="margin: 5px 0;"><strong>Estimated Subsidy:</strong> $315.00 per fortnight</p>';
                html += '<p style="margin: 5px 0; color: ' + colors.button + '; font-size: 16px;"><strong>Out-of-pocket:</strong> $135.00 per fortnight</p>';
                html += '</div>';
                
                // Footer
                if (footerText) {
                    html += '<div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e9ecef;">';
                    html += '<p style="margin: 0; font-size: 13px; color: #666; text-align: center;">' + footerText + '</p>';
                    html += '</div>';
                }
                
                html += '</div>'; // End content container
                html += '</div>'; // End main container
                
                return html;
            }
            
            // Update admin preview
            function updateAdminPreview() {
                var previewHtml = generateAdminPreview();
                $('#admin-email-live-preview').html(previewHtml);
            }
            
            // Update preview when form values change
            $('input, select, textarea').on('change input', function() {
                var activeTemplate = $('.nav-tab-active').data('template');
                if (activeTemplate === 'user') {
                    updateLivePreview();
                } else {
                    updateAdminPreview();
                }
            });
            
            // Initialize preview
            updateLivePreview();
            updateAdminPreview();
            
            // Reset template button
            $('#reset-template').on('click', function() {
                if (confirm('Are you sure you want to reset the email template to default settings? This cannot be undone.')) {
                    $('#email-template-form')[0].reset();
                    $('#header-image-dropzone').html(
                        '<div class="upload-placeholder">' +
                        '<span class="dashicons dashicons-format-image"></span>' +
                        '<p>Click or drag image here</p>' +
                        '<small>Recommended: 600px × 170px</small>' +
                        '</div>'
                    );
                    updateLivePreview();
                }
            });
            
            // Send test email button
            $('#send-test-email').on('click', function() {
                var email = prompt('Enter email address to send test email to:', '<?php echo esc_js(get_option('admin_email')); ?>');
                if (email) {
                    alert('Test email functionality would be implemented here.\nEmail would be sent to: ' + email);
                    // TODO: Implement AJAX call to send test email
                }
            });
        });
        </script>
        <?php
    }

    public function childcare_ccs_shortcode_page()
    {
        ?>
        <div class="wrap ccs-shortcode-page">
            <!-- Header -->
            <div style="background:linear-gradient(135deg, #0073aa 0%, #005177 100%); color:#fff; padding:30px; border-radius:8px; margin-bottom:30px;">
                <h1 style="color:#fff; margin:0 0 10px 0; font-size:32px;">
                    <span class="dashicons dashicons-shortcode" style="font-size:36px; width:36px; height:36px; margin-right:10px;"></span>
                    How to Use the Calculator
                </h1>
                <p style="font-size:18px; margin:0; opacity:0.95;">Step-by-step guide to add the calculator to your website</p>
            </div>

            <!-- Shortcode Box -->
            <div style="background:#fff; padding:30px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); margin-bottom:30px;">
                <h2 style="margin:0 0 20px 0; color:#0073aa; font-size:24px;">
                    <span class="dashicons dashicons-editor-code" style="margin-right:8px;"></span>
                    Your Shortcode
                </h2>
                <div style="background:#f8f9fa; border:2px dashed #0073aa; padding:20px; border-radius:8px; text-align:center; margin-bottom:20px;">
                    <code style="font-size:24px; font-weight:700; color:#0073aa; font-family:monospace;">[thechildcare_ccs_calculator]</code>
                </div>
                <div style="text-align:center;">
                    <button onclick="navigator.clipboard.writeText('[thechildcare_ccs_calculator]'); this.textContent='✓ Copied!'; setTimeout(() => this.textContent='Copy Shortcode', 2000);" class="button button-primary button-large" style="padding:12px 30px; font-size:16px;">
                        <span class="dashicons dashicons-clipboard" style="margin-right:5px;"></span>
                        Copy Shortcode
                    </button>
                </div>
            </div>

            <!-- Step-by-Step Guide -->
            <div style="margin-bottom:30px;">
                <h2 style="color:#0073aa; font-size:24px; margin-bottom:20px;">
                    <span class="dashicons dashicons-list-view" style="margin-right:8px;"></span>
                    Installation Steps
                </h2>

                <!-- Step 1 -->
                <div style="background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); margin-bottom:20px; border-left:4px solid #0073aa;">
                    <div style="display:flex; align-items:flex-start; gap:20px;">
                        <div style="background:#0073aa; color:#fff; width:50px; height:50px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:24px; flex-shrink:0;">1</div>
                        <div style="flex:1;">
                            <h3 style="margin:0 0 10px 0; color:#0073aa; font-size:20px;">Create a New Page</h3>
                            <p style="margin:0 0 15px 0; color:#666; line-height:1.6;">Go to <strong>Pages → Add New</strong> in your WordPress admin panel.</p>
                            <ul style="margin:0; padding-left:20px; color:#666; line-height:1.8;">
                                <li>Give your page a title (e.g., "Child Care Subsidy Calculator" or "CCS Calculator")</li>
                                <li>You can also add this to an existing page if you prefer</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div style="background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); margin-bottom:20px; border-left:4px solid #00a32a;">
                    <div style="display:flex; align-items:flex-start; gap:20px;">
                        <div style="background:#00a32a; color:#fff; width:50px; height:50px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:24px; flex-shrink:0;">2</div>
                        <div style="flex:1;">
                            <h3 style="margin:0 0 10px 0; color:#00a32a; font-size:20px;">Add the Shortcode</h3>
                            <p style="margin:0 0 15px 0; color:#666; line-height:1.6;">In the page editor, add the shortcode where you want the calculator to appear.</p>
                            <div style="background:#f8f9fa; padding:15px; border-radius:4px; border-left:3px solid #00a32a; margin-bottom:15px;">
                                <p style="margin:0 0 10px 0; font-weight:600;">For Block Editor (Gutenberg):</p>
                                <ol style="margin:0; padding-left:20px; line-height:1.8;">
                                    <li>Click the <strong>+</strong> button to add a new block</li>
                                    <li>Search for "Shortcode" block</li>
                                    <li>Paste <code>[thechildcare_ccs_calculator]</code> into the block</li>
                                </ol>
                            </div>
                            <div style="background:#f8f9fa; padding:15px; border-radius:4px; border-left:3px solid #00a32a;">
                                <p style="margin:0 0 10px 0; font-weight:600;">For Classic Editor:</p>
                                <ol style="margin:0; padding-left:20px; line-height:1.8;">
                                    <li>Simply paste <code>[thechildcare_ccs_calculator]</code> anywhere in the content</li>
                                    <li>The shortcode will be replaced with the calculator when published</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div style="background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); margin-bottom:20px; border-left:4px solid #f0b849;">
                    <div style="display:flex; align-items:flex-start; gap:20px;">
                        <div style="background:#f0b849; color:#fff; width:50px; height:50px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:24px; flex-shrink:0;">3</div>
                        <div style="flex:1;">
                            <h3 style="margin:0 0 10px 0; color:#f0b849; font-size:20px;">Configure Settings (Optional)</h3>
                            <p style="margin:0 0 15px 0; color:#666; line-height:1.6;">Before publishing, you may want to configure the calculator settings.</p>
                            <ul style="margin:0; padding-left:20px; color:#666; line-height:1.8;">
                                <li><a href="?page=child-care-settings">Calculator Settings</a> - Set income thresholds and hourly caps</li>
                                <li><a href="?page=child-care-styling">Appearance Settings</a> - Customize colors and fonts</li>
                                <li><a href="?page=child-care-email">Email Template</a> - Configure email design</li>
                                <li><a href="?page=child-care-hubspot">Form Integration</a> - Setup HubSpot or custom form</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div style="background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); margin-bottom:20px; border-left:4px solid #d63638;">
                    <div style="display:flex; align-items:flex-start; gap:20px;">
                        <div style="background:#d63638; color:#fff; width:50px; height:50px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:24px; flex-shrink:0;">4</div>
                        <div style="flex:1;">
                            <h3 style="margin:0 0 10px 0; color:#d63638; font-size:20px;">Publish Your Page</h3>
                            <p style="margin:0 0 15px 0; color:#666; line-height:1.6;">Click the <strong>Publish</strong> button to make your calculator live.</p>
                            <ul style="margin:0; padding-left:20px; color:#666; line-height:1.8;">
                                <li>Preview the page first to see how it looks</li>
                                <li>Test the calculator with sample data</li>
                                <li>Ensure email delivery is working (test with your own email)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Step 5 -->
                <div style="background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); border-left:4px solid #7e3bd0;">
                    <div style="display:flex; align-items:flex-start; gap:20px;">
                        <div style="background:#7e3bd0; color:#fff; width:50px; height:50px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:24px; flex-shrink:0;">5</div>
                        <div style="flex:1;">
                            <h3 style="margin:0 0 10px 0; color:#7e3bd0; font-size:20px;">Add to Navigation (Optional)</h3>
                            <p style="margin:0 0 15px 0; color:#666; line-height:1.6;">Make it easy for visitors to find your calculator.</p>
                            <ul style="margin:0; padding-left:20px; color:#666; line-height:1.8;">
                                <li>Go to <strong>Appearance → Menus</strong></li>
                                <li>Add your calculator page to the main menu</li>
                                <li>Consider adding it to the header or footer for easy access</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Options -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:30px;">
                
                <!-- Where to Use -->
                <div style="background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="margin:0 0 15px 0; color:#0073aa; font-size:18px; border-bottom:2px solid #0073aa; padding-bottom:10px;">
                        <span class="dashicons dashicons-location" style="margin-right:8px;"></span>
                        Where Can You Use It?
                    </h3>
                    <ul style="margin:0; padding-left:20px; line-height:1.8; color:#666;">
                        <li><strong>Pages</strong> - Dedicated calculator page</li>
                        <li><strong>Posts</strong> - Within blog articles</li>
                        <li><strong>Widgets</strong> - Sidebar or footer (if theme supports shortcodes)</li>
                        <li><strong>Custom Templates</strong> - Using do_shortcode() function</li>
                        <li><strong>Page Builders</strong> - Elementor, WPBakery, etc.</li>
                    </ul>
                </div>

                <!-- Tips & Best Practices -->
                <div style="background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="margin:0 0 15px 0; color:#0073aa; font-size:18px; border-bottom:2px solid #0073aa; padding-bottom:10px;">
                        <span class="dashicons dashicons-lightbulb" style="margin-right:8px;"></span>
                        Tips & Best Practices
                    </h3>
                    <ul style="margin:0; padding-left:20px; line-height:1.8; color:#666;">
                        <li>Use a <strong>full-width page template</strong> for better display</li>
                        <li>Add introductory text above the calculator</li>
                        <li>Include a disclaimer about estimates</li>
                        <li>Test on mobile devices for responsiveness</li>
                        <li>Monitor submissions in the admin panel</li>
                    </ul>
                </div>
            </div>

            <!-- Troubleshooting -->
            <div style="background:#fff3cd; padding:25px; border-left:4px solid #f0b849; border-radius:4px; margin-bottom:30px;">
                <h3 style="margin:0 0 15px 0; color:#856404; font-size:20px;">
                    <span class="dashicons dashicons-sos" style="margin-right:8px;"></span>
                    Troubleshooting
                </h3>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    <div>
                        <p style="margin:0 0 10px 0; color:#856404; font-weight:600;">Calculator not showing?</p>
                        <ul style="margin:0; padding-left:20px; color:#856404; line-height:1.6;">
                            <li>Check if shortcode is spelled correctly</li>
                            <li>Ensure plugin is activated</li>
                            <li>Clear browser and WordPress cache</li>
                            <li>Check for JavaScript errors in browser console</li>
                        </ul>
                    </div>
                    <div>
                        <p style="margin:0 0 10px 0; color:#856404; font-weight:600;">Emails not sending?</p>
                        <ul style="margin:0; padding-left:20px; color:#856404; line-height:1.6;">
                            <li>Install WP Mail SMTP plugin</li>
                            <li>Check spam folder</li>
                            <li>Verify email settings in Form Integration</li>
                            <li>Test with different email addresses</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Need Help -->
            <div style="background:#e7f5fe; padding:25px; border-radius:8px; border-left:4px solid #0073aa; text-align:center;">
                <h3 style="margin:0 0 15px 0; color:#0073aa; font-size:20px;">
                    <span class="dashicons dashicons-sos" style="margin-right:8px;"></span>
                    Need Help?
                </h3>
                <p style="margin:0 0 20px 0; color:#666; font-size:16px;">If you need assistance or have questions, we're here to help!</p>
                <div style="display:flex; gap:15px; justify-content:center; flex-wrap:wrap;">
                    <a href="https://i9.edu.au/" target="_blank" class="button button-primary button-large">
                        <span class="dashicons dashicons-admin-site" style="margin-right:5px;"></span>
                        Visit i9 Education
                    </a>
                    <a href="?page=child-care-ccs" class="button button-secondary button-large">
                        <span class="dashicons dashicons-dashboard" style="margin-right:5px;"></span>
                        Back to Dashboard
                    </a>
                    <a href="?page=child-care-settings" class="button button-secondary button-large">
                        <span class="dashicons dashicons-admin-settings" style="margin-right:5px;"></span>
                        Configure Settings
                    </a>
                </div>
            </div>
        </div>

        <style>
            .ccs-shortcode-page code {
                background: #f8f9fa;
                padding: 2px 6px;
                border-radius: 3px;
                font-family: monospace;
                color: #d63638;
            }
            .ccs-shortcode-page .button {
                transition: all 0.3s ease;
            }
            .ccs-shortcode-page .button:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            }
        </style>
        <?php
    }

    public function childcare_ccs_main_page()
    {
        ?>
        <div class="wrap ccs-dashboard">
            <!-- Header Section -->
            <div style="background:linear-gradient(135deg, #0073aa 0%, #005177 100%); color:#fff; padding:30px; border-radius:8px; margin-bottom:30px;">
                <h1 style="color:#fff; margin:0 0 10px 0; font-size:32px;">
                    <span class="dashicons dashicons-calculator" style="font-size:36px; width:36px; height:36px; margin-right:10px;"></span>
                    Child Care Subsidy Calculator
                </h1>
                <p style="font-size:18px; margin:0; opacity:0.95;">
                    <?php 
                    $plugin_data = get_file_data(CCS_CALCULATOR_PLUGIN_DIR . 'childcare-subsidy-calculator.php', ['Version' => 'Version']);
                    $current_version = $plugin_data['Version'] ?? '2.0.0';
                    echo 'Version ' . esc_html($current_version) . ' - Professional CCS estimation tool for Australian families';
                    ?>
                </p>
            </div>

            <!-- Stats Cards -->
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:20px; margin-bottom:30px;">
                <div style="background:#fff; padding:25px; border-radius:8px; border-left:4px solid #0073aa; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <div style="display:flex; align-items:center; justify-content:space-between;">
                        <div>
                            <h3 style="margin:0; color:#666; font-size:14px; text-transform:uppercase;">Total Submissions</h3>
                            <p style="margin:10px 0 0 0; font-size:32px; font-weight:700; color:#0073aa;">
                                <?php 
                                // Clear any cached counts and get fresh data
                                wp_cache_delete('ccs_submission', 'counts');
                                $count = wp_count_posts('ccs_submission');
                                
                                $total = 0;
                                if (is_object($count)) {
                                    $total = isset($count->publish) ? intval($count->publish) : 0;
                                    
                                    // If no published posts, check other statuses
                                    if ($total === 0) {
                                        $draft = isset($count->draft) ? intval($count->draft) : 0;
                                        $private = isset($count->private) ? intval($count->private) : 0;
                                        $total = $draft + $private;
                                    }
                                }
                                
                                // Fallback: Direct database query
                                if ($total === 0) {
                                    global $wpdb;
                                    $total = intval($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'ccs_submission' AND post_status IN ('publish', 'draft', 'private')"));
                                }
                                
                                echo number_format($total);
                                ?>
                            </p>
                        </div>
                        <span class="dashicons dashicons-chart-line" style="font-size:48px; color:#0073aa; opacity:0.2;"></span>
                    </div>
                </div>

                <div style="background:#fff; padding:25px; border-radius:8px; border-left:4px solid #00a32a; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <div style="display:flex; align-items:center; justify-content:space-between;">
                        <div>
                            <h3 style="margin:0; color:#666; font-size:14px; text-transform:uppercase;">Major Features</h3>
                            <p style="margin:10px 0 0 0; font-size:32px; font-weight:700; color:#00a32a;">36</p>
                        </div>
                        <span class="dashicons dashicons-star-filled" style="font-size:48px; color:#00a32a; opacity:0.2;"></span>
                    </div>
                </div>

                <div style="background:#fff; padding:25px; border-radius:8px; border-left:4px solid #f0b849; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <div style="display:flex; align-items:center; justify-content:space-between;">
                        <div>
                            <h3 style="margin:0; color:#666; font-size:14px; text-transform:uppercase;">Suburbs Database</h3>
                            <p style="margin:10px 0 0 0; font-size:32px; font-weight:700; color:#f0b849;">18K+</p>
                        </div>
                        <span class="dashicons dashicons-location" style="font-size:48px; color:#f0b849; opacity:0.2;"></span>
                    </div>
                </div>

                <div style="background:#fff; padding:25px; border-radius:8px; border-left:4px solid #d63638; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <div style="display:flex; align-items:center; justify-content:space-between;">
                        <div>
                            <h3 style="margin:0; color:#666; font-size:14px; text-transform:uppercase;">Plugin Version</h3>
                            <p style="margin:10px 0 0 0; font-size:32px; font-weight:700; color:#d63638;">
                                <?php 
                                $plugin_data = get_file_data(CCS_CALCULATOR_PLUGIN_DIR . 'childcare-subsidy-calculator.php', ['Version' => 'Version']);
                                echo esc_html($plugin_data['Version'] ?? '2.0.0');
                                ?>
                            </p>
                        </div>
                        <span class="dashicons dashicons-update" style="font-size:48px; color:#d63638; opacity:0.2;"></span>
                    </div>
                </div>
            </div>

            <!-- System Requirements -->
            <div style="background:#fff; padding:15px 20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); margin-bottom:30px;">
                <?php
                // Get plugin requirements
                $plugin_file = CCS_CALCULATOR_PLUGIN_DIR . 'childcare-subsidy-calculator.php';
                $plugin_data = get_file_data($plugin_file, [
                    'RequiresWP' => 'Requires at least',
                    'RequiresPHP' => 'Requires PHP'
                ]);
                
                global $wp_version;
                $required_wp = $plugin_data['RequiresWP'] ?? '5.0';
                $required_php = $plugin_data['RequiresPHP'] ?? '7.4';
                $current_wp = $wp_version;
                $current_php = PHP_VERSION;
                
                $wp_ok = version_compare($current_wp, $required_wp, '>=');
                $php_ok = version_compare($current_php, $required_php, '>=');
                $all_ok = $wp_ok && $php_ok;
                ?>
                
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:15px;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span class="dashicons dashicons-admin-tools" style="font-size:24px; color:#0073aa;"></span>
                        <strong style="font-size:16px; color:#333;">System Requirements</strong>
                    </div>
                    
                    <div style="display:flex; gap:25px; flex-wrap:wrap;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span class="dashicons dashicons-wordpress" style="color:<?php echo $wp_ok ? '#00a32a' : '#d63638'; ?>;"></span>
                            <span style="font-size:13px;">
                                <strong>WordPress:</strong> <?php echo esc_html($required_wp); ?>+ 
                                <span style="color:<?php echo $wp_ok ? '#00a32a' : '#d63638'; ?>; font-weight:600;">(<?php echo esc_html($current_wp); ?>)</span>
                                <?php echo $wp_ok ? '✓' : '✗'; ?>
                            </span>
                        </div>
                        
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span class="dashicons dashicons-media-code" style="color:<?php echo $php_ok ? '#00a32a' : '#d63638'; ?>;"></span>
                            <span style="font-size:13px;">
                                <strong>PHP:</strong> <?php echo esc_html($required_php); ?>+ 
                                <span style="color:<?php echo $php_ok ? '#00a32a' : '#d63638'; ?>; font-weight:600;">(<?php echo esc_html($current_php); ?>)</span>
                                <?php echo $php_ok ? '✓' : '✗'; ?>
                            </span>
                        </div>
                        
                        <div style="padding:5px 15px; background:<?php echo $all_ok ? '#d4edda' : '#f8d7da'; ?>; border-radius:4px;">
                            <span style="color:<?php echo $all_ok ? '#155724' : '#721c24'; ?>; font-weight:600; font-size:13px;">
                                <?php echo $all_ok ? '✓ Compatible' : '⚠ Update Required'; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Features Grid -->
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:20px; margin-bottom:30px;">
                
                <!-- Submissions Management -->
                <div style="background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <h2 style="margin:0 0 15px 0; color:#0073aa; font-size:20px; border-bottom:2px solid #0073aa; padding-bottom:10px;">
                        <span class="dashicons dashicons-list-view" style="margin-right:8px;"></span>
                        Submissions Management
                    </h2>
                    <ul style="line-height:1.8; margin:0; padding-left:20px;">
                        <li>View all calculator submissions</li>
                        <li>Detailed user contact information</li>
                        <li>Location & enrolment tracking</li>
                        <li>Full calculation summary</li>
                        <li>Delete & manage submissions</li>
                    </ul>
                    <a href="?page=ccs-submissions" class="button button-primary" style="margin-top:15px; width:100%; text-align:center;">
                        View Submissions →
                    </a>
                </div>

                <!-- Email Templates -->
                <div style="background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <h2 style="margin:0 0 15px 0; color:#0073aa; font-size:20px; border-bottom:2px solid #0073aa; padding-bottom:10px;">
                        <span class="dashicons dashicons-email" style="margin-right:8px;"></span>
                        Professional Email Templates
                    </h2>
                    <ul style="line-height:1.8; margin:0; padding-left:20px;">
                        <li>Beautiful HTML email design</li>
                        <li>Custom header image upload</li>
                        <li>Social media integration</li>
                        <li>Contact info customization</li>
                        <li>Mobile-responsive design</li>
                    </ul>
                    <a href="?page=child-care-email" class="button button-primary" style="margin-top:15px; width:100%; text-align:center;">
                        Configure Emails →
                    </a>
                </div>

                <!-- Appearance Settings -->
                <div style="background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <h2 style="margin:0 0 15px 0; color:#0073aa; font-size:20px; border-bottom:2px solid #0073aa; padding-bottom:10px;">
                        <span class="dashicons dashicons-admin-appearance" style="margin-right:8px;"></span>
                        Appearance Customization
                    </h2>
                    <ul style="line-height:1.8; margin:0; padding-left:20px;">
                        <li>6 organized styling tabs</li>
                        <li>10+ Google Fonts selection</li>
                        <li>RGBA color support</li>
                        <li>Button & input styling</li>
                        <li>Progress bar customization</li>
                    </ul>
                    <a href="?page=child-care-styling" class="button button-primary" style="margin-top:15px; width:100%; text-align:center;">
                        Customize Appearance →
                    </a>
                </div>

                <!-- Form Integration -->
                <div style="background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <h2 style="margin:0 0 15px 0; color:#0073aa; font-size:20px; border-bottom:2px solid #0073aa; padding-bottom:10px;">
                        <span class="dashicons dashicons-forms" style="margin-right:8px;"></span>
                        Form Integration
                    </h2>
                    <ul style="line-height:1.8; margin:0; padding-left:20px;">
                        <li>HubSpot CRM integration</li>
                        <li>Custom SMTP form option</li>
                        <li>Auto email delivery</li>
                        <li>Step 1 data collection</li>
                        <li>Database submission tracking</li>
                    </ul>
                    <a href="?page=child-care-hubspot" class="button button-primary" style="margin-top:15px; width:100%; text-align:center;">
                        Setup Integration →
                    </a>
                </div>

                <!-- Calculator Settings -->
                <div style="background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <h2 style="margin:0 0 15px 0; color:#0073aa; font-size:20px; border-bottom:2px solid #0073aa; padding-bottom:10px;">
                        <span class="dashicons dashicons-admin-settings" style="margin-right:8px;"></span>
                        Calculator Settings
                    </h2>
                    <ul style="line-height:1.8; margin:0; padding-left:20px;">
                        <li>Income thresholds (90% to 0%)</li>
                        <li>Hourly rate caps by care type</li>
                        <li>$5,000 income step per 1%</li>
                        <li>Disclaimer text customization</li>
                        <li>Policy update tracking</li>
                    </ul>
                    <a href="?page=child-care-settings" class="button button-primary" style="margin-top:15px; width:100%; text-align:center;">
                        Configure Settings →
                    </a>
                </div>

                <!-- Suburbs Database -->
                <div style="background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <h2 style="margin:0 0 15px 0; color:#0073aa; font-size:20px; border-bottom:2px solid #0073aa; padding-bottom:10px;">
                        <span class="dashicons dashicons-location-alt" style="margin-right:8px;"></span>
                        Suburbs Database
                    </h2>
                    <ul style="line-height:1.8; margin:0; padding-left:20px;">
                        <li>18,000+ Australian suburbs</li>
                        <li>Real-time autocomplete search</li>
                        <li>Postcode lookup support</li>
                        <li>Easy import/update tools</li>
                        <li>All states & territories</li>
                    </ul>
                    <a href="?page=child-care-suburbs" class="button button-primary" style="margin-top:15px; width:100%; text-align:center;">
                        Manage Suburbs →
                    </a>
                </div>
            </div>

            <!-- Quick Start Guide -->
            <div style="background:#e7f5fe; padding:25px; border-radius:8px; border-left:4px solid #0073aa; margin-bottom:30px;">
                <h2 style="margin:0 0 20px 0; color:#0073aa;">
                    <span class="dashicons dashicons-flag" style="margin-right:8px;"></span>
                    Quick Start Guide
                </h2>
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:20px;">
                    <div>
                        <div style="background:#0073aa; color:#fff; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:20px; margin-bottom:10px;">1</div>
                        <h3 style="margin:0 0 5px 0; font-size:16px;">Configure Settings</h3>
                        <p style="margin:0; color:#666; font-size:14px;">Set income thresholds and hourly caps</p>
                    </div>
                    <div>
                        <div style="background:#0073aa; color:#fff; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:20px; margin-bottom:10px;">2</div>
                        <h3 style="margin:0 0 5px 0; font-size:16px;">Setup Email Template</h3>
                        <p style="margin:0; color:#666; font-size:14px;">Upload header image and configure contacts</p>
                    </div>
                    <div>
                        <div style="background:#0073aa; color:#fff; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:20px; margin-bottom:10px;">3</div>
                        <h3 style="margin:0 0 5px 0; font-size:16px;">Customize Appearance</h3>
                        <p style="margin:0; color:#666; font-size:14px;">Choose fonts, colors, and styling</p>
                    </div>
                    <div>
                        <div style="background:#0073aa; color:#fff; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:20px; margin-bottom:10px;">4</div>
                        <h3 style="margin:0 0 5px 0; font-size:16px;">Add Shortcode</h3>
                        <p style="margin:0; color:#666; font-size:14px;">Place <code>[thechildcare_ccs_calculator]</code> on page</p>
                    </div>
                    <div>
                        <div style="background:#0073aa; color:#fff; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:20px; margin-bottom:10px;">5</div>
                        <h3 style="margin:0 0 5px 0; font-size:16px;">Test & Launch</h3>
                        <p style="margin:0; color:#666; font-size:14px;">Test calculator and go live!</p>
                    </div>
                </div>
            </div>

            <!-- Resources & Links -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:30px;">
                <div style="background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <h2 style="margin:0 0 20px 0; color:#0073aa;">
                        <span class="dashicons dashicons-book" style="margin-right:8px;"></span>
                        Documentation
                    </h2>
                    <ul style="line-height:2; margin:0;">
                        <li><a href="?page=child-care-shortcode">📖 How to Use Guide</a></li>
                        <li><a href="?page=child-care-changelog">📋 Changelog & Version History</a></li>
                        <li><a href="https://www.servicesaustralia.gov.au/child-care-subsidy" target="_blank">🏛️ Services Australia - Official CCS Info</a></li>
                        <li><a href="https://i9.edu.au/" target="_blank">🎓 i9 Education - Support & Documentation</a></li>
                    </ul>
                </div>

                <div style="background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <h2 style="margin:0 0 20px 0; color:#0073aa;">
                        <span class="dashicons dashicons-admin-tools" style="margin-right:8px;"></span>
                        Quick Actions
                    </h2>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <a href="?page=ccs-submissions" class="button button-secondary" style="text-align:center;">
                            <span class="dashicons dashicons-list-view" style="margin-right:5px;"></span>
                            View All Submissions
                        </a>
                        <a href="?page=child-care-email" class="button button-secondary" style="text-align:center;">
                            <span class="dashicons dashicons-email" style="margin-right:5px;"></span>
                            Configure Email Template
                        </a>
                        <a href="?page=child-care-styling" class="button button-secondary" style="text-align:center;">
                            <span class="dashicons dashicons-admin-appearance" style="margin-right:5px;"></span>
                            Customize Appearance
                        </a>
                    </div>
                </div>
            </div>

            <!-- Important Notes -->
            <div style="background:#fff3cd; padding:20px; border-left:4px solid #f0b849; border-radius:4px; margin-bottom:20px;">
                <h3 style="margin:0 0 10px 0; color:#856404;">
                    <span class="dashicons dashicons-info" style="margin-right:5px;"></span>
                    Important Information
                </h3>
                <ul style="margin:0; padding-left:20px; color:#856404; line-height:1.8;">
                    <li><strong>Email Delivery:</strong> Install WP Mail SMTP plugin for reliable email delivery</li>
                    <li><strong>Policy Updates:</strong> Check Services Australia for latest CCS rates and update settings accordingly</li>
                    <li><strong>Disclaimer:</strong> Calculator provides estimates only - final amounts determined by Services Australia</li>
                    <li><strong>Support:</strong> Visit <a href="https://i9.edu.au/" target="_blank" style="color:#856404; text-decoration:underline;">i9 Education</a> for documentation and support</li>
                </ul>
            </div>

            <!-- Footer Info -->
            <div style="background:#f0f0f1; padding:20px; border-radius:8px; text-align:center;">
                <p style="margin:0; color:#666;">
                    <strong>The Child Care Subsidy Calculator</strong> v2.0.0 | 
                    Developed by <a href="https://i9.edu.au/" target="_blank">i9 Education</a> | 
                    <a href="?page=child-care-changelog">View Changelog</a>
                </p>
            </div>
        </div>

        <style>
            .ccs-dashboard .button {
                transition: all 0.3s ease;
            }
            .ccs-dashboard .button:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            }
        </style>
        <?php
    }

    public function childcare_ccs_submissions_page()
    {
        if (!current_user_can('manage_options')) return;
        
        // Handle view action - show single submission
        if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['post_id'])) {
            $this->view_single_submission(intval($_GET['post_id']));
            return;
        }
        
        // Handle flush rewrite rules
        if (isset($_GET['action']) && $_GET['action'] === 'flush_rewrites') {
            flush_rewrite_rules();
            echo '<div class="notice notice-success is-dismissible"><p><strong>✓ Rewrite rules flushed!</strong> You can now view submissions.</p></div>';
        }
        
        // Handle delete action
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['post_id'])) {
            $post_id = intval($_GET['post_id']);
            wp_delete_post($post_id, true);
            echo '<div class="notice notice-success is-dismissible"><p>Submission deleted successfully.</p></div>';
        }
        
        // Get all submissions
        $args = [
            'post_type' => 'ccs_submission',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        ];
        
        $submissions = get_posts($args);
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">CCS Submissions</h1>
            <hr class="wp-header-end">
            
            <?php if (empty($submissions)): ?>
                <div style="background:#fff; padding:40px; text-align:center; border:1px solid #ccc; border-radius:8px; margin-top:20px;">
                    <p style="font-size:18px; color:#666;">📋 No submissions yet</p>
                    <p style="color:#999;">Submissions will appear here when users complete the calculator.</p>
                </div>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width:30%;">Name</th>
                            <th style="width:25%;">Email</th>
                            <th style="width:15%;">Phone</th>
                            <th style="width:20%;">Date</th>
                            <th style="width:10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $submission): 
                            $user_email = get_post_meta($submission->ID, 'user_email', true);
                            $user_phone = get_post_meta($submission->ID, 'user_phone', true);
                            $submission_date = get_post_meta($submission->ID, 'submission_date', true);
                            if (empty($submission_date)) {
                                $submission_date = $submission->post_date;
                            }
                        ?>
                        <tr>
                            <td>
                                <strong>
                                    <a href="<?php echo admin_url('admin.php?page=ccs-submissions&action=view&post_id=' . $submission->ID); ?>">
                                        <?php echo esc_html($submission->post_title); ?>
                                    </a>
                                </strong>
                            </td>
                            <td>
                                <?php if ($user_email): ?>
                                    <a href="mailto:<?php echo esc_attr($user_email); ?>">
                                        <?php echo esc_html($user_email); ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color:#999;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $user_phone ? esc_html($user_phone) : '<span style="color:#999;">—</span>'; ?>
                            </td>
                            <td>
                                <?php echo date('M j, Y g:i a', strtotime($submission_date)); ?>
                            </td>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=ccs-submissions&action=view&post_id=' . $submission->ID); ?>" class="button button-small">
                                    View
                                </a>
                                <a href="<?php echo admin_url('admin.php?page=ccs-submissions&action=delete&post_id=' . $submission->ID); ?>" 
                                   class="button button-small" 
                                   onclick="return confirm('Are you sure you want to delete this submission?');"
                                   style="color:#b32d2e;">
                                    Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <p style="margin-top:20px; color:#666;">
                    <strong>Total Submissions:</strong> <?php echo count($submissions); ?>
                </p>
            <?php endif; ?>
        </div>
        <?php
    }

    private function view_single_submission($post_id)
    {
        $submission = get_post($post_id);
        
        if (!$submission || $submission->post_type !== 'ccs_submission') {
            echo '<div class="wrap"><div class="notice notice-error"><p>Submission not found.</p></div></div>';
            return;
        }
        
        $user_name = get_post_meta($post_id, 'user_name', true);
        $user_email = get_post_meta($post_id, 'user_email', true);
        $user_phone = get_post_meta($post_id, 'user_phone', true);
        $submission_date = get_post_meta($post_id, 'submission_date', true);
        $location = get_post_meta($post_id, 'location', true);
        $atsi_status = get_post_meta($post_id, 'atsi_status', true);
        $enrolment_option = get_post_meta($post_id, 'enrolment_option', true);
        
        if (empty($submission_date)) {
            $submission_date = $submission->post_date;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html($submission->post_title); ?></h1>
            <a href="<?php echo admin_url('admin.php?page=ccs-submissions'); ?>" class="page-title-action">← Back to All Submissions</a>
            <hr class="wp-header-end">
            
            <div style="display:grid; grid-template-columns:2fr 1fr; gap:20px; margin-top:20px;">
                <!-- Main Content -->
                <div>
                    <div style="background:#fff; padding:20px; border:1px solid #ccc; border-radius:8px;">
                        <h2 style="margin-top:0;">Calculation Summary</h2>
                        <?php echo wp_kses_post($submission->post_content); ?>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div>
                    <div style="background:#fff; padding:20px; border:1px solid #ccc; border-radius:8px; margin-bottom:20px;">
                        <h3 style="margin-top:0;">Contact Details</h3>
                        
                        <p style="margin:15px 0;">
                            <strong>👤 Name:</strong><br>
                            <?php echo $user_name ? esc_html($user_name) : '<span style="color:#999;">Not provided</span>'; ?>
                        </p>
                        
                        <p style="margin:15px 0;">
                            <strong>📧 Email:</strong><br>
                            <?php if ($user_email): ?>
                                <a href="mailto:<?php echo esc_attr($user_email); ?>" style="word-break:break-all;">
                                    <?php echo esc_html($user_email); ?>
                                </a>
                            <?php else: ?>
                                <span style="color:#999;">Not provided</span>
                            <?php endif; ?>
                        </p>
                        
                        <p style="margin:15px 0;">
                            <strong>📱 Phone:</strong><br>
                            <?php if ($user_phone): ?>
                                <a href="tel:<?php echo esc_attr($user_phone); ?>">
                                    <?php echo esc_html($user_phone); ?>
                                </a>
                            <?php else: ?>
                                <span style="color:#999;">Not provided</span>
                            <?php endif; ?>
                        </p>
                        
                        <p style="margin:15px 0;">
                            <strong>📅 Submitted:</strong><br>
                            <?php echo date('F j, Y', strtotime($submission_date)); ?><br>
                            <span style="color:#666; font-size:13px;">
                                <?php echo date('g:i a', strtotime($submission_date)); ?>
                            </span>
                        </p>
                    </div>
                    
                    <div style="background:#fff; padding:20px; border:1px solid #ccc; border-radius:8px; margin-bottom:20px;">
                        <h3 style="margin-top:0;">📍 Location & Details</h3>
                        
                        <p style="margin:15px 0;">
                            <strong>Location:</strong><br>
                            <?php echo $location ? esc_html($location) : '<span style="color:#999;">Not provided</span>'; ?>
                        </p>
                        
                        <p style="margin:15px 0;">
                            <strong>ATSI Status:</strong><br>
                            <?php echo $atsi_status ? ucfirst(esc_html($atsi_status)) : '<span style="color:#999;">Not provided</span>'; ?>
                        </p>
                        
                        <p style="margin:15px 0;">
                            <strong>Enrolment Option:</strong><br>
                            <?php echo $enrolment_option ? esc_html($enrolment_option) : '<span style="color:#999;">Not provided</span>'; ?>
                        </p>
                    </div>
                    
                    <div style="background:#fff; padding:20px; border:1px solid #ccc; border-radius:8px;">
                        <p style="margin:0;">
                            <a href="<?php echo admin_url('admin.php?page=ccs-submissions&action=delete&post_id=' . $post_id); ?>" 
                               class="button button-secondary" 
                               onclick="return confirm('Are you sure you want to delete this submission?');"
                               style="width:100%; text-align:center; color:#b32d2e;">
                                🗑️ Delete Submission
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function childcare_ccs_changelog_page()
    {
        $changelog_file = CCS_CALCULATOR_PLUGIN_DIR . 'CHANGELOG.md';
        $changelog_content = '';
        
        if (file_exists($changelog_file)) {
            $changelog_content = file_get_contents($changelog_file);
        }
        ?>
        <div class="wrap">
            <h1>Changelog - Version History</h1>
            <div style="background:#fff; padding:20px; border:1px solid #ccc; border-radius:4px;">
                <?php if ($changelog_content): ?>
                    <div style="font-family: monospace; white-space: pre-wrap; line-height: 1.6;">
                        <?php echo esc_html($changelog_content); ?>
                    </div>
                <?php else: ?>
                    <p>No changelog available.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}

