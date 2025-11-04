<?php

namespace CCSCalculator\Includes\Frontend;

if (!defined('ABSPATH')) {
    exit;
}

class Assets
{
    public function register()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue()
    {
        wp_enqueue_script('jquery');
        
        // Use last modified time of settings to bust cache
        $cache_buster = get_option('ccs_settings_updated', time());
        wp_enqueue_style('childcare-ccs-style', CCS_CALCULATOR_PLUGIN_URL . 'assets/css/childcare.css', [], $cache_buster);
        
        // Add custom styling from admin settings
        $this->add_custom_styles();
    }
    
    private function add_custom_styles()
    {
        // Get styling options
        $font_family = get_option('ccs_font_family', 'Red Hat Display');
        $heading_size = get_option('ccs_heading_size', '32');
        $heading_color = get_option('ccs_heading_color', '#1a1a1a');
        $label_color = get_option('ccs_label_color', '#2c3e50');
        $text_color = get_option('ccs_text_color', '#2c3e50');
        $tooltip_icon_color = get_option('ccs_tooltip_icon_color', '#84bd00');
        $input_bg = get_option('ccs_input_bg_color', '#f8f9fa');
        $input_border = get_option('ccs_input_border_color', '#e1e8ed');
        $input_focus = get_option('ccs_input_focus_color', '#0073aa');
        $input_radius = get_option('ccs_input_border_radius', '8');
        $slider_color = get_option('ccs_slider_color', '#0073aa');
        $slider_bg = get_option('ccs_slider_bg_color', '#e1e8ed');
        $button_bg = get_option('ccs_button_bg_color', '#0073aa');
        $button_text = get_option('ccs_button_text_color', '#ffffff');
        $button_hover = get_option('ccs_button_hover_color', '#005f8a');
        $button_radius = get_option('ccs_button_border_radius', '8');
        
        // New button styling options
        $button_padding = get_option('ccs_button_padding', '12');
        $button_hover_text = get_option('ccs_button_hover_text_color', '#ffffff');
        $button_border = get_option('ccs_button_border_color', '#0073aa');
        $button_border_hover = get_option('ccs_button_border_hover_color', '#005f8a');
        
        // Child count button styling - Complete from admin
        $child_count_bg = get_option('ccs_child_count_bg_color', '#f8f9fa');
        $child_count_text = get_option('ccs_child_count_text_color', '#2c3e50');
        $child_count_border = get_option('ccs_child_count_border_color', '#e1e8ed');
        $child_count_hover_bg = get_option('ccs_child_count_hover_bg_color', '#e3f2fd');
        $child_count_hover_text = get_option('ccs_child_count_hover_text_color', '#0073aa');
        $child_count_hover_border = get_option('ccs_child_count_hover_border_color', '#0073aa');
        $child_count_active_bg = get_option('ccs_child_count_active_bg_color', '#0073aa');
        $child_count_active_text = get_option('ccs_child_count_active_text_color', '#ffffff');
        $child_count_border_radius = get_option('ccs_child_count_border_radius', '8');
        $child_count_font_family = get_option('ccs_child_count_font_family', 'inherit');
        
        // Fortnight button styling - Complete from admin
        $fortnight_bg = get_option('ccs_fortnight_btn_bg_color', '#f8f9fa');
        $fortnight_text = get_option('ccs_fortnight_btn_text_color', '#2c3e50');
        $fortnight_border = get_option('ccs_fortnight_btn_border_color', '#e1e8ed');
        $fortnight_hover_bg = get_option('ccs_fortnight_btn_hover_bg_color', '#e3f2fd');
        $fortnight_hover_text = get_option('ccs_fortnight_btn_hover_text_color', '#0073aa');
        $fortnight_hover_border = get_option('ccs_fortnight_btn_hover_border_color', '#0073aa');
        $fortnight_active_bg = get_option('ccs_fortnight_btn_active_bg_color', '#0073aa');
        $fortnight_active_text = get_option('ccs_fortnight_btn_active_text_color', '#ffffff');
        $fortnight_font_size = get_option('ccs_fortnight_btn_font_size', '14');
        $fortnight_border_radius = get_option('ccs_fortnight_btn_border_radius', '6');
        $fortnight_font_family = get_option('ccs_fortnight_btn_font_family', 'inherit');
        
        // Slider styling - Complete from admin
        $slider_track = get_option('ccs_slider_track_color', '#e1e8ed');
        $slider_completed = get_option('ccs_slider_completed_color', '#0073aa');
        $slider_thumb = get_option('ccs_slider_thumb_color', '#0073aa');
        $slider_font_size = get_option('ccs_slider_font_size', '14');
        $slider_border_color = get_option('ccs_slider_border_color', '#e1e8ed');
        $slider_hover_color = get_option('ccs_slider_hover_color', '#005f8a');
        $slider_font_family = get_option('ccs_slider_font_family', 'inherit');
        $slider_border_radius = get_option('ccs_slider_border_radius', '8');
        $slider_track_height = get_option('ccs_slider_track_height', '6');
        $slider_thumb_size = get_option('ccs_slider_thumb_size', '20');
        
        // New comprehensive styling options
        $input_font_size = get_option('ccs_input_font_size', '16');
        $input_padding = get_option('ccs_input_padding', '12');
        $input_width = get_option('ccs_input_width', '100');
        $input_border_width = get_option('ccs_input_border_width', '2');
        $button_font_size = get_option('ccs_button_font_size', '15');
        $button_font_weight = get_option('ccs_button_font_weight', '600');
        $button_width = get_option('ccs_button_width', '120');
        $button_border_width = get_option('ccs_button_border_width', '2');
        
        // Step-specific settings
        $step1_location_font_size = get_option('ccs_step1_location_font_size', '16');
        $step1_location_padding = get_option('ccs_step1_location_padding', '12');
        $step1_dropdown_font_size = get_option('ccs_step1_dropdown_font_size', '16');
        
        $step2_income_font_size = get_option('ccs_step2_income_font_size', '18');
        $step2_income_padding = get_option('ccs_step2_income_padding', '15');
        $step2_activity_font_size = get_option('ccs_step2_activity_font_size', '18');
        $step2_activity_padding = get_option('ccs_step2_activity_padding', '15');
        
        $step3_child_count_font_size = get_option('ccs_step3_child_count_font_size', '18');
        $step3_child_count_padding = get_option('ccs_step3_child_count_padding', '12');
        $step3_dob_font_size = get_option('ccs_step3_dob_font_size', '16');
        
        $step4_card_font_size = get_option('ccs_step4_card_font_size', '16');
        $step4_total_font_size = get_option('ccs_step4_total_font_size', '24');
        $step4_total_font_weight = get_option('ccs_step4_total_font_weight', '700');
        
        // Summary Row Colors & Styling
        $summary_total_fee_color = get_option('ccs_summary_total_fee_color', '#0073aa');
        $summary_subsidy_color = get_option('ccs_summary_subsidy_color', '#6B46C1');
        $summary_out_pocket_color = get_option('ccs_summary_out_pocket_color', '#4ECDC4');
        $summary_label_text_color = get_option('ccs_summary_label_text_color', '#333333');
        $summary_row_padding = get_option('ccs_summary_row_padding', '10');
        $summary_row_bg_color = get_option('ccs_summary_row_bg_color', '#f5f5f5');
        $summary_border_color = get_option('ccs_summary_border_color', '#f5f5f5');
        
        // Weekly Breakdown Styling
        $weekly_header_color = get_option('ccs_weekly_header_color', '#333333');
        $weekly_header_font_size = get_option('ccs_weekly_header_font_size', '16');
        $weekly_text_font_size = get_option('ccs_weekly_text_font_size', '16');
        
        // Child Detail Cards
        $child_card_bg_color = get_option('ccs_child_card_bg_color', '#ffffff');
        $child_card_border_color = get_option('ccs_child_card_border_color', '#f5f5f5');
        $child_card_border_radius = get_option('ccs_child_card_border_radius', '5');
        $child_card_padding = get_option('ccs_child_card_padding', '20');
        $child_card_text_font_size = get_option('ccs_child_card_text_font_size', '16');
        $child_card_header_color = get_option('ccs_child_card_header_color', '#333333');
        
        // Period Selection & Child Select
        $period_btn_font_size = get_option('ccs_period_btn_font_size', '14');
        $child_select_font_size = get_option('ccs_child_select_font_size', '16');
        
        // Enhanced button settings
        $button_letter_spacing = get_option('ccs_button_letter_spacing', '0');
        $button_margin = get_option('ccs_button_margin', '5');
        $button_height = get_option('ccs_button_height', '44');
        
        // Mobile responsive settings
        $mobile_heading_size = get_option('ccs_mobile_heading_size', '24');
        $mobile_input_font_size = get_option('ccs_mobile_input_font_size', '16');
        $mobile_button_font_size = get_option('ccs_mobile_button_font_size', '14');
        $mobile_container_padding = get_option('ccs_mobile_container_padding', '20');
        $mobile_input_padding = get_option('ccs_mobile_input_padding', '14');
        $mobile_button_padding = get_option('ccs_mobile_button_padding', '14');
        $mobile_button_width = get_option('ccs_mobile_button_width', 'full');
        $mobile_input_width = get_option('ccs_mobile_input_width', '100');
        $mobile_border_width = get_option('ccs_mobile_border_width', '2');
        $mobile_breakpoint = get_option('ccs_mobile_breakpoint', '768');
        
        // Navigation button styling (Next/Back buttons)
        $nav_next_bg = get_option('ccs_nav_next_bg_color', '#0073aa');
        $nav_next_text = get_option('ccs_nav_next_text_color', '#ffffff');
        $nav_next_hover = get_option('ccs_nav_next_hover_color', '#005f8a');
        $nav_next_border = get_option('ccs_nav_next_border_color', '#0073aa');
        $nav_next_border_hover = get_option('ccs_nav_next_border_hover_color', '#005f8a');
        $nav_next_hover_text = get_option('ccs_nav_next_hover_text_color', '#ffffff');
        $nav_next_radius = get_option('ccs_nav_next_border_radius', '8');
        $nav_next_padding = get_option('ccs_nav_next_padding', '12');
        $nav_next_font_size = get_option('ccs_nav_next_font_size', '15');
        $nav_next_font_weight = get_option('ccs_nav_next_font_weight', '600');

        $nav_back_bg = get_option('ccs_nav_back_bg_color', '#f8f9fa');
        $nav_back_text = get_option('ccs_nav_back_text_color', '#2c3e50');
        $nav_back_hover = get_option('ccs_nav_back_hover_color', '#e3f2fd');
        $nav_back_border = get_option('ccs_nav_back_border_color', '#e1e8ed');
        $nav_back_border_hover = get_option('ccs_nav_back_border_hover_color', '#0073aa');
        $nav_back_hover_text = get_option('ccs_nav_back_hover_text_color', '#0073aa');
        $nav_back_radius = get_option('ccs_nav_back_border_radius', '8');
        $nav_back_padding = get_option('ccs_nav_back_padding', '12');
        $nav_back_font_size = get_option('ccs_nav_back_font_size', '15');
        $nav_back_font_weight = get_option('ccs_nav_back_font_weight', '600');
        
        // Enrolment option button styling
        $enrolment_bg = get_option('ccs_enrolment_bg_color', '#f8f9fa');
        $enrolment_text = get_option('ccs_enrolment_text_color', '#2c3e50');
        $enrolment_border = get_option('ccs_enrolment_border_color', '#e1e8ed');
        $enrolment_hover_bg = get_option('ccs_enrolment_hover_bg_color', '#e3f2fd');
        $enrolment_hover_text = get_option('ccs_enrolment_hover_text_color', '#0073aa');
        $enrolment_hover_border = get_option('ccs_enrolment_hover_border_color', '#0073aa');
        $enrolment_active_bg = get_option('ccs_enrolment_active_bg_color', '#0073aa');
        $enrolment_active_text = get_option('ccs_enrolment_active_text_color', '#ffffff');

        $bg_color = get_option('ccs_background_color', '#ffffff');
        $container_radius = get_option('ccs_container_border_radius', '16');
        $container_shadow = get_option('ccs_container_shadow', 'medium');
        $container_padding = get_option('ccs_container_padding', '35');
        $progress_active = get_option('ccs_progress_active_color', '#0073aa');
        $progress_completed = get_option('ccs_progress_completed_color', '#00a32a');
        $progress_inactive = get_option('ccs_progress_inactive_color', '#e1e8ed');
        $progress_line = get_option('ccs_progress_line_color', '#e1e8ed');
        $progress_text = get_option('ccs_progress_text_color', '#666666');
        $progress_size = get_option('ccs_progress_step_size', '40');
        $card_bg = get_option('ccs_card_bg_color', '#f8f9fa');
        $card_border = get_option('ccs_card_border_color', '#e8f4f8');
        $card_radius = get_option('ccs_card_border_radius', '12');
        $summary_value = get_option('ccs_summary_value_color', '#0073aa');
        $summary_label = get_option('ccs_summary_label_color', '#2c3e50');
        $accent_color = get_option('ccs_accent_color', '#0073aa');
        $border_color = get_option('ccs_border_color', '#e1e8ed');
        
        // Summary period buttons (Fortnightly, Weekly, Monthly, Yearly)
        $period_btn_bg = get_option('ccs_period_btn_bg_color', '#84bd00');
        $period_btn_text = get_option('ccs_period_btn_text_color', '#ffffff');
        $period_btn_hover_bg = get_option('ccs_period_btn_hover_bg_color', '#6fa000');
        $period_btn_hover_text = get_option('ccs_period_btn_hover_text_color', '#ffffff');
        $period_btn_active_bg = get_option('ccs_period_btn_active_bg_color', '#5a8500');
        $period_btn_active_text = get_option('ccs_period_btn_active_text_color', '#ffffff');
        $period_btn_radius = get_option('ccs_period_btn_border_radius', '8');
        
        // Shadow options
        $shadow_map = [
            'none' => 'none',
            'light' => '0 2px 8px rgba(0, 0, 0, 0.05)',
            'medium' => '0 10px 40px rgba(0, 0, 0, 0.08)',
            'heavy' => '0 15px 50px rgba(0, 0, 0, 0.15)'
        ];
        $box_shadow = $shadow_map[$container_shadow] ?? $shadow_map['medium'];
        
        // Build custom CSS
        $custom_css = "
        /* CSS Custom Properties for Dynamic Styling */
        .childcare-ccs-root {
            --ccs-progress-active-color: {$progress_active};
            --ccs-progress-completed-color: {$progress_completed};
            --ccs-progress-inactive-color: {$progress_inactive};
            --ccs-progress-line-color: {$progress_line};
            --ccs-progress-text-color: {$progress_text};
            --ccs-progress-step-size: {$progress_size}px;
            
            font-family: '{$font_family}', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            background: {$bg_color} !important;
            border-radius: {$container_radius}px !important;
            box-shadow: {$box_shadow} !important;
            padding: {$container_padding}px !important;
        }
        
        /* Headings */
        .childcare-ccs-root h3 {
            color: {$heading_color} !important;
            font-size: {$heading_size}px !important;
        }
        
        .childcare-ccs-root h4,
        .childcare-ccs-root h5 {
            color: {$text_color} !important;
        }
        
        /* Labels */
        .childcare-step label {
            color: {$label_color} !important;
            font-size: 14px !important;
            line-height: 20px !important;
        }

        .suburb-suggestion span {
            font-size: 16px !important;
            line-height: 20px !important;
        }
        
        /* Tooltip Icon */
        .childcare-ccs-root .tooltip-icon {
            background: {$tooltip_icon_color} !important;
        }
        
        /* Input Fields */
        .childcare-step input[type='text'],
        .childcare-step input[type='number'],
        .childcare-step input[type='date'],
        .childcare-step input[type='email'],
        .childcare-step input[type='tel'],
        .childcare-step select,
        .childcare-step textarea {
            background: {$input_bg} !important;
            border-color: {$input_border} !important;
            border-radius: {$input_radius}px !important;
            border-width: {$input_border_width}px !important;
            font-family: '{$font_family}', sans-serif !important;
            font-size: {$input_font_size}px !important;
            padding: {$input_padding}px !important;
            width: {$input_width}% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            margin: 0px !important;
        }
        
        .childcare-step input:focus,
        .childcare-step select:focus,
        .childcare-step textarea:focus {
            border-color: {$input_focus} !important;
            box-shadow: 0 0 0 3px rgba(" . hexdec(substr($input_focus, 1, 2)) . ", " . hexdec(substr($input_focus, 3, 2)) . ", " . hexdec(substr($input_focus, 5, 2)) . ", 0.1) !important;
            outline: none !important;
        }
        
        /* Slider Styling */
        .childcare-step input[type='range'] {
            background: {$slider_bg} !important;
        }
        
        .childcare-step input[type='range']::-webkit-slider-thumb {
            background: {$slider_color} !important;
        }
        
        .childcare-step input[type='range']::-moz-range-thumb {
            background: {$slider_color} !important;
        }
        
        /* Button Styling */
        .childcare-ccs-root .button,
        .childcare-ccs-root .button.button-primary {
            font-family: '{$font_family}', sans-serif !important;
            font-size: {$button_font_size}px !important;
            font-weight: {$button_font_weight} !important;
            letter-spacing: {$button_letter_spacing}px !important;
            background: {$button_bg} !important;
            color: {$button_text} !important;
            border-color: {$button_border} !important;
            border-width: {$button_border_width}px !important;
            border-radius: {$button_radius}px !important;
            padding: {$button_padding}px 20px !important;
            min-width: {$button_width}px !important;
            height: unset !important;
            margin: 0px !important;
            box-sizing: border-box !important;
            line-height: 20px !important;
            min-width: unset !important;
            border-style: solid !important;
            transition: all 0.3s ease !important;
        }
        
        .childcare-ccs-root .button:hover,
        .childcare-ccs-root .button.button-primary:hover {
            background: {$button_hover} !important;
            border-color: {$button_border_hover} !important;
            color: {$button_hover_text} !important;
        }

        /* Navigation Button Styling - Next/Back Buttons */
        .childcare-ccs-root .nav-button.nav-next,
        .childcare-ccs-root .nav-button.nav-next.button-primary {
            font-family: '{$font_family}', sans-serif !important;
            font-size: {$nav_next_font_size}px !important;
            font-weight: {$nav_next_font_weight} !important;
            letter-spacing: {$button_letter_spacing}px !important;
            background: {$nav_next_bg};
            color: {$nav_next_text};
            border-color: {$nav_next_border};
            border-width: {$button_border_width}px !important;
            border-radius: {$nav_next_radius}px !important;
            padding: {$nav_next_padding}px 20px !important;
            min-width: {$button_width}px !important;
            height: unset !important;
            margin: 0px !important;
            box-sizing: border-box !important;
            line-height: 20px !important;
            min-width: unset !important;
            border-style: solid !important;
            transition: all 0.3s ease !important;
        }

        .childcare-ccs-root .nav-button.nav-next:hover,
        .childcare-ccs-root .nav-button.nav-next.button-primary:hover {
            background: {$nav_next_hover} !important;
            border-color: {$nav_next_border_hover} !important;
            color: {$nav_next_hover_text} !important;
        }

        .childcare-ccs-root .nav-button.nav-back {
            font-family: '{$font_family}', sans-serif !important;
            font-size: {$nav_back_font_size}px !important;
            font-weight: {$nav_back_font_weight} !important;
            letter-spacing: {$button_letter_spacing}px !important;
            background: {$nav_back_bg} !important;
            color: {$nav_back_text} !important;
            border-color: {$nav_back_border} !important;
            border-width: {$button_border_width}px !important;
            border-radius: {$nav_back_radius}px !important;
            padding: {$nav_back_padding}px 20px !important;
            min-width: {$button_width}px !important;
            height: unset !important;
            margin: 0px !important;
            box-sizing: border-box !important;
            line-height: 20px !important;
            min-width: unset !important;
            border-style: solid !important;
            transition: all 0.3s ease !important;
        }

        .childcare-ccs-root .nav-button.nav-back:hover {
            background: {$nav_back_hover} !important;
            border-color: {$nav_back_border_hover} !important;
            color: {$nav_back_hover_text} !important;
        }

        /* Enrolment Option Buttons */
        .childcare-ccs-root .button.enrolment-option {
            background: {$enrolment_bg} !important;
            color: {$enrolment_text} !important;
            border: {$button_border_width}px solid {$enrolment_border} !important;
            border-radius: {$button_radius}px !important;
            padding: {$button_padding}px 20px !important;
            font-size: {$button_font_size}px !important;
            font-weight: {$button_font_weight} !important;
            transition: all 0.3s ease !important;
            cursor: pointer !important;
        }
        
        .childcare-ccs-root .button.enrolment-option:hover {
            background: {$enrolment_hover_bg} !important;
            border-color: {$enrolment_hover_border} !important;
            color: {$enrolment_hover_text} !important;
        }
        
        .childcare-ccs-root .button.enrolment-option.button-primary,
        .childcare-ccs-root .button.enrolment-option.active {
            background: {$enrolment_active_bg} !important;
            border-color: {$enrolment_active_bg} !important;
            color: {$enrolment_active_text} !important;
            font-weight: 600 !important;
        }

        /* Summary Period Buttons (Fortnightly, Weekly, Monthly, Yearly) */
        .childcare-ccs-root .summary-btn {
            background: {$period_btn_bg} !important;
            color: {$period_btn_text} !important;
            border-color: {$period_btn_bg} !important;
            border-radius: {$period_btn_radius}px !important;
            transition: all 0.3s ease !important;
        }
        
        .childcare-ccs-root .summary-btn:hover {
            background: {$period_btn_hover_bg} !important;
            color: {$period_btn_hover_text} !important;
            border-color: {$period_btn_hover_bg} !important;
        }
        
        .childcare-ccs-root .summary-btn.button-primary,
        .childcare-ccs-root .summary-btn.active {
            background: {$period_btn_active_bg} !important;
            color: {$period_btn_active_text} !important;
            border-color: {$period_btn_active_bg} !important;
            font-weight: 600 !important;
        }

        /* Paragraphs */
        #childcare-ccs-calculator p {
            margin-bottom: 10px !important;
        }
        
        /* Child Count Buttons (1, 2, 3, 4, 5) */
        .childcare-ccs-root .child-count-btn {
            background: {$child_count_bg} !important;
            color: {$child_count_text} !important;
            border-color: {$child_count_border} !important;
            border-radius: {$child_count_border_radius}px !important;
            border-width: {$button_border_width}px !important;
            border-style: solid !important;
            font-family: " . ($child_count_font_family === 'inherit' ? "'{$font_family}'" : "'{$child_count_font_family}'") . ", sans-serif !important;
            width: {$button_width}px !important;
            min-width: {$button_width}px !important;
            transition: all 0.3s ease !important;
        }
        
        .childcare-ccs-root .child-count-btn:hover {
            background: {$child_count_hover_bg} !important;
            color: {$child_count_hover_text} !important;
            border-color: {$child_count_hover_border} !important;
        }
        
        .childcare-ccs-root .child-count-btn.button-primary,
        .childcare-ccs-root .child-count-btn.active {
            background: {$child_count_active_bg} !important;
            color: {$child_count_active_text} !important;
            border-color: {$child_count_active_bg} !important;
        }
        
        /* Fortnight Days Buttons */
        .childcare-ccs-root .fortnight-day-btn,
        .childcare-ccs-root .fortnight-days .day-button {
            background: {$fortnight_bg} !important;
            color: {$fortnight_text} !important;
            border: {$button_border_width}px solid {$fortnight_border} !important;
            border-radius: {$fortnight_border_radius}px !important;
            padding: {$button_padding}px 15px !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            font-family: " . ($fortnight_font_family === 'inherit' ? "'{$font_family}'" : "'{$fortnight_font_family}'") . ", sans-serif !important;
            font-size: {$fortnight_font_size}px !important;
            display: inline-block !important;
            text-align: center !important;
            min-width: {$button_width}px !important;
            font-weight: {$button_font_weight} !important;
            line-height: 20px !important;
        }
        
        .childcare-ccs-root .fortnight-day-btn:hover,
        .childcare-ccs-root .fortnight-days .day-button:hover {
            background: {$fortnight_hover_bg} !important;
            color: {$fortnight_hover_text} !important;
            border-color: {$fortnight_hover_border} !important;
        }
        
        .childcare-ccs-root .fortnight-day-btn.active,
        .childcare-ccs-root .fortnight-days .day-button.active {
            background: {$fortnight_active_bg} !important;
            color: {$fortnight_active_text} !important;
            border-color: {$fortnight_active_bg} !important;
        }
        
        /* Enhanced Slider Styling */
        .childcare-ccs-root .childcare-step input[type='range'] {
            background: {$slider_track} !important;
            position: relative !important;
            height: {$slider_track_height}px !important;
            border-radius: {$slider_border_radius}px !important;
        }
        
        .childcare-ccs-root .childcare-step input[type='range']::-webkit-slider-thumb {
            background: {$slider_thumb} !important;
            width: {$slider_thumb_size}px !important;
            height: {$slider_thumb_size}px !important;
            border-radius: 50% !important;
        }
        
        .childcare-ccs-root .childcare-step input[type='range']::-moz-range-thumb {
            background: {$slider_thumb} !important;
            width: {$slider_thumb_size}px !important;
            height: {$slider_thumb_size}px !important;
            border-radius: 50% !important;
        }
        
        .childcare-ccs-root .childcare-step input[type='range']:hover::-webkit-slider-thumb {
            background: {$slider_hover_color} !important;
        }
        
        .childcare-ccs-root .childcare-step input[type='range']:hover::-moz-range-thumb {
            background: {$slider_hover_color} !important;
        }
        
        /* Slider Labels */
        .childcare-ccs-root .slider-container span {
            font-size: {$slider_font_size}px !important;
            font-family: " . ($slider_font_family === 'inherit' ? "'{$font_family}'" : "'{$slider_font_family}'") . ", sans-serif !important;
        }
        
        /* Slider completed line effect */
        .childcare-ccs-root .slider-container {
            position: relative !important;
            --slider-progress: 50%;
        }
        
        .childcare-ccs-root .slider-container > div {
            position: relative !important;
        }
        
        .childcare-ccs-root .slider-container > div::before {
            content: '' !important;
            position: absolute !important;
            top: 58% !important;
            left: 0 !important;
            width: var(--slider-progress, 50%) !important;
            height: {$slider_track_height}px !important;
            background: {$slider_completed} !important;
            border-radius: {$slider_border_radius}px !important;
            transform: translateY(-50%) !important;
            z-index: 9 !important;
            transition: width 0.3s ease !important;
            pointer-events: none !important;
        }
        
        .childcare-ccs-root .slider-container input[type='range'] {
            position: relative !important;
            z-index: 2 !important;
            -webkit-appearance: none;
            appearance: none;
            border: 0px !important;
            height: {$slider_track_height}px !important;
        }
        
        input[type=range]::-webkit-slider-thumb {
            height: {$slider_thumb_size}px !important;
            width: {$slider_thumb_size}px !important;
            border: 0px !important;
            border-radius: 50% !important;
        }

        .childcare-ccs-root .slider-container input[type='range']:focus {
            outline: none !important;
            border: 0px !important;
            box-shadow: none !important;
        }
        .slider-container span.calc-hours {
            border: 1px solid #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 30px;
            height: unset !important;
            line-height: 20px !important;
        }
        
        /* Enhanced Progress Steps - PROPERLY SCOPED */
        .childcare-ccs-root .progress-container {
            --step-size: var(--ccs-progress-step-size, 40px);
        }
        
        .childcare-ccs-root .progress-line-bg {
            background: var(--ccs-progress-line-color, #e1e8ed) !important;
        }
        
        .childcare-ccs-root .progress-line-active {
            background: var(--ccs-progress-completed-color, #00a32a) !important;
        }
        
        .childcare-ccs-root .progress-step {
            width: var(--step-size) !important;
            height: var(--step-size) !important;
            background: var(--ccs-progress-inactive-color, #e1e8ed) !important;
            color: var(--ccs-progress-text-color, #666) !important;
            border: 3px solid transparent !important;
            font-size: calc(var(--step-size) * 0.4) !important;
        }
        
        .childcare-ccs-root .progress-step.active {
            background: var(--ccs-progress-active-color, #0073aa) !important;
            color: #ffffff !important;
            border-color: var(--ccs-progress-active-color, #0073aa) !important;
            transform: scale(1.1) !important;
            box-shadow: 0 0 0 4px rgba(0, 115, 170, 0.2) !important;
        }
        
        .childcare-ccs-root .progress-step.completed {
            background: var(--ccs-progress-completed-color, #00a32a) !important;
            color: #ffffff !important;
            border-color: var(--ccs-progress-completed-color, #00a32a) !important;
        }
        
        .childcare-ccs-root .progress-label {
            color: var(--ccs-progress-text-color, #666) !important;
            font-size: calc(var(--step-size) * 0.325) !important;
        }
        
        .childcare-ccs-root .progress-step-wrapper:has(.progress-step.active) .progress-label {
            color: var(--ccs-progress-active-color, #0073aa) !important;
            font-weight: 700 !important;
            transform: scale(1.05) !important;
        }
        
        .childcare-ccs-root .progress-step-wrapper:has(.progress-step.completed) .progress-label {
            color: var(--ccs-progress-completed-color, #00a32a) !important;
            font-weight: 600 !important;
        }
        
        /* Child Detail Cards */
        
        .childcare-ccs-root .child-details h4,
        .childcare-ccs-root .child-detail-card h5 {
            color: {$accent_color} !important;
            border-bottom-color: {$card_border} !important;
        }
        
        /* Summary Section - Enhanced with Admin Controls */
        .childcare-ccs-root span.week-breakdown__week,
        .childcare-ccs-root .my__values__week span.week-breakdown__values span.value {
            color: {$summary_value} !important;
        }
        
        .childcare-ccs-root .c-summary-table__row__label,
        .childcare-ccs-root span.week-breakdown__label {
            color: {$summary_label_text_color} !important;
        }
        
        /* Summary Row Styling with Admin Controls */
        .childcare-ccs-root .c-summary-table__row {
            padding: {$summary_row_padding}px 15px !important;
            border-bottom: {$input_border_width}px solid {$summary_border_color} !important;
        }
        
        /* Summary Row Background Colors */
        .childcare-ccs-root .c-summary-table__row:nth-child(2) {
            background: {$summary_row_bg_color} !important;
        }
        
        /* Specific Summary Value Colors */
        .childcare-ccs-root .c-summary-table__row:nth-child(1) .c-summary-table__row__value {
            color: {$summary_total_fee_color} !important;
        }
        
        .childcare-ccs-root .c-summary-table__row:nth-child(2) .c-summary-table__row__value {
            color: {$summary_subsidy_color} !important;
        }
        
        .childcare-ccs-root .c-summary-table__row:nth-child(3) .c-summary-table__row__value {
            color: {$summary_out_pocket_color} !important;
        }
        
        /* Weekly Breakdown Headers */
        .childcare-ccs-root h4 {
            color: {$weekly_header_color} !important;
            font-size: {$weekly_header_font_size}px !important;
        }
        
        /* Child Detail Cards */
        .childcare-ccs-root .child-detail-card {
            background: {$child_card_bg_color} !important;
            border: {$input_border_width}px solid {$child_card_border_color} !important;
            border-radius: {$child_card_border_radius}px !important;
            padding: {$child_card_padding}px !important;
        }
        
        .childcare-ccs-root .child-detail-card h5 {
            color: {$child_card_header_color} !important;
        }
        
        .childcare-ccs-root .child-detail-card p {
            font-size: {$child_card_text_font_size}px !important;
        }
        
        /* Period Selection Buttons */
        .childcare-ccs-root .summary-btn {
            font-size: {$period_btn_font_size}px !important;
        }
        
        /* Child Select Dropdown */
        .childcare-ccs-root #child-select-wrapper label,
        .childcare-ccs-root #child-select {
            font-size: {$child_select_font_size}px !important;
        }
        
        /* Show Total Label */
        .childcare-ccs-root .show_total strong {
            font-size: {$child_select_font_size}px !important;
        }
        
        /* Accent Colors */
        .childcare-ccs-root a {
            color: {$accent_color} !important;
        }
        
        /* Suburb Suggestions */
        .childcare-ccs-root .suburb-suggestion:hover {
            background: {$accent_color} !important;
        }
        
        /* Summary Email Form */
        .childcare-ccs-root #summary-email-form button[type='submit'],
        .childcare-ccs-root #custom-summary-form button[type='submit'] {
            background: {$button_bg} !important;
            color: {$button_text} !important;
            font-family: '{$font_family}', sans-serif !important;
            border-radius: {$button_radius}px !important;
        }
        
        .childcare-ccs-root #summary-email-form button[type='submit']:hover,
        .childcare-ccs-root #custom-summary-form button[type='submit']:hover {
            background: {$button_hover} !important;
        }
        
        /* Step-Specific Styling */
        
        /* Step 1: Location and Dropdown Fields */
        .childcare-ccs-root #step1 input[type='text'],
        .childcare-ccs-root #step1 select {
            font-size: {$step1_location_font_size}px !important;
            padding: {$step1_location_padding}px !important;
            line-height: 20px !important;;
        }
        
        .childcare-ccs-root #step1 select {
            font-size: {$step1_dropdown_font_size}px !important;
        }
        
        /* Step 2: Income and Activity Fields */
        .childcare-ccs-root #step2 #income {
            font-size: {$step2_income_font_size}px !important;
            padding: {$step2_income_padding}px !important;
            font-weight: 600 !important;
        }
        
        .childcare-ccs-root #step2 #activity {
            font-size: {$step2_activity_font_size}px !important;
            padding: {$step2_activity_padding}px !important;
        }
        
        /* Step 3: Child Count and DOB Fields */
        .childcare-ccs-root .child-count-btn {
            font-size: {$step3_child_count_font_size}px !important;
            padding: {$step3_child_count_padding}px !important;
            font-weight: 600 !important;
        }
        
        .childcare-ccs-root input[type='date'] {
            font-size: {$step3_dob_font_size}px !important;
        }
        
        /* Step 4: Summary and Results */
        .childcare-ccs-root .c-summary-table__row,
        .childcare-ccs-root .child-details {
            font-size: {$step4_card_font_size}px !important;
        }
        
        .childcare-ccs-root .c-summary-table__row__value span.value,
        .childcare-ccs-root .week-breakdown__values span.value {
            font-size: {$step4_total_font_size}px !important;
            font-weight: {$step4_total_font_weight} !important;
        }
        
        /* Responsive Design - Mobile Styles */
        @media (max-width: {$mobile_breakpoint}px) {
            .childcare-ccs-root {
                padding: {$mobile_container_padding}px !important;
            }
            
            .childcare-ccs-root h3 {
                font-size: {$mobile_heading_size}px !important;
            }
            
            .childcare-step input[type='text'],
            .childcare-step input[type='number'],
            .childcare-step input[type='date'],
            .childcare-step input[type='email'],
            .childcare-step input[type='tel'],
            .childcare-step select,
            .childcare-step textarea {
                font-size: {$mobile_input_font_size}px !important;
                padding: {$mobile_input_padding}px !important;
                width: {$mobile_input_width}% !important;
                border-width: {$mobile_border_width}px !important;
            }
            
            .childcare-ccs-root .button,
            .childcare-ccs-root .button.button-primary {
                font-size: {$mobile_button_font_size}px !important;
                padding: {$mobile_button_padding}px 16px !important;
                border-width: {$mobile_border_width}px !important;" . 
                ($mobile_button_width === 'full' ? 'width: 100% !important; margin-bottom: 10px !important;' : 
                ($mobile_button_width === 'half' ? 'width: 48% !important; margin: 0 1% 10px 1% !important;' : 
                'width: auto !important;')) . "
            }
            
            .childcare-ccs-root .nav-button.nav-next,
            .childcare-ccs-root .nav-button.nav-back {
                font-size: {$mobile_button_font_size}px !important;
                padding: {$mobile_button_padding}px 16px !important;
                border-width: {$mobile_border_width}px !important;" . 
                ($mobile_button_width === 'full' ? 'width: 100% !important; margin-bottom: 10px !important;' : 
                ($mobile_button_width === 'half' ? 'width: 48% !important; margin: 0 1% 10px 1% !important;' : 
                'width: auto !important;')) . "
            }
            
            .childcare-ccs-root .child-count-btn {
                font-size: {$mobile_button_font_size}px !important;
                padding: {$mobile_button_padding}px !important;
                border-width: {$mobile_border_width}px !important;
                margin: 4px 2px !important;
                width: {$button_width}px !important;
                min-width: {$button_width}px !important;
            }
            
            .childcare-ccs-root .fortnight-day-btn,
            .childcare-ccs-root .fortnight-days .day-button {
                font-size: {$mobile_button_font_size}px !important;
                padding: {$mobile_button_padding}px 12px !important;
                border-width: {$mobile_border_width}px !important;
                min-width: {$button_width}px !important;
            }
            
            .childcare-ccs-root .slider-container {
                margin: 15px 0 !important;
            }
            
            .childcare-ccs-root .child-hours-output {
                font-size: {$mobile_button_font_size}px !important;
            }
            
            .childcare-ccs-root .progress-step {
                width: 30px !important;
                height: 30px !important;
                font-size: 13px !important;
            }
                
            .childcare-ccs-root .progress-label{
                line-height: 12px;
			    font-size: 10px !important;
            }
            .childcare-ccs-root .progress-label.household {
                display: flex;
                flex-flow: column;
            }
            .childcare-ccs-root #childcare-progress {
                align-items: flex-start !important;
            }
            .childcare-ccs-root #step2 div:first-child {
                flex-flow: column !important;
                gap: 0px !important;
            }
            .childcare-ccs-root #step2 div:first-child label {
                width: 100% !important;
            }
        }
        ";
        
        // Add Google Fonts import if needed
        if ($font_family !== 'System Default' && strpos($font_family, 'apple-system') === false) {
            $font_url = str_replace(' ', '+', $font_family);
            $custom_css = "@import url('https://fonts.googleapis.com/css2?family={$font_url}:wght@400;500;600;700&display=swap');\n" . $custom_css;
        }
        
        wp_add_inline_style('childcare-ccs-style', $custom_css);
        
        // Add custom CSS from admin Custom CSS page
        $user_custom_css = get_option('ccs_custom_css', '');
        if (!empty($user_custom_css)) {
            wp_add_inline_style('childcare-ccs-style', $user_custom_css);
        }
    }
}
