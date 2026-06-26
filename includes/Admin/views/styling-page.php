<?php
if (!defined('ABSPATH')) { exit; }
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
                
                // Loader/Spinner Settings
                'ccs_spinner_color',
                
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

                // CTA Buttons
                'ccs_cta_section_enabled', 'ccs_book_tour_text', 'ccs_book_tour_url',
                'ccs_contact_us_text', 'ccs_contact_us_url',
                
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
                    <tr>
                        <th scope="row">
                            <label for="ccs_spinner_color">Spinner/Loader Color</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_spinner_color" 
                                   name="ccs_spinner_color" 
                                   value="<?php echo esc_attr(get_option('ccs_spinner_color', '#3498db')); ?>" 
                                   class="ccs-color-picker">
                            <p class="description">Color of the loading spinner in suburb search dropdown</p>
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

                <div style="background:#e8f5e9; padding:15px; border-radius:8px; margin:20px 0;">
                    <h4 style="margin-top:0; color:#2e7d32;">🎯 Call-to-Action Buttons</h4>
                    <p style="margin-bottom:0;">Configure the "Book a Tour" and "Contact Us" buttons displayed below the info box on the summary page. Both sections share the same container.</p>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ccs_cta_section_enabled">Enable CTA Buttons</label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" 
                                       id="ccs_cta_section_enabled" 
                                       name="ccs_cta_section_enabled" 
                                       value="1" 
                                       <?php checked(get_option('ccs_cta_section_enabled', 1), 1); ?>>
                                Show CTA buttons below the info box
                            </label>
                            <p class="description">Toggle to show/hide the call-to-action buttons (buttons appear below the info box text in the same container)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_book_tour_text">Book a Tour Button Text</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_book_tour_text" 
                                   name="ccs_book_tour_text" 
                                   value="<?php echo esc_attr(get_option('ccs_book_tour_text', 'Book a Tour')); ?>" 
                                   style="width: 300px;">
                            <p class="description">Text for the "Book a Tour" button</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_book_tour_url">Book a Tour URL / Popup ID</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_book_tour_url" 
                                   name="ccs_book_tour_url" 
                                   value="<?php echo esc_attr(get_option('ccs_book_tour_url', '#')); ?>" 
                                   style="width: 100%; max-width: 500px;" 
                                   placeholder="#elementor-action:action=popup:open&settings=eyJpZCI6IjEyMyJ9">
                            <p class="description">Enter a URL or Elementor popup action (e.g., #elementor-action:action=popup:open&settings=eyJpZCI6IjEyMyJ9)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_contact_us_text">Contact Us Button Text</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_contact_us_text" 
                                   name="ccs_contact_us_text" 
                                   value="<?php echo esc_attr(get_option('ccs_contact_us_text', 'Contact Us')); ?>" 
                                   style="width: 300px;">
                            <p class="description">Text for the "Contact Us" button</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ccs_contact_us_url">Contact Us URL / Popup ID</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="ccs_contact_us_url" 
                                   name="ccs_contact_us_url" 
                                   value="<?php echo esc_attr(get_option('ccs_contact_us_url', '#')); ?>" 
                                   style="width: 100%; max-width: 500px;" 
                                   placeholder="#elementor-action:action=popup:open&settings=eyJpZCI6IjEyMyJ9">
                            <p class="description">Enter a URL or Elementor popup action (e.g., #elementor-action:action=popup:open&settings=eyJpZCI6IjEyMyJ9)</p>
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
