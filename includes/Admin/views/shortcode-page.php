<?php
if (!defined('ABSPATH')) { exit; }
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
