<?php
if (!defined('ABSPATH')) { exit; }
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
