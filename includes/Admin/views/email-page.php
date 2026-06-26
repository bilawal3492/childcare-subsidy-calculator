<?php
if (!defined('ABSPATH')) { exit; }
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
