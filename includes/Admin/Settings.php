<?php

namespace CCSCalculator\Includes\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class Settings
{
    public function register()
    {
        add_action('admin_init', [$this, 'admin_init']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_color_picker']);
        add_action('admin_notices', [$this, 'settings_saved_notice']);
    }
    
    public function settings_saved_notice()
    {
        if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>✓ Settings saved successfully!</strong></p>
            </div>
            <?php
        }
    }

    public function admin_init()
    {
        // Calculator settings
        register_setting('childcare_ccs_group', 'childcare_ccs_policy', [$this, 'childcare_ccs_policy_sanitize']);
        register_setting('childcare_ccs_group', 'ccs_centres_list', [$this, 'sanitize_centres_list']);
        
        // HubSpot settings (separate group)
        register_setting('childcare_ccs_hubspot_group', 'ccs_form_type', 'sanitize_text_field');
        register_setting('childcare_ccs_hubspot_group', 'ccs_hubspot_portal_id', 'sanitize_text_field');
        register_setting('childcare_ccs_hubspot_group', 'ccs_hubspot_form_id', 'sanitize_text_field');
        register_setting('childcare_ccs_hubspot_group', 'ccs_hubspot_region', 'sanitize_text_field');
        register_setting('childcare_ccs_hubspot_group', 'ccs_hubspot_hidden_field', 'sanitize_text_field');
        
        // Email template settings
        register_setting('childcare_ccs_email_group', 'ccs_email_header_image', 'esc_url_raw');
        register_setting('childcare_ccs_email_group', 'ccs_email_contact_phone', 'sanitize_text_field');
        register_setting('childcare_ccs_email_group', 'ccs_email_contact_email', 'sanitize_email');
        register_setting('childcare_ccs_email_group', 'ccs_email_facebook', 'esc_url_raw');
        register_setting('childcare_ccs_email_group', 'ccs_email_twitter', 'esc_url_raw');
        register_setting('childcare_ccs_email_group', 'ccs_email_instagram', 'esc_url_raw');
        register_setting('childcare_ccs_email_group', 'ccs_email_linkedin', 'esc_url_raw');
        
        // Enhanced email template settings
        register_setting('childcare_ccs_email_group', 'ccs_email_template_subject', 'sanitize_text_field');
        register_setting('childcare_ccs_email_group', 'ccs_email_template_greeting', 'sanitize_text_field');
        register_setting('childcare_ccs_email_group', 'ccs_email_template_intro', [$this, 'sanitize_html_content']);
        register_setting('childcare_ccs_email_group', 'ccs_email_template_body', [$this, 'sanitize_html_content']);
        register_setting('childcare_ccs_email_group', 'ccs_email_template_footer_text', [$this, 'sanitize_html_content']);
        register_setting('childcare_ccs_email_group', 'ccs_email_template_colors', [$this, 'sanitize_color_array']);
        register_setting('childcare_ccs_email_group', 'ccs_email_template_fonts', 'sanitize_text_field');
        register_setting('childcare_ccs_email_group', 'ccs_email_template_layout', 'sanitize_text_field');
        register_setting('childcare_ccs_email_group', 'ccs_email_template_logo_position', 'sanitize_text_field');
        register_setting('childcare_ccs_email_group', 'ccs_email_template_show_summary', 'absint');
        register_setting('childcare_ccs_email_group', 'ccs_email_template_show_breakdown', 'absint');
        
        // Admin email template settings
        register_setting('childcare_ccs_email_group', 'ccs_admin_email_subject', 'sanitize_text_field');
        register_setting('childcare_ccs_email_group', 'ccs_admin_email_heading', 'sanitize_text_field');
        register_setting('childcare_ccs_email_group', 'ccs_admin_email_intro', 'sanitize_textarea_field');
        register_setting('childcare_ccs_email_group', 'ccs_admin_email_footer', 'sanitize_textarea_field');
        register_setting('childcare_ccs_email_group', 'ccs_admin_email_show_location', 'absint');
        register_setting('childcare_ccs_email_group', 'ccs_admin_email_show_timestamp', 'absint');
        register_setting('childcare_ccs_email_group', 'ccs_admin_email_colors', [$this, 'sanitize_color_array']);
        register_setting('childcare_ccs_email_group', 'ccs_admin_notification_emails', 'sanitize_textarea_field');
        
        // Styling settings (separate group) - using custom sanitize for RGBA support
        register_setting('childcare_ccs_styling_group', 'ccs_font_family', 'sanitize_text_field');
        register_setting('childcare_ccs_styling_group', 'ccs_heading_size', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_heading_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_label_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_text_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_input_bg_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_input_border_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_input_focus_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_input_border_radius', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_slider_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_slider_bg_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_button_bg_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_button_text_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_button_hover_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_button_border_radius', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_button_border_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_button_border_hover_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_button_padding', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_button_hover_text_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_button_font_size', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_button_font_weight', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_button_width', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_button_border_width', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_button_letter_spacing', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_button_margin', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_button_height', 'absint');

        // Navigation button styling (Next/Back buttons)
        register_setting('childcare_ccs_styling_group', 'ccs_nav_next_bg_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_nav_next_text_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_nav_next_hover_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_nav_next_border_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_nav_next_border_hover_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_nav_next_hover_text_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_nav_next_border_radius', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_nav_next_padding', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_nav_next_font_size', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_nav_next_font_weight', 'absint');

        register_setting('childcare_ccs_styling_group', 'ccs_nav_back_bg_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_nav_back_text_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_nav_back_hover_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_nav_back_border_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_nav_back_border_hover_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_nav_back_hover_text_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_nav_back_border_radius', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_nav_back_padding', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_nav_back_font_size', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_nav_back_font_weight', 'absint');
        
        // Enrolment option button styling
        register_setting('childcare_ccs_styling_group', 'ccs_enrolment_bg_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_enrolment_text_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_enrolment_border_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_enrolment_hover_bg_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_enrolment_hover_text_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_enrolment_hover_border_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_enrolment_active_bg_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_enrolment_active_text_color', [$this, 'sanitize_color_with_alpha']);
        
        register_setting('childcare_ccs_styling_group', 'ccs_background_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_container_border_radius', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_container_shadow', 'sanitize_text_field');
        register_setting('childcare_ccs_styling_group', 'ccs_container_padding', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_progress_active_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_progress_completed_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_progress_inactive_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_progress_line_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_progress_text_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_progress_step_size', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_card_bg_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_card_border_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_card_border_radius', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_summary_value_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_summary_label_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_accent_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_border_color', [$this, 'sanitize_color_with_alpha']);
        
        // Loader/Spinner settings
        register_setting('childcare_ccs_styling_group', 'ccs_spinner_color', [$this, 'sanitize_color_with_alpha']);
        
        // Summary page specific colors
        register_setting('childcare_ccs_styling_group', 'ccs_summary_heading_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_total_fee_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_subsidy_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_out_of_pocket_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_week_heading_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_fee_bg_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_subsidy_bg_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_out_of_pocket_bg_color', [$this, 'sanitize_color_with_alpha']);
        
        // Summary period buttons (Fortnightly, Weekly, Monthly, Yearly)
        register_setting('childcare_ccs_styling_group', 'ccs_period_btn_bg_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_period_btn_text_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_period_btn_hover_bg_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_period_btn_hover_text_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_period_btn_active_bg_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_period_btn_active_text_color', [$this, 'sanitize_color_with_alpha']);
        
        // Email form toggle section
        register_setting('childcare_ccs_styling_group', 'ccs_email_toggle_title', 'sanitize_text_field');
        register_setting('childcare_ccs_styling_group', 'ccs_email_toggle_subtitle', 'sanitize_text_field');
        register_setting('childcare_ccs_styling_group', 'ccs_email_toggle_icon_image', 'esc_url_raw');
        register_setting('childcare_ccs_styling_group', 'ccs_email_toggle_bg_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_email_toggle_text_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_email_toggle_icon_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_email_form_bg_color', [$this, 'sanitize_color_with_alpha']);
        
        // Info box (above email form)
        register_setting('childcare_ccs_styling_group', 'ccs_info_box_enabled', 'absint');
        register_setting('childcare_ccs_styling_group', 'ccs_info_box_text', 'wp_kses_post');
        register_setting('childcare_ccs_styling_group', 'ccs_info_box_icon_image', 'esc_url_raw');
        register_setting('childcare_ccs_styling_group', 'ccs_info_box_icon_color', [$this, 'sanitize_color_with_alpha']);
        register_setting('childcare_ccs_styling_group', 'ccs_info_box_text_color', [$this, 'sanitize_color_with_alpha']);
        
        // Custom form checkboxes
        register_setting('childcare_ccs_styling_group', 'ccs_privacy_policy_url', 'esc_url_raw');
        register_setting('childcare_ccs_styling_group', 'ccs_privacy_policy_text', 'sanitize_text_field');
        register_setting('childcare_ccs_styling_group', 'ccs_contact_checkbox_text', 'sanitize_text_field');
    }
    
    public function sanitize_color_with_alpha($color)
    {
        // Allow hex colors
        if (preg_match('/^#[a-f0-9]{6}$/i', $color)) {
            return sanitize_hex_color($color);
        }
        
        // Allow rgba colors
        if (preg_match('/^rgba?\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*(,\s*[\d.]+\s*)?\)$/i', $color)) {
            return sanitize_text_field($color);
        }
        
        // Fallback to hex sanitization
        return sanitize_hex_color($color);
    }
    
    public function sanitize_html_content($content)
    {
        // Allow basic HTML tags for email content
        $allowed_tags = array(
            'p' => array(),
            'br' => array(),
            'strong' => array(),
            'b' => array(),
            'em' => array(),
            'i' => array(),
            'u' => array(),
            'a' => array('href' => array(), 'title' => array()),
            'ul' => array(),
            'ol' => array(),
            'li' => array(),
            'h1' => array(),
            'h2' => array(),
            'h3' => array(),
            'h4' => array(),
            'div' => array('style' => array()),
            'span' => array('style' => array())
        );
        
        return wp_kses($content, $allowed_tags);
    }
    
    public function sanitize_color_array($colors)
    {
        if (!is_array($colors)) {
            return array();
        }
        
        $sanitized = array();
        foreach ($colors as $key => $color) {
            $sanitized[sanitize_key($key)] = $this->sanitize_color_with_alpha($color);
        }
        
        return $sanitized;
    }

    public function enqueue_color_picker($hook)
    {
        
        // Enqueue on all child-care-subsidy admin pages to ensure it loads
        if (strpos($hook, 'child-care-subsidy') !== false || 
            strpos($hook, 'child-care-styling') !== false ||
            strpos($hook, 'styling') !== false ||
            isset($_GET['page']) && strpos($_GET['page'], 'child-care') !== false) {
            
            // Force enqueue color picker with dependencies
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-widget');
            wp_enqueue_script('jquery-ui-mouse');
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-slider');
            wp_enqueue_script('wp-color-picker', false, array('jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse', 'jquery-ui-draggable', 'jquery-ui-slider'));
            
            
            // Add custom CSS for better color picker styling
            wp_add_inline_style('wp-color-picker', '
                .wp-picker-container .wp-color-result-text {
                    display: inline-block;
                    vertical-align: top;
                }
                .wp-picker-container {
                    display: inline-block;
                    margin-bottom: 10px;
                }
                .ccs-color-picker {
                    width: 140px !important;
                    font-family: monospace !important;
                    font-size: 12px !important;
                }
            ');
        }
        
        // Enqueue media uploader on email template page
        if ($hook === 'child-care-subsidy_page_child-care-email' || 
            (isset($_GET['page']) && $_GET['page'] === 'child-care-email')) {
            wp_enqueue_media();
            
            // Also enqueue the media scripts explicitly
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
            wp_enqueue_style('thickbox');
        }
    }

    public function childcare_ccs_policy_sanitize($input)
    {
        if (!is_array($input)) return $input;
        $out = get_option('childcare_ccs_policy', []);
        $out = array_merge($out, (array)$input);

        $out['income_base_threshold'] = floatval($out['income_base_threshold']);
        $out['income_zero_threshold'] = floatval($out['income_zero_threshold']);
        $out['income_step']           = floatval($out['income_step']);
        $out['max_pct']               = floatval($out['max_pct']);

        if (isset($out['hourly_caps']) && is_array($out['hourly_caps'])) {
            foreach ($out['hourly_caps'] as $k => $v) {
                $out['hourly_caps'][$k] = floatval($v);
            }
        }

        // Sanitize new calculator form settings
        $out['default_withholding'] = isset($out['default_withholding']) ? absint($out['default_withholding']) : 5;
        $out['default_activity_hours'] = isset($out['default_activity_hours']) ? absint($out['default_activity_hours']) : 48;
        $out['ccs_hours_8_16'] = isset($out['ccs_hours_8_16']) ? absint($out['ccs_hours_8_16']) : 36;
        $out['ccs_hours_17_48'] = isset($out['ccs_hours_17_48']) ? absint($out['ccs_hours_17_48']) : 72;
        $out['ccs_hours_48_plus'] = isset($out['ccs_hours_48_plus']) ? absint($out['ccs_hours_48_plus']) : 100;
        $out['low_income_threshold'] = isset($out['low_income_threshold']) ? floatval($out['low_income_threshold']) : 85279;
        $out['higher_ccs_threshold'] = isset($out['higher_ccs_threshold']) ? floatval($out['higher_ccs_threshold']) : 367563;

        $out['last_updated']    = sanitize_text_field($out['last_updated']);
        $out['disclaimer_text'] = sanitize_text_field($out['disclaimer_text']);
        return $out;
    }

    public function sanitize_centres_list($input)
    {
        if (empty($input)) {
            return '';
        }
        
        // Split by newlines and sanitize each centre name
        $centres = explode("\n", $input);
        $sanitized = array_map('sanitize_text_field', $centres);
        $sanitized = array_filter($sanitized); // Remove empty lines
        
        return implode("\n", $sanitized);
    }
}

