<?php
if (!defined('ABSPATH')) { exit; }
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
            'disclaimer_text'       => 'This is an estimate only. Final entitlements determined by Services Australia. From 5 January 2026, all CCS eligible families can get at least 72 hours (3 days) of subsidised child care per fortnight under the 3 Day Guarantee.',
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
                    <div class="ccs-settings-card" style="background:#fff; padding:25px; border:1px solid #ddd; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.08);">
                        <h2 style="margin-top:0; padding-bottom:15px; border-bottom:3px solid #0073aa; color:#0073aa; font-size:18px; display:flex; align-items:center;">
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
                    <div class="ccs-settings-card" style="background:#fff; padding:25px; border:1px solid #ddd; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.08);">
                        <h2 style="margin-top:0; padding-bottom:15px; border-bottom:3px solid #0073aa; color:#0073aa; font-size:18px; display:flex; align-items:center;">
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
                </div>
                
                <!-- Additional Settings Card -->
                <div class="ccs-settings-card" style="background:#fff; padding:25px; border:1px solid #ddd; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.08); margin-bottom:20px;">
                    <h2 style="margin-top:0; padding-bottom:15px; border-bottom:3px solid #0073aa; color:#0073aa; font-size:18px; display:flex; align-items:center;">
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
                <div class="ccs-settings-card" style="background:#fff; padding:25px; border:1px solid #ddd; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.08); margin-bottom:20px;">
                    <h2 style="margin-top:0; padding-bottom:15px; border-bottom:3px solid #0073aa; color:#0073aa; font-size:18px; display:flex; align-items:center;">
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

                    <div style="margin-top:25px;">
                        <h3 style="font-size:15px; font-weight:600; margin-bottom:15px; color:#0073aa; display:flex; align-items:center;">
                            <span class="dashicons dashicons-schedule" style="font-size:18px; width:18px; height:18px; margin-right:8px;"></span>
                            Activity Hours to CCS Hours Mapping
                        </h3>
                        <div style="background:#f9f9f9; padding:20px; border-radius:6px; border:1px solid #e0e0e0;">
                            <table style="width:100%; border-collapse:collapse;">
                                <thead>
                                    <tr style="background:#0073aa; color:#fff;">
                                        <th style="text-align:left; padding:12px; font-weight:600; border-radius:4px 0 0 0;">Activity Hours per Fortnight</th>
                                        <th style="text-align:left; padding:12px; font-weight:600; border-radius:0 4px 0 0;">CCS Hours per Fortnight</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="background:#fff; border-bottom:1px solid #e0e0e0;">
                                        <td style="padding:12px; font-weight:500;">8 hours to 16 hours</td>
                                        <td style="padding:12px;">
                                            <input type="number" 
                                                   name="childcare_ccs_policy[ccs_hours_8_16]" 
                                                   value="<?php echo esc_attr($policy['ccs_hours_8_16'] ?? 36); ?>"
                                                   style="width:100px; padding:8px 12px; border:1px solid #ddd; border-radius:4px; font-size:14px;">
                                            <span style="margin-left:8px; color:#666; font-weight:500;">hours</span>
                                        </td>
                                    </tr>
                                    <tr style="background:#fff; border-bottom:1px solid #e0e0e0;">
                                        <td style="padding:12px; font-weight:500;">17 hours to 48 hours</td>
                                        <td style="padding:12px;">
                                            <input type="number" 
                                                   name="childcare_ccs_policy[ccs_hours_17_48]" 
                                                   value="<?php echo esc_attr($policy['ccs_hours_17_48'] ?? 72); ?>"
                                                   style="width:100px; padding:8px 12px; border:1px solid #ddd; border-radius:4px; font-size:14px;">
                                            <span style="margin-left:8px; color:#666; font-weight:500;">hours</span>
                                        </td>
                                    </tr>
                                    <tr style="background:#fff;">
                                        <td style="padding:12px; font-weight:500;">More than 48 hours</td>
                                        <td style="padding:12px;">
                                            <input type="number" 
                                                   name="childcare_ccs_policy[ccs_hours_48_plus]" 
                                                   value="<?php echo esc_attr($policy['ccs_hours_48_plus'] ?? 100); ?>"
                                                   style="width:100px; padding:8px 12px; border:1px solid #ddd; border-radius:4px; font-size:14px;">
                                            <span style="margin-left:8px; color:#666; font-weight:500;">hours</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="description" style="margin-top:10px; color:#666; font-style:italic;">Configure how activity hours map to subsidised CCS hours per fortnight</p>
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

                <!-- Centres Management Card -->
                <div class="ccs-settings-card" style="background:#fff; padding:25px; border:1px solid #ddd; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.08); margin-bottom:20px;">
                    <h2 style="margin-top:0; padding-bottom:15px; border-bottom:3px solid #0073aa; color:#0073aa; font-size:18px; display:flex; align-items:center;">
                        <span class="dashicons dashicons-building" style="font-size:20px; width:20px; height:20px; margin-right:8px;"></span>
                        Childcare Centres List
                    </h2>
                    
                    <div style="margin-top:20px;">
                        <label style="display:block; font-weight:600; margin-bottom:8px; color:#333;">
                            Centres (one per line)
                        </label>
                        <textarea name="ccs_centres_list" 
                                  rows="10" 
                                  placeholder="Enter centre names, one per line&#10;Example:&#10;ABC Learning Centre - Sydney&#10;Little Stars Childcare - Melbourne&#10;Sunshine Kids - Brisbane"
                                  style="width:100%; padding:12px; border:1px solid #ddd; border-radius:4px; font-size:14px; font-family:monospace; resize:vertical;"><?php echo esc_textarea(get_option('ccs_centres_list', '')); ?></textarea>
                        <p class="description" style="margin-top:8px; color:#666;">
                            <span class="dashicons dashicons-info" style="color:#0073aa;"></span>
                            Enter each childcare centre name on a new line. These will appear in the dropdown when users select "Existing family" option.
                        </p>
                    </div>
                </div>

                <!-- Info Box -->
                <div style="background:linear-gradient(135deg, #e7f5fe 0%, #f0f9ff 100%); border-left:5px solid #0073aa; padding:20px 25px; margin-bottom:30px; border-radius:8px; box-shadow:0 2px 4px rgba(0,115,170,0.1);">
                    <p style="margin:0; color:#333; font-size:14px; line-height:1.6;">
                        <span class="dashicons dashicons-info" style="color:#0073aa; margin-right:8px; font-size:20px; vertical-align:middle;"></span>
                        <strong style="color:#0073aa;">Important:</strong> These settings control the calculator's subsidy calculations. Ensure values match current Australian Government CCS policy. 
                        Visit <a href="https://www.servicesaustralia.gov.au/child-care-subsidy" target="_blank" style="color:#0073aa; text-decoration:underline; font-weight:600;">Services Australia</a> for official rates.
                    </p>
                </div>

                <?php submit_button('Save Settings', 'primary large', 'submit', true, ['style' => 'padding:12px 40px; font-size:16px; height:auto; border-radius:6px; box-shadow:0 2px 4px rgba(0,115,170,0.3);']); ?>
            </form>

            <!-- Rules & Instructions Section -->
            <div style="margin-top:40px; padding-top:30px; border-top:2px solid #ddd;">
                <h2 style="font-size:22px; margin-bottom:20px; color:#23282d;">
                    <span class="dashicons dashicons-book" style="font-size:24px; width:24px; height:24px; margin-right:8px;"></span>
                    Calculator Rules & Instructions
                </h2>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    
                    <!-- How CCS is Calculated -->
                    <div style="background:#fff; padding:25px; border:1px solid #ddd; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.08);">
                        <h3 style="margin-top:0; color:#0073aa; font-size:16px; border-bottom:2px solid #0073aa; padding-bottom:12px; display:flex; align-items:center;">
                            <span style="font-size:20px; margin-right:8px;">📊</span> How Child Care Subsidy is Calculated
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
                    <div style="background:#fff; padding:25px; border:1px solid #ddd; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.08);">
                        <h3 style="margin-top:0; color:#0073aa; font-size:16px; border-bottom:2px solid #0073aa; padding-bottom:12px; display:flex; align-items:center;">
                            <span style="font-size:20px; margin-right:8px;">💰</span> Income Thresholds Explained
                        </h3>
                        <ul style="margin:15px 0; padding-left:20px; line-height:1.8; color:#333;">
                            <li><strong>Base Threshold:</strong> Income up to this amount receives maximum 90% subsidy</li>
                            <li><strong>Zero Threshold:</strong> Income above this amount receives 0% subsidy</li>
                            <li><strong>Income Step:</strong> For every $5,000 above base, subsidy drops by 1%</li>
                            <li><strong>Example:</strong> $80,000 = 90%, $85,000 = 89%, $90,000 = 88%</li>
                        </ul>
                    </div>

                    <!-- Hourly Caps Guide -->
                    <div style="background:#fff; padding:25px; border:1px solid #ddd; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.08);">
                        <h3 style="margin-top:0; color:#0073aa; font-size:16px; border-bottom:2px solid #0073aa; padding-bottom:12px; display:flex; align-items:center;">
                            <span style="font-size:20px; margin-right:8px;">⏰</span> Hourly Rate Caps Guide
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
                    <div style="background:#fff; padding:25px; border:1px solid #ddd; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.08);">
                        <h3 style="margin-top:0; color:#0073aa; font-size:16px; border-bottom:2px solid #0073aa; padding-bottom:12px; display:flex; align-items:center;">
                            <span style="font-size:20px; margin-right:8px;">🔄</span> When to Update Settings
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
