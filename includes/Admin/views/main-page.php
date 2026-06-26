<?php
if (!defined('ABSPATH')) { exit; }
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
