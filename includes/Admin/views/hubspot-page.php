<?php
if (!defined('ABSPATH')) { exit; }
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
                </div>

                <h2 style="color:#0073aa;">📋 HubSpot Field Setup (Individual CCS Fields)</h2>
                <div style="background:#e7f5fe; padding:15px; border-left:4px solid #0073aa; margin:20px 0;">
                    <p style="margin-top:0;"><strong>This plugin submits data directly to HubSpot's Forms API using individual fields.</strong> Your HubSpot form should have the following fields configured:</p>
                    
                    <h3>Step 1: Create Contact Properties</h3>
                    <p>In HubSpot, go to <strong>Settings → Properties → Contact Properties</strong> and create the following fields:</p>
                    
                    <h4>Required Fields (Single-line text):</h4>
                    <ul style="columns: 2; -webkit-columns: 2; -moz-columns: 2;">
                        <li><code>firstname</code></li>
                        <li><code>lastname</code></li>
                        <li><code>email</code></li>
                        <li><code>phone</code></li>
                        <li><code>ccs_suburb</code></li>
                        <li><code>ccs_household_income</code></li>
                        <li><code>ccs_activity_level</code></li>
                        <li><code>ccs_activity_hours</code></li>
                        <li><code>ccs_number_of_children</code></li>
                        <li><code>ccs_daily_fee</code></li>
                        <li><code>ccs_days_per_week</code></li>
                        <li><code>ccs_ccs_percentage</code></li>
                        <li><code>ccs_hourly_rate_cap</code></li>
                        <li><code>ccs_annual_subsidy_cap</code></li>
                        <li><code>ccs_fortnight_hours</code></li>
                        <li><code>ccs_subsidy_per_session</code></li>
                        <li><code>ccs_out_of_pocket_per_session</code></li>
                        <li><code>ccs_weekly_subsidy</code></li>
                        <li><code>ccs_weekly_out_of_pocket</code></li>
                        <li><code>ccs_annual_subsidy</code></li>
                        <li><code>ccs_annual_out_of_pocket</code></li>
                        <li><code>ccs_total_annual_fees</code></li>
                        <li><code>ccs_current_period</code></li>
                        <li><code>ccs_cap_remaining</code></li>
                        <li><code>ccs_submission_date</code></li>
                        <li><code>ccs_calculator_version</code></li>
                    </ul>
                    
                    <h4>Child Details (Multi-line text - for up to 5 children):</h4>
                    <ul>
                        <li><code>ccs_child_1_details</code></li>
                        <li><code>ccs_child_2_details</code></li>
                        <li><code>ccs_child_3_details</code></li>
                        <li><code>ccs_child_4_details</code></li>
                        <li><code>ccs_child_5_details</code></li>
                    </ul>

                    <h3>Step 2: Add Fields to Your Form</h3>
                    <ol>
                        <li>Go to <strong>Marketing → Lead Capture → Forms</strong></li>
                        <li>Edit your form</li>
                        <li>Add the required fields from the list above</li>
                        <li><strong>Publish</strong> the form (unpublished forms won't accept API submissions)</li>
                    </ol>

                    <h3>Step 3: Verify Internal Names</h3>
                    <p>To check a field's internal name in HubSpot:</p>
                    <ol>
                        <li>Click on the field in the form editor</li>
                        <li>Look for "Internal name" in the field properties panel</li>
                        <li>The internal name must match EXACTLY (case-sensitive)</li>
                    </ol>
                    
                    <p style="margin-top:15px; padding:10px; background:#d4edda; border-radius:4px;"><strong>✅ Benefits:</strong> Individual fields allow better data segmentation, reporting, and workflow automation in HubSpot.</p>
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
