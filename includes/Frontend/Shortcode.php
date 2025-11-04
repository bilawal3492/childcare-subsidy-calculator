<?php

namespace CCSCalculator\Includes\Frontend;

if (!defined('ABSPATH')) {
    exit;
}

class Shortcode
{
    public function register()
    {
        add_shortcode('thechildcare_ccs_calculator', [$this, 'render']);
    }

    public function render()
    {
        // No need to load suburbs - will be fetched via AJAX
        $policy = get_option('childcare_ccs_policy', []);
        
        // Get summary page colors from admin settings
        $summary_heading_color = get_option('ccs_summary_heading_color', '#84bd00');
        $total_fee_color = get_option('ccs_total_fee_color', '#0073aa');
        $subsidy_color = get_option('ccs_subsidy_color', '#6b46c1');
        $out_of_pocket_color = get_option('ccs_out_of_pocket_color', '#00bcd4');
        $week_heading_color = get_option('ccs_week_heading_color', '#333333');
        $fee_bg_color = get_option('ccs_fee_bg_color', 'rgba(0, 115, 170, 0.1)');
        $subsidy_bg_color = get_option('ccs_subsidy_bg_color', 'rgba(107, 70, 193, 0.1)');
        $out_of_pocket_bg_color = get_option('ccs_out_of_pocket_bg_color', 'rgba(0, 188, 212, 0.1)');
        
        // Get spinner color from admin settings
        $spinner_color = get_option('ccs_spinner_color', '#3498db');
        
        // Get centres list from admin settings
        $centres_list = get_option('ccs_centres_list', '');
        $centres = array_filter(explode("\n", $centres_list));
        
        ob_start();
        ?>
<style>
@keyframes csc-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Centres dropdown styling */
.centres-dropdown {
    scrollbar-width: thin;
    scrollbar-color: #ccc #f0f0f0;
}

.centres-dropdown::-webkit-scrollbar {
    width: 8px;
}

.centres-dropdown::-webkit-scrollbar-track {
    background: #f0f0f0;
    border-radius: 4px;
}

.centres-dropdown::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 4px;
}

.centres-dropdown::-webkit-scrollbar-thumb:hover {
    background: #999;
}

.centre-option:last-child {
    border-bottom: none !important;
}
</style>

<div id="childcare-ccs-calculator" class="childcare-ccs-root" style="max-width:1080px; margin:0 auto; border:1px solid #ddd; padding:20px; border-radius:8px;">
    <h3>Childcare Subsidy Calculator</h3>


    <!-- Enhanced Progress bar with dynamic line -->
    <div id="childcare-progress" class="progress-container" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; position:relative; padding:0 20px;">
        <!-- Background line -->
        <div class="progress-line-bg" style="position:absolute; top:28%; width: 100%; left:0px; right:0px; height:3px; background:var(--ccs-progress-inactive-color, #e1e8ed); z-index:1; border-radius:3px; transform:translateY(-50%);"></div>
        <!-- Active progress line -->
        <div class="progress-line-active" style="position:absolute; top:28%; left:0px; height:3px; background:var(--ccs-progress-completed-color, #00a32a); z-index:2; border-radius:3px; transform:translateY(-50%); width:0%; transition:width 0.6s cubic-bezier(0.4, 0, 0.2, 1);"></div>
        
        <div class="progress-step-wrapper" style="position:relative; text-align:center; z-index:3; flex:1;">
            <div class="progress-step" data-step="1" id="progress1" style="border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:600; transition:all 0.3s ease; cursor:pointer; margin:0 auto;">1</div>
            <div class="progress-label" style="margin-top:8px; font-weight:500; transition:all 0.3s ease;">Location</div>
        </div>
        <div class="progress-step-wrapper" style="position:relative; text-align:center; z-index:3; flex:1;">
            <div class="progress-step" data-step="2" id="progress2" style="border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:600; transition:all 0.3s ease; cursor:pointer; margin:0 auto;">2</div>
            <div class="progress-label household" style="margin-top:8px; font-weight:500; transition:all 0.3s ease;">Household <span class="mbl_adjust">Income</span></div>
        </div>
        <div class="progress-step-wrapper" style="position:relative; text-align:center; z-index:3; flex:1;">
            <div class="progress-step" data-step="3" id="progress3" style="border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:600; transition:all 0.3s ease; cursor:pointer; margin:0 auto;">3</div>
            <div class="progress-label" style="margin-top:8px; font-weight:500; transition:all 0.3s ease;">Children</div>
        </div>
        <div class="progress-step-wrapper" style="position:relative; text-align:center; z-index:3; flex:1;">
            <div class="progress-step" data-step="4" id="progress4" style="border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:600; transition:all 0.3s ease; cursor:pointer; margin:0 auto;">4</div>
            <div class="progress-label" style="margin-top:8px; font-weight:500; transition:all 0.3s ease;">Summary</div>
        </div>
    </div>


    <!-- Step 1 -->
    <div class="childcare-step" id="step1">
        <div id="suburb-container" style="position:relative; margin-bottom: 20px;">
            <label>Where are you located? (Suburb / Postal Code)<br>
                <input type="text" id="suburb" placeholder="Enter suburb or postal code" autocomplete="off" style="width:100%; line-height: 20px; border-style: solid; margin-top: 10px !important;">
            </label>
            <div id="suburb-suggestions" class="suburb-suggestions" style="display:none;position:absolute;z-index:10;background:#fff;border:1px solid #ccc;width:100%;max-height:300px;overflow-y:auto;">
                <!-- Loader for suburb search -->
                <div id="suburb-loader" class="suburb-loader" style="display:none; padding:20px; text-align:center;">
                    <div class="suburb-loader-spinner" style="width:30px; height:30px; border:3px solid #f3f3f3; border-top:3px solid <?php echo esc_attr($spinner_color); ?>; border-radius:50%; animation:csc-spin 1s linear infinite; margin:0 auto;"></div>
                    <p style="margin-top:10px; color:#666; font-size:14px;">Searching suburbs...</p>
                </div>
            </div>
        </div>
        <p class="atsi" style="margin-bottom: 20px;">
            <label>Is your child Aboriginal or Torres Strait Islander?<br>
                <select id="atsi" style="margin-top: 10px !important;">
                    <option value="">Select</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </label>
        </p>
        
        <p style="font-size: 14px; line-height: 20px; font-weight: 500;">Please select the option that applies to you:</p>
        <div id="enrolment-options" style="margin-bottom:15px; display:flex; gap:10px;">
            <button type="button" class="button enrolment-option" data-value="existing">Existing family</button>
            <button type="button" class="button enrolment-option" data-value="other">With another care provider</button>
            <button type="button" class="button enrolment-option" data-value="none">Not currently enrolled in childcare</button>
        </div>


        <div id="extra-field-wrapper" style="display:none; margin-top:10px;">
            <label id="extra-label" style="margin-bottom: 10px;"></label>
            <input type="text" id="extra-field" style="width:100%; line-height: 20px; border-style: solid;">
        </div>

        <!-- Centres Dropdown (for existing family option) -->
        <div id="centres-dropdown-wrapper" style="display:none; margin-top:10px; position:relative;">
            <label style="display:block; margin-bottom:10px; font-weight:500;">Select Your Centre</label>
            <input type="text" 
                   id="centre-search" 
                   placeholder="Search for your centre..." 
                   autocomplete="off"
                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-size:14px;">
            <div id="centres-dropdown" class="centres-dropdown" style="display:none; position:absolute; z-index:10; background:#fff; border:1px solid #ccc; width:100%; max-height:250px; overflow-y:auto; border-radius:4px; margin-top:2px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                <?php if (!empty($centres)): ?>
                    <?php foreach ($centres as $centre): ?>
                        <div class="centre-option" data-centre="<?php echo esc_attr(trim($centre)); ?>" style="padding:12px; font-size: 16px; line-height: 20px; cursor:pointer; border-bottom:1px solid #f0f0f0; transition:background 0.2s;">
                            <?php echo esc_html(trim($centre)); ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding:12px; font-size: 16px; line-height: 20px; color:#999; text-align:center;">No centres available</div>
                <?php endif; ?>
            </div>
            <input type="hidden" id="selected-centre" value="">
        </div>


        <p class="next-steps" style="margin-top:20px;">
            <button type="button" class="button button-primary nav-button nav-next" id="next1">Next</button>
        </p>
    </div>


    <!-- Step 2 -->
    <div class="childcare-step" id="step2" style="display:none;">
        <!-- Row 1: Do you know CCS Percentage and Activity Hours (always visible) -->
        <div class="house-hold-income-info" style="display:flex; gap:15px; margin-bottom:15px;">
            <label style="width:50%;">Do you know your CCS Percentage?
                <div class="tooltip-container">
                    <span class="tooltip-icon">i</span>
                    <div class="tooltip-content">
                        <strong>Do you know your CCS Percentage?</strong><br><br>
                        If you know your CCS percentage, you can enter it into the 'Child Care Subsidy Percentage' field.
                    </div>
                </div>
                <br>
                <select style="line-height: 20px; border-style: solid; margin-top: 10px !important; width: 100%;" id="know_ccs_percentage">
                    <option value="" selected>Select</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </label>
            <label style="width:50%;">Hours of Recognised Activities per Fortnight
                <div class="tooltip-container">
                    <span class="tooltip-icon">i</span>
                    <div class="tooltip-content">
                        <strong>Hours of Recognised Activities per Fortnight</strong><br><br>
                        The combined hours of work, training, study or other recognised activities undertaken per fortnight.<br><br>
                        The hours of subsidised care the family is entitled to will be determined by the person with the lowest number of hours of activities per fortnight in the family.<br><br>
                        <strong>Note:</strong> Parental or maternity leave (paid or unpaid) is counted as a recognised activity as long as you're expected to return to work after your leave ends. When including this as a recognised activity you should give Centrelink the hours you worked before you started your leave.
                    </div>
                </div>
                <br>
                <select style="line-height: 20px; border-style: solid; margin-top: 10px !important; width: 100%;" id="activity">
                    <option value="48" <?php selected($policy['default_activity_hours'] ?? 48, 48); ?>>More than 48 hours</option>
                    <option value="17" <?php selected($policy['default_activity_hours'] ?? 48, 17); ?>>17 hours to 48 hours</option>
                    <option value="8" <?php selected($policy['default_activity_hours'] ?? 48, 8); ?>>8 hours to 16 hours</option>
                </select>
            </label>
        </div>

        <!-- Row 2: CCS Hours Display and CCS Withholding (always visible) -->
        <div class="house-hold-income-info" style="display:flex; gap:15px; margin-bottom:15px;">
            <label style="width:50%;">Hours of Child Care Subsidy
                <div class="tooltip-container">
                    <span class="tooltip-icon">i</span>
                    <div class="tooltip-content">
                        <strong>Hours of Child Care Subsidy</strong><br><br>
                        The maximum number of hours of Child Care Subsidy (per fortnight) will be decided by the hours of activity undertaken.<br><br>
                        <ul style="list-style-type: '• '; padding-left: 15px;">
                            <li><strong>8 hours to 16 hours</strong> of activity (per fortnight): maximum 36 hours of subsidy (per fortnight)</li>
                            <li><strong>17 hours to 48 hours</strong> of activity (per fortnight): maximum 72 hours of subsidy (per fortnight)</li>
                            <li><strong>more than 48 hours</strong> of activity (per fortnight): maximum 100 hours of subsidy (per fortnight)</li>
                        </ul>
                        <strong>Low income families</strong> on $85,279 or less a year will be able to access 24 hours of subsidised care per fortnight without having to meet the activity test.<br><br>
                        If you don't meet the activity test and you have a preschool aged child attending preschool in a centre based day care service, you can access 36 hours of subsidised care per fortnight.
                    </div>
                </div>
                <br>
                <input style="line-height: 20px; border-style: solid; margin-top: 10px !important; width: 100%; background-color: #f0f0f0;" type="text" id="ccs_hours_display" readonly value="Up to <?php echo esc_attr($policy['ccs_hours_48_plus'] ?? 100); ?> hours per fortnight">
            </label>
            <label style="width:50%;">CCS Withholding Percentage
                <div class="tooltip-container">
                    <span class="tooltip-icon">i</span>
                    <div class="tooltip-content">
                        <strong>CCS Withholding Percentage</strong><br><br>
                        The withholding percentage is the percentage of your Child Care Subsidy that is held back by Services Australia to help you avoid a debt at the end of the financial year.
                    </div>
                </div>
                <br>
                <select style="line-height: 20px; border-style: solid; margin-top: 10px !important; width: 100%;" id="ccs_withholding_percentage">
                    <option value="5" <?php selected($policy['default_withholding'] ?? 5, 5); ?>>5%</option>
                    <option value="4" <?php selected($policy['default_withholding'] ?? 5, 4); ?>>4%</option>
                    <option value="3" <?php selected($policy['default_withholding'] ?? 5, 3); ?>>3%</option>
                    <option value="2" <?php selected($policy['default_withholding'] ?? 5, 2); ?>>2%</option>
                    <option value="1" <?php selected($policy['default_withholding'] ?? 5, 1); ?>>1%</option>
                    <option value="0" <?php selected($policy['default_withholding'] ?? 5, 0); ?>>0%</option>
                </select>
            </label>
        </div>

        <!-- Fields shown when user KNOWS their CCS percentage (Yes) -->
        <div class="house-hold-income-info" id="ccs_known_fields" style="display:none;">
            <!-- Row 3: Standard and Higher CCS Percentage -->
            <div style="display:flex; gap:15px; margin-bottom:15px;">
                <label style="width:50%;">Standard Child Care Subsidy Percentage
                    <div class="tooltip-container">
                        <span class="tooltip-icon">i</span>
                        <div class="tooltip-content">
                            <strong>Standard Child Care Subsidy Percentage</strong><br><br>
                            Subsidy per cent of the actual fee charged (up to relevant percentage of the hourly fee cap).<br><br>
                            The maximum hourly fee cap by service type is list below:<br><br>
                            Centre Based Day Care: $14.63^;<br><br>
                            Family Day Care: $13.56^;<br><br>
                            Outside School Hours Care: $12.81^.<br><br>
                            ^ These amounts are correct for 2025-2026 (FY2026) and may be subject to adjustment through indexation in subsequent years.
                        </div>
                    </div>
                    <br>
                    <div style="position:relative; margin-top: 10px;">
                        <input style="line-height: 20px; border-style: solid; width:100%; padding-right:30px;" type="number" id="standard_ccs_percentage" min="0" max="100" step="0.01" placeholder="Enter a value">
                        <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); color:#666; pointer-events:none;">%</span>
                    </div>
                </label>
                <label style="width:50%;">Higher Child Care Subsidy Percentage
                    <div class="tooltip-container">
                        <span class="tooltip-icon">i</span>
                        <div class="tooltip-content">
                            <strong>Higher Child Care Subsidy Percentage</strong><br><br>
                            Subsidy per cent of the actual fee charged (up to relevant percentage of the hourly fee cap).<br><br>
                            The maximum hourly fee cap by service type is list below:<br><br>
                            Centre Based Day Care: $14.63^;<br><br>
                            Family Day Care: $13.56^;<br><br>
                            Outside School Hours Care: $12.81^.<br><br>
                            Families with more than one child aged 5 or under, with income less than $367,563 will get a higher rate for their second and younger children.<br><br>
                            ^ These amounts are correct for 2025-2026 (FY2026) and may be subject to adjustment through indexation in subsequent years.
                        </div>
                    </div>
                    <br>
                    <div style="position:relative; margin-top: 10px;">
                        <input style="line-height: 20px; border-style: solid; width:100%; padding-right:30px; background-color: #f0f0f0;" type="number" id="higher_ccs_percentage" min="0" max="100" step="0.01" placeholder="0.00" readonly>
                        <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); color:#666; pointer-events:none;">%</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Fields shown when user DOESN'T know their CCS percentage (No) -->
        <div class="house-hold-income-info" id="ccs_unknown_fields" style="display:none;">
            <!-- Row 3: Family Adjusted Taxable Income (single field) -->
            <div class="tax-income" style="display:flex; gap:15px; margin-bottom:15px;">
                <label style="width:50%;">Family Adjusted Taxable Income
                    <div class="tooltip-container">
                        <span class="tooltip-icon">i</span>
                        <div class="tooltip-content">
                            <strong>Family Adjusted Taxable Income (ATI)</strong><br><br>
                            For the purposes of Family Assistance, an individual's Adjusted Tax Income for a financial year is the sum of the following amounts for that year:
                            <ul style="list-style-type: '• '; padding-left: 15px;">
                                <li>taxable income</li>
                                <li>the value of any adjusted fringe benefits</li>
                                <li>target foreign income (including tax exempt foreign employment income)</li>
                                <li>total net investment loss</li>
                                <li>tax free pension or benefit</li>
                                <li>reportable superannuation contributions</li>
                            </ul>
                            Then subtracting 100% of the individual's child maintenance expenditure.
                        </div>
                    </div>
                    <br>
                    <div style="position:relative; margin-top: 10px;">
                        <input style="line-height: 20px; border-style: solid; width:100%; padding-left:50px !important;" type="number" id="family_ati" min="0" step="1000" placeholder="00.00">
                        <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#666; pointer-events:none;">AU$</span>
                    </div>
                </label>
                <label style="width:50%;">Standard Child Care Subsidy Percentage
                    <div class="tooltip-container">
                        <span class="tooltip-icon">i</span>
                        <div class="tooltip-content">
                            <strong>Standard Child Care Subsidy Percentage</strong><br><br>
                            Subsidy per cent of the actual fee charged (up to relevant percentage of the hourly fee cap).<br><br>
                            The maximum hourly fee cap by service type is list below:<br><br>
                            Centre Based Day Care: $14.63^;<br><br>
                            Family Day Care: $13.56^;<br><br>
                            Outside School Hours Care: $12.81^.<br><br>
                            ^ These amounts are correct for 2025-2026 (FY2026) and may be subject to adjustment through indexation in subsequent years.
                        </div>
                    </div>
                    <br>
                    <div style="position:relative; margin-top: 10px;">
                        <input style="line-height: 20px; border-style: solid; width:100%; padding-right:30px; background-color: #f0f0f0;" type="number" id="standard_ccs_percentage_calc" min="0" max="100" step="0.01" value="90.00" readonly>
                        <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); color:#666; pointer-events:none;">%</span>
                    </div>
                </label>
            </div>

            <!-- Row 4: Standard and Higher CCS Percentage (calculated) -->
            <div class="higher-percent" style="display:flex; gap:15px; margin-bottom:15px;">
                <label style="width:50%;">Higher Child Care Subsidy Percentage
                    <div class="tooltip-container">
                        <span class="tooltip-icon">i</span>
                        <div class="tooltip-content">
                            <strong>Higher Child Care Subsidy Percentage</strong><br><br>
                            Subsidy per cent of the actual fee charged (up to relevant percentage of the hourly fee cap).<br><br>
                            The maximum hourly fee cap by service type is list below:<br><br>
                            Centre Based Day Care: $14.63^;<br><br>
                            Family Day Care: $13.56^;<br><br>
                            Outside School Hours Care: $12.81^.<br><br>
                            Families with more than one child aged 5 or under, with income less than $367,563 will get a higher rate for their second and younger children.<br><br>
                            ^ These amounts are correct for 2025-2026 (FY2026) and may be subject to adjustment through indexation in subsequent years.
                        </div>
                    </div>
                    <br>
                    <div style="position:relative; margin-top: 10px;">
                        <input style="line-height: 20px; border-style: solid; width:100%; padding-right:30px; background-color: #f0f0f0;" type="number" id="higher_ccs_percentage_calc" min="0" max="100" step="0.01" value="95.00" readonly>
                        <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); color:#666; pointer-events:none;">%</span>
                    </div>
                </label>
                <div class="empty_space" style="width:50%;"></div>
            </div>
        </div>

        <p class="next-steps">
            <button type="button" class="button nav-button nav-back" id="back2">Back</button>
           <button type="button" class="button button-primary nav-button nav-next" id="next2">Next</button>
        </p>
    </div>


    <!-- Step 3 -->
    <div class="childcare-step" id="step3" style="display:none;">
        <p>How many children will you have in long child care?</p>
        <div id="child-buttons" style="margin-bottom:10px;">
            <?php for($i=1;$i<=5;$i++): ?>
                <button type="button" class="button child-count-btn" data-count="<?php echo $i; ?>"><?php echo $i; ?></button>
            <?php endfor; ?>
        </div>
        <div id="children-details"></div>
        <p class="next-steps">
            <button type="button" class="button nav-button nav-back" id="back3">Back</button>
            <button type="button" class="button button-primary nav-button nav-next" id="next3">Next</button>
        </p>
    </div>


    <!-- Step 4 Summary -->
    <div class="childcare-step" id="step4" style="display:none;">
        <div id="childcare-loader" style="text-align:center; padding:40px 20px; display:none; background:#f9f9f9; border-radius:8px; margin-bottom:30px;">
            <div class="childcare-spinner" style="width:50px;height:50px;margin:0 auto 20px auto;border:5px solid #f3f3f3;border-top-color:<?php echo esc_attr($spinner_color); ?>;border-radius:50%;animation:csc-spin 1s linear infinite;"></div>
            <p style="margin:0; font-size:16px; color:#666; font-weight:500;">Calculating your childcare subsidy...</p>
            <p style="margin:10px 0 0 0; font-size:14px; color:#999;">Please wait a moment</p>
        </div>

        <div id="summary-content" style="display:none;">
            <div id="child-details-summary" style="display: flex; gap: 20px; flex-wrap: wrap; margin: 30px 0px 10px 0px;"></div>

        <!-- Household Income Information -->
        <div id="household-income-info"></div>

        <!-- First row: period buttons + child dropdown -->
        <div style="display:flex; flex-wrap: wrap; justify-content: space-between; margin-bottom:30px; gap:10px;">
            <div class="show_total">
                <strong style="font-size: 16px; line-height: 20px;">Show Total For:</strong>
                <div class="show_total_btns">
                    <button type="button" class="summary-btn button button-primary nav-button nav-next" data-period="fortnight">Fortnightly</button>
                    <button type="button" class="summary-btn button nav-button nav-next" data-period="week">Weekly</button>
                    <button type="button" class="summary-btn button nav-button nav-next" data-period="month">Monthly</button>
                    <button type="button" class="summary-btn button nav-button nav-next" data-period="year">Yearly</button>
                </div>
            </div>
            <div id="child-select-wrapper" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <label for="child-select" style="margin-bottom: 0px !important; font-size: 16px !important; line-height: 20px !important;"><strong>Select Child:</strong></label>
                <select id="child-select" style="padding:5px 10px !important; width: unset !important"></select>
            </div>
        </div>


        <!-- Second row: summary columns -->
        <div style="display:flex; flex-wrap:wrap; gap:20px;">
            <div style="flex:1;">
                <h4 id="summary-title" style="margin-bottom: 20px;">All Children</h4>
                <div id="summary-overall">
                    Total fees: $0.00<br>
                    Estimated subsidy: $0.00<br>
                    Out-of-pocket costs: $0.00
            </div>
            </div>
            <div style="flex:1;">
                <div id="summary-weekly">
                    <p>Week 1<br>Total fee: $0<br>Est. subsidy: $0<br>Out-of-pocket: $0</p>
                    <p>Week 2<br>Total fee: $0<br>Est. subsidy: $0<br>Out-of-pocket: $0</p>
                </div>
            </div>
        </div>


        <?php if(get_option('ccs_info_box_enabled', 1)): ?>
        <!-- Info Box -->
        <div class="ccs-info-box-top" style="margin-top:30px; display:flex; gap:15px; align-items:center;">
            <div style="flex-shrink:0;">
                <?php 
                $info_icon_image = get_option('ccs_info_box_icon_image', '');
                if ($info_icon_image): ?>
                    <img src="<?php echo esc_url($info_icon_image); ?>" 
                         alt="Info" 
                         style="width:60px; height:60px; object-fit:contain;">
                <?php else: ?>
                    <svg style="width:60px; height:60px; fill:<?php echo esc_attr(get_option('ccs_info_box_icon_color', '#f7b731')); ?>;" viewBox="0 0 200 200">
                        <!-- Lightbulb Icon -->
                        <circle cx="100" cy="100" r="95" fill="currentColor"/>
                        <g fill="#4a4a4a">
                            <!-- Bulb -->
                            <path d="M100,45 C85,45 73,57 73,72 C73,82 78,91 85,96 L85,115 L115,115 L115,96 C122,91 127,82 127,72 C127,57 115,45 100,45 Z"/>
                            <!-- Base lines -->
                            <rect x="85" y="120" width="30" height="8" rx="2"/>
                            <rect x="85" y="132" width="30" height="8" rx="2"/>
                            <rect x="90" y="144" width="20" height="8" rx="2"/>
                        </g>
                    </svg>
                <?php endif; ?>
            </div>
            <div class="info-box-text" style="flex:1; color:<?php echo esc_attr(get_option('ccs_info_box_text_color', '#333333')); ?>; font-size:16px; line-height:1.6;">
                <?php echo wp_kses_post(get_option('ccs_info_box_text', 'From January 2026, all families who are eligible for CCS can attend a minimum of 3 days per week (or 72 hours per fortnight) of subsidised care, regardless of their activity level.')); ?>
            </div>
        </div>
        <?php endif; ?>

        <div id="summary-email-form" style="margin-top:30px; border-radius:6px; overflow:hidden;">
            <!-- Toggle Header -->
            <div id="email-toggle-header" style="padding:10px 20px; cursor:pointer; display:flex; justify-content:space-between; align-items:center; background:<?php echo esc_attr(get_option('ccs_email_toggle_bg_color', '#d9d9d9')); ?>; color:<?php echo esc_attr(get_option('ccs_email_toggle_text_color', '#333333')); ?>;">
                <div style="display:flex; align-items:center; gap:15px;">
                    <?php 
                    $email_icon_image = get_option('ccs_email_toggle_icon_image', '');
                    if ($email_icon_image): ?>
                        <img src="<?php echo esc_url($email_icon_image); ?>" 
                             alt="Email" 
                             style="width:24px; height:24px; object-fit:contain;">
                    <?php else: ?>
                        <svg class="i-email c-accordion__trigger__text__icon" style="width:24px; height:24px; fill:<?php echo esc_attr(get_option('ccs_email_toggle_icon_color', '#f7941d')); ?>;"><use xlink:href="/svg/symbol-defs-ccs-calc.svg#icon-email"></use></svg>
                    <?php endif; ?>
                    <div>
                        <h4 style="margin:0; font-size:18px; font-weight:600;"><?php echo esc_html(get_option('ccs_email_toggle_title', 'Email me my results')); ?></h4>
                    </div>
                </div>
                <span id="email-toggle-icon" style="font-size:16px; transition:transform 0.3s ease; color:<?php echo esc_attr(get_option('ccs_email_toggle_text_color', '#333333')); ?>;">▼</span>
            </div>
            
            <!-- Form Content (Hidden by default) -->
            <div id="email-form-content" style="display:none; padding:20px; background:<?php echo esc_attr(get_option('ccs_email_form_bg_color', '#f9f9f9')); ?>;">
                <?php if(get_option('ccs_email_toggle_subtitle')): ?>
                <p style="margin-bottom:15px; color:#666;"><?php echo esc_html(get_option('ccs_email_toggle_subtitle', '')); ?></p>
                <?php endif; ?>
                
                <!-- Form Container (HubSpot or Custom) -->
                <div id="form-container">
                <div id="hubspot-form-container"></div>
                <div id="custom-form-container" style="display:none;">
                    <form id="custom-summary-form">
                        <div style="display:flex; flex-wrap:wrap; gap:15px; margin-bottom:15px;">
                            <div style="flex:1; min-width:250px;">
                                <input style="width:100%; padding:12px; border:1px solid #ccc; border-radius:4px; font-size:14px;" 
                                       type="text" 
                                       id="custom_firstname" 
                                       name="firstname" 
                                       placeholder="*First name"
                                       required>
                            </div>
                            <div style="flex:1; min-width:250px;">
                                <input style="width:100%; padding:12px; border:1px solid #ccc; border-radius:4px; font-size:14px;" 
                                       type="text" 
                                       id="custom_lastname" 
                                       name="lastname" 
                                       placeholder="*Last name"
                                       required>
                            </div>
                        </div>
                        
                        <div style="display:flex; flex-wrap:wrap; gap:15px; margin-bottom:15px;">
                            <div style="flex:1; min-width:250px;">
                                <input style="width:100%; padding:12px; border:1px solid #ccc; border-radius:4px; font-size:14px;" 
                                       type="email" 
                                       id="custom_email" 
                                       name="email" 
                                       placeholder="*Email"
                                       required>
                            </div>
                            <div style="flex:1; min-width:250px;">
                                <input style="width:100%; padding:12px; border:1px solid #ccc; border-radius:4px; font-size:14px;" 
                                       type="tel" 
                                       id="custom_phone" 
                                       name="phone" 
                                       placeholder="Phone">
                            </div>
                        </div>
                        
                        <div style="margin-bottom:15px;">
                            <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                                <input type="checkbox" 
                                       id="custom_privacy" 
                                       name="privacy" 
                                       required
                                       style="width:18px; height:18px; cursor:pointer;">
                                <span style="font-size:14px; color:#666;">
                                    <?php 
                                    $privacy_text = get_option('ccs_privacy_policy_text', "I agree to Goodstart's Privacy Policy*");
                                    $privacy_url = get_option('ccs_privacy_policy_url', '#');
                                    
                                    // Replace "Privacy Policy" with link
                                    if (strpos($privacy_text, 'Privacy Policy') !== false) {
                                        echo str_replace(
                                            'Privacy Policy', 
                                            '<a href="' . esc_url($privacy_url) . '" target="_blank" style="color:#0073aa; text-decoration:underline;">Privacy Policy</a>', 
                                            esc_html($privacy_text)
                                        );
                                    } else {
                                        echo esc_html($privacy_text);
                                    }
                                    ?>
                                </span>
                            </label>
                        </div>
                        
                        <div style="margin-bottom:20px;">
                            <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                                <input type="checkbox" 
                                       id="custom_contact" 
                                       name="contact"
                                       style="width:18px; height:18px; cursor:pointer;">
                                <span style="font-size:14px; color:#666;">
                                    <?php echo esc_html(get_option('ccs_contact_checkbox_text', 'I would like to be contacted to find out more about potential savings.')); ?>
                                </span>
                            </label>
                        </div>
                        
                        <div style="text-align:right;">
                            <button type="submit" 
                                    class="button button-primary nav-button nav-next" 
                                    style="padding:12px 40px; font-size:16px; font-weight:600;">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
                </div>
                
                <div id="send-summary-response" style="margin-top:10px;"></div>
            </div>
        </div>

        </div><!-- End summary-content -->

    </div>


    <p style="margin:0px; font-size:12px; color:#666;">
        <?php echo esc_html($policy['disclaimer_text'] ?? ''); ?><br>
        <em>Last updated: <?php echo esc_html($policy['last_updated'] ?? ''); ?></em>
    </p>
</div>


<!-- HubSpot Forms Script -->
<?php 
$hubspot_region = get_option('ccs_hubspot_region', 'na1');
$hubspot_script_url = "https://js.hsforms.net/forms/embed/v2.js";
if ($hubspot_region === 'eu1') {
    $hubspot_script_url = "https://js-eu1.hsforms.net/forms/embed/v2.js";
}
?>
<script charset="utf-8" type="text/javascript" src="<?php echo esc_url($hubspot_script_url); ?>"></script>


<script>
jQuery(document).ready(function($){
    const policy = <?php echo json_encode($policy ?: []); ?>;
    const hourly_caps = policy.hourly_caps || {};
    
    // Summary page colors from admin settings
    const summaryColors = {
        heading: '<?php echo esc_js($summary_heading_color); ?>',
        totalFee: '<?php echo esc_js($total_fee_color); ?>',
        subsidy: '<?php echo esc_js($subsidy_color); ?>',
        outOfPocket: '<?php echo esc_js($out_of_pocket_color); ?>',
        weekHeading: '<?php echo esc_js($week_heading_color); ?>',
        feeBg: '<?php echo esc_js($fee_bg_color); ?>',
        subsidyBg: '<?php echo esc_js($subsidy_bg_color); ?>',
        outOfPocketBg: '<?php echo esc_js($out_of_pocket_bg_color); ?>'
    };
    
    let numChildren = 0;
    let childrenData = [];
    let enrolmentSelection = '';
    let extraAnswer = '';

    // Helper function to format currency with commas
    function formatCurrency(amount) {
        return parseFloat(amount).toLocaleString('en-AU', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Function to get enrollment option text with extra field
    function getEnrollmentOptionText() {
        const currentExtraAnswer = $('#extra-field').val().trim();
        const selectedButton = $('#enrolment-options .button-primary');
        const selectedValue = selectedButton.data('value');
        
        
        if (selectedValue === 'existing' || enrolmentSelection === 'existing') {
            return 'Existing family' + (currentExtraAnswer ? ' - ' + currentExtraAnswer : '');
        } else if (selectedValue === 'other' || enrolmentSelection === 'other') {
            return 'With another care provider' + (currentExtraAnswer ? ' - ' + currentExtraAnswer : '');
        } else if (selectedValue === 'none' || enrolmentSelection === 'none') {
            return 'Not currently enrolled in childcare';
        } else {
            return selectedButton.text() || 'Not specified';
        }
    }

    function setActiveStep(step){
        const totalSteps = 4;
        
        $('#childcare-progress .progress-step').each(function(i){
            const idx = i + 1;
            const $step = $(this);
            const $label = $step.siblings('.progress-label');
            
            // Remove all classes
            $step.removeClass('active completed');
            
            if(idx < step) {
                // Completed steps
                $step.addClass('completed');
                $step.css({
                    'background': 'var(--ccs-progress-completed-color, #00a32a)',
                    'color': '#fff',
                    'border-color': 'var(--ccs-progress-completed-color, #00a32a)',
                    'transform': 'scale(1)'
                });
                $step.html('✓');
                $label.css({
                    'color': 'var(--ccs-progress-completed-color, #00a32a)',
                    'font-weight': '600'
                });
            } else if(idx === step) {
                // Active step
                $step.addClass('active');
                $step.css({
                    'background': 'var(--ccs-progress-active-color, #0073aa)',
                    'color': '#fff',
                    'border-color': 'var(--ccs-progress-active-color, #0073aa)',
                    'transform': 'scale(1.1)',
                    'box-shadow': '0 0 0 4px rgba(0, 115, 170, 0.2)'
                });
                $step.html(idx);
                $label.css({
                    'color': 'var(--ccs-progress-active-color, #0073aa)',
                    'font-weight': '700',
                    'transform': 'scale(1.05)'
                });
            } else {
                // Future steps
                $step.css({
                    'background': 'var(--ccs-progress-inactive-color, #e1e8ed)',
                    'color': '#999',
                    'border-color': 'transparent',
                    'transform': 'scale(1)',
                    'box-shadow': 'none'
                });
                $step.html(idx);
                $label.css({
                    'color': '#999',
                    'font-weight': '500',
                    'transform': 'scale(1)'
                });
            }
        });
        
        // Update progress line width
        const progressPercentage = ((step - 1) / (totalSteps - 1)) * 100;
        $('.progress-line-active').css('width', progressPercentage + '%');
    }
    setActiveStep(1);


    // Step navigation
    $('#childcare-progress').on('click', '.progress-step', function(){
        const idx=$(this).index()+1;
        const current=$('#childcare-progress .progress-step.active').index()+1;
        if(idx<=current){ $('.childcare-step').hide(); $('#step'+idx).show(); setActiveStep(idx); }
    });


    // Enrolment option buttons
    $('#enrolment-options').on('click', '.enrolment-option', function(){
        $('#enrolment-options .enrolment-option').removeClass('button-primary');
        $(this).addClass('button-primary');


        enrolmentSelection = $(this).data('value');


        if (enrolmentSelection === 'existing') {
            // Show centres dropdown instead of text field
            $('#extra-field-wrapper').hide();
            $('#centres-dropdown-wrapper').show();
            $('#centre-search').val('');
            $('#selected-centre').val('');
        } else if (enrolmentSelection === 'other') {
            $('#extra-label').text('Please enter your current care provider:');
            $('#extra-field-wrapper').show();
            $('#centres-dropdown-wrapper').hide();
        } else {
            $('#extra-field-wrapper').hide();
            $('#centres-dropdown-wrapper').hide();
            $('#extra-field').val('');
            $('#selected-centre').val('');
        }
    });

    // Centre search functionality
    $('#centre-search').on('focus', function(){
        $('#centres-dropdown').show();
    });

    $('#centre-search').on('input', function(){
        const searchTerm = $(this).val().toLowerCase();
        $('.centre-option').each(function(){
            const centreName = $(this).text().toLowerCase();
            if (centreName.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Centre selection
    $(document).on('click', '.centre-option', function(){
        const centreName = $(this).data('centre');
        $('#centre-search').val(centreName);
        $('#selected-centre').val(centreName);
        $('#centres-dropdown').hide();
    });

    // Hide dropdown when clicking outside
    $(document).on('click', function(e){
        if (!$(e.target).closest('#centres-dropdown-wrapper').length) {
            $('#centres-dropdown').hide();
        }
    });

    // Hover effect for centre options
    $(document).on('mouseenter', '.centre-option', function(){
        $(this).css('background', '#f0f0f0');
    }).on('mouseleave', '.centre-option', function(){
        $(this).css('background', '#fff');
    });


    // Suburb autocomplete with AJAX
    let suburbSearchTimeout;
    $('#suburb').on('input', function(){
        const val = $(this).val().trim();
        const $suggest = $('#suburb-suggestions');
        const $loader = $('#suburb-loader');
        
        if(val.length < 2) {
            $suggest.hide();
            $loader.hide();
            return;
        }
        
        // Clear previous timeout
        clearTimeout(suburbSearchTimeout);
        
        // Show dropdown and loader
        $suggest.show();
        $loader.show();
        // Hide any previous results
        $('.suburb-suggestion').remove();
        
        // Debounce AJAX request
        suburbSearchTimeout = setTimeout(function() {
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'GET',
                data: {
                    action: 'ccs_search_suburbs',
                    q: val
                },
                success: function(response) {
                    // Hide loader
                    $loader.hide();
                    
                    // Remove any previous results
                    $('.suburb-suggestion').remove();
                    
                    if(response.success && response.data.length > 0) {
                        response.data.forEach(function(item) {
                            $suggest.append(
                                $('<div>')
                                    .addClass('suburb-suggestion')
                                    .attr('data-suburb', item.suburb)
                                    .attr('data-postcode', item.postcode)
                                    .attr('data-state', item.state)
                                    .html(`<span class="suburb-name">${item.suburb}</span><span class="suburb-postcode">${item.postcode}</span>`)
                            );
                        });
                    } else {
                        $suggest.append('<div class="no-results" style="padding:12px; color:#999;">No suburbs found</div>');
                    }
                },
                error: function() {
                    $loader.hide();
                    $('.suburb-suggestion').remove();
                    $suggest.append('<div class="error-message" style="padding:12px; color:#d00;">Search failed. Please try again.</div>');
                }
            });
        }, 300); // 300ms debounce
    });
    
    $(document).on('click','.suburb-suggestion', function(){ 
        const suburb = $(this).data('suburb');
        const postcode = $(this).data('postcode');
        const state = $(this).data('state');
        $('#suburb').val(`${suburb} — ${postcode}`);
        $('#suburb').data('suburb', suburb);
        $('#suburb').data('postcode', postcode);
        $('#suburb').data('state', state);
        $('#suburb-suggestions').hide();
    });
    
    $(document).on('click', function(e){ 
        if(!$(e.target).closest('#suburb-container').length) $('#suburb-suggestions').hide(); 
    });


    // Helper function to show inline error messages
    function showError(fieldSelector, message) {
        const $field = $(fieldSelector);
        // Remove any existing error message
        $field.siblings('.error-message').remove();
        // Add error styling to field
        $field.css('border-color', '#d63638');
        // Add error message below field
        $field.after('<div class="error-message" style="color: #d63638; font-size: 13px; margin-top: 5px; font-weight: 500; line-height: 16px;">' + message + '</div>');
        // Scroll to the field
        $('html, body').animate({
            scrollTop: $field.offset().top - 100
        }, 300);
    }

    function clearError(fieldSelector) {
        const $field = $(fieldSelector);
        $field.css('border-color', '');
        $field.siblings('.error-message').remove();
    }

    // Clear errors on input
    $('#suburb, #atsi, #know_ccs_percentage, #family_ati, #standard_ccs_percentage, #higher_ccs_percentage, #ccs_withholding_percentage, #activity, #extra-field').on('input change', function() {
        clearError(this);
    });
    
    // Clear errors on dynamically created child fields
    $(document).on('input change', '.child-dob, .child-fee', function() {
        clearError(this);
    });

    // Handle CCS Percentage selection
    $('#know_ccs_percentage').on('change', function() {
        const knowsCCS = $(this).val();
        
        if (knowsCCS === 'yes') {
            $('#ccs_known_fields').show();
            $('#ccs_unknown_fields').hide();
            // Clear values in hidden fields
            $('#family_ati').val('');
        } else if (knowsCCS === 'no') {
            $('#ccs_known_fields').hide();
            $('#ccs_unknown_fields').show();
            // Clear values in hidden fields
            $('#standard_ccs_percentage, #higher_ccs_percentage').val('');
        } else {
            $('#ccs_known_fields').hide();
            $('#ccs_unknown_fields').hide();
        }
    });

    // Higher CCS lookup table based on Standard CCS
    const higherCCSLookup = {
        33: 50.00, 34: 50.00, 35: 50.00,
        36: 50.76, 37: 52.43, 38: 54.09, 39: 55.76, 40: 57.43,
        41: 59.09, 42: 60.76, 43: 62.43, 44: 64.09, 45: 65.76,
        46: 67.43, 47: 69.09, 48: 70.76, 49: 72.43, 50: 74.09,
        51: 75.76, 52: 77.43, 53: 79.09, 54: 80.00, 55: 80.00,
        56: 80.00, 57: 80.00, 58: 80.00, 59: 80.00, 60: 80.00,
        61: 80.00, 62: 80.00, 63: 80.00, 64: 80.00, 65: 80.00,
        66: 80.00, 67: 80.00, 68: 80.00, 69: 80.00, 70: 81.00,
        71: 82.66, 72: 84.33, 73: 86.00, 74: 87.66, 75: 89.33,
        76: 91.00, 77: 92.66, 78: 94.33, 79: 95.00, 80: 95.00,
        81: 95.00, 82: 95.00, 83: 95.00, 84: 95.00, 85: 95.00,
        86: 95.00, 87: 95.00, 88: 95.00, 89: 95.00, 90: 95.00
    };

    // Auto-calculate Higher CCS when Standard CCS is entered (Yes mode)
    $('#standard_ccs_percentage').on('input', function() {
        const standardCCS = parseFloat($(this).val());
        const $higherField = $('#higher_ccs_percentage');
        const $standardField = $(this);
        
        // Clear any previous error
        clearError('#standard_ccs_percentage');
        
        if (!isNaN(standardCCS)) {
            // Validation: Check if > 90%
            if (standardCCS > 90) {
                showError('#standard_ccs_percentage', 'The Standard Child Care Subsidy Percentage should be no more than 90%!');
                $higherField.val('');
                return;
            }
            
            // Validation: Check if < 33%
            if (standardCCS < 33 && standardCCS > 0) {
                $higherField.val('0.00');
                // Optionally show info message
                return;
            }
            
            // Lookup Higher CCS from table
            if (standardCCS >= 33 && standardCCS <= 90) {
                const roundedStandard = Math.round(standardCCS);
                const higherCCS = higherCCSLookup[roundedStandard] || 0;
                $higherField.val(higherCCS.toFixed(2));
            } else if (standardCCS === 0) {
                $higherField.val('0.00');
            } else {
                $higherField.val('');
            }
        } else {
            $higherField.val('');
        }
    });

    // Auto-calculate CCS percentages when income is entered (No mode)
    $('#family_ati').on('input', function() {
        const income = parseFloat($(this).val());
        if (!isNaN(income) && income >= 0) {
            calculateCCSPercentages(income);
        }
    });

    // Auto-update CCS Hours display when activity hours dropdown changes
    $('#activity').on('change', function() {
        const activityHours = parseFloat($(this).val());
        let ccsHours = 0;
        
        if (activityHours >= 48) {
            ccsHours = policy.ccs_hours_48_plus || 100;
        } else if (activityHours >= 17) {
            ccsHours = policy.ccs_hours_17_48 || 72;
        } else if (activityHours >= 8) {
            ccsHours = policy.ccs_hours_8_16 || 36;
        } else {
            ccsHours = 0;
        }
        
        $('#ccs_hours_display').val('Up to ' + ccsHours + ' hours per fortnight');
    });

    // Function to calculate CCS percentages based on income
    function calculateCCSPercentages(income) {
        const base = parseFloat(policy.income_base_threshold) || 0;
        const zero = parseFloat(policy.income_zero_threshold) || 0;
        const step = parseFloat(policy.income_step) || 1;
        const max_pct = parseFloat(policy.max_pct) || 0;
        const lowIncomeThreshold = parseFloat(policy.low_income_threshold) || 85279;
        
        let standardCCS = 0;
        
        // Special case: Low income families (threshold from admin settings)
        if (income <= lowIncomeThreshold) {
            standardCCS = 90; // 90% for low income families
        } else if (income <= base) {
            // Income between $85,279 and base threshold
            standardCCS = max_pct * 100; // Convert to percentage
        } else if (income >= zero) {
            // Income at or above zero threshold
            standardCCS = 0;
        } else {
            // Income between base and zero threshold - calculate based on steps
            standardCCS = Math.max(0, (max_pct - Math.floor((income - base) / step) * 0.01) * 100);
        }
        
        // Cap standard CCS at 90% (maximum possible)
        standardCCS = Math.min(90, standardCCS);
        
        // Floor at 33% if income is within eligible range
        // If calculated value would be less than 33%, set to 33% (minimum for higher CCS eligibility)
        // But if income is too high (above the threshold for 33%), then it can go below 33% or to 0%
        
        // Calculate the income threshold where CCS would be 33%
        // Formula: income = base + ((max_pct - 0.33) * 100) * step
        const income33Threshold = base + ((max_pct - 0.33) * 100) * step;
        
        // If income is above the 33% threshold, CCS can be less than 33% or 0%
        if (income > income33Threshold) {
            standardCCS = Math.max(0, standardCCS);
        } else if (standardCCS < 33 && standardCCS > 0) {
            // If we're within eligible range but calculated less than 33%, set to 33%
            standardCCS = 33;
        }
        
        // Higher CCS calculation using lookup table
        let higherCCS = 0;
        
        // Special case: Low income families get 95% higher CCS
        if (income <= lowIncomeThreshold) {
            higherCCS = 95;
        } else if (standardCCS < 33) {
            higherCCS = 0; // Not eligible if standard is less than 33%
        } else {
            const roundedStandard = Math.round(standardCCS);
            higherCCS = higherCCSLookup[roundedStandard] || 0;
        }
        
        // Update the readonly fields
        $('#standard_ccs_percentage_calc').val(standardCCS.toFixed(2));
        $('#higher_ccs_percentage_calc').val(higherCCS.toFixed(2));
    }

    // Email form toggle functionality
    $('#email-toggle-header').on('click', function() {
        const $content = $('#email-form-content');
        const $icon = $('#email-toggle-icon');
        
        if ($content.is(':visible')) {
            $content.slideUp(300);
            $icon.css('transform', 'rotate(0deg)');
        } else {
            $content.slideDown(300);
            $icon.css('transform', 'rotate(180deg)');
        }
    });

    // Navigation handlers
    $('#next1').off('click').on('click', function(){
        // Clear all previous errors
        $('.error-message').remove();
        $('#suburb, #atsi, #extra-field, #centre-search').css('border-color', '');
        
        let hasError = false;
        
        if ($('#suburb').val() === '') {
            showError('#suburb', 'Please enter your suburb or postal code');
            hasError = true;
        }
        
        if ($('#atsi').val() === '') {
            showError('#atsi', 'Please select an option');
            hasError = true;
        }

        if (!enrolmentSelection) {
            showError('#enrolment-options', 'Please select your enrolment option');
            hasError = true;
        }

        // Validate centre selection for existing family
        if (enrolmentSelection === 'existing' && $('#selected-centre').val().trim() === '') {
            showError('#centre-search', 'Please select your centre from the list');
            hasError = true;
        }

        // Validate text field for other option
        if (enrolmentSelection === 'other' && $('#extra-field').val().trim() === '') {
            showError('#extra-field', 'Please provide the required information');
            hasError = true;
        }
        
        if (hasError) return;


        // Save extra answer for later use (centre or other provider)
        if (enrolmentSelection === 'existing') {
            extraAnswer = $('#selected-centre').val().trim();
        } else {
            extraAnswer = $('#extra-field').val().trim();
        }


        $('.childcare-step').hide();
        $('#step2').show();
        setActiveStep(2);
    });
    $('#back2').click(function(){ $('.childcare-step').hide(); $('#step1').show(); setActiveStep(1); });
    $('#next2').click(function(){ 
        // Clear all previous errors
        $('.error-message').remove();
        $('#know_ccs_percentage, #family_ati, #standard_ccs_percentage, #higher_ccs_percentage, #ccs_withholding_percentage, #activity').css('border-color', '');
        
        let hasError = false;
        const knowsCCS = $('#know_ccs_percentage').val();
        
        // Always check if CCS percentage selection is made
        if (knowsCCS === '') {
            showError('#know_ccs_percentage', 'Please select if you know your CCS percentage');
            hasError = true;
        }
        
        // Validate based on user's selection
        if (knowsCCS === 'yes') {
            // User knows CCS percentage - validate those fields
            if ($('#standard_ccs_percentage').val() === '') {
                showError('#standard_ccs_percentage', 'Please enter Standard Child Care Subsidy Percentage');
                hasError = true;
            }
            
            if ($('#higher_ccs_percentage').val() === '') {
                showError('#higher_ccs_percentage', 'Please enter Higher Child Care Subsidy Percentage');
                hasError = true;
            }
            
            // CCS Withholding is a dropdown, always has a value (default 5%)
            
        } else if (knowsCCS === 'no') {
            // User doesn't know CCS percentage - validate income and activity
            if ($('#family_ati').val() === '') {
                showError('#family_ati', 'Please enter your Family Adjusted Taxable Income');
                hasError = true;
            }
            
            if ($('#activity').val() === '') {
                showError('#activity', 'Please enter your activity hours per fortnight');
                hasError = true;
            }
            
            // Standard and Higher CCS percentages are readonly with default values
            // CCS Withholding is a dropdown, always has a value (default 5%)
        }
        
        if (hasError) return;
        
        $('.childcare-step').hide(); 
        $('#step3').show(); 
        setActiveStep(3); 
    });
    $('#back3').click(function(){ $('.childcare-step').hide(); $('#step2').show(); setActiveStep(2); });
    $('#back4').click(function(){ $('.childcare-step').hide(); $('#step3').show(); setActiveStep(3); });


    // Child count buttons
    $('#child-buttons').on('click','.child-count-btn', function(){
        $('#child-buttons .child-count-btn').removeClass('button-primary'); $(this).addClass('button-primary');
        numChildren=parseInt($(this).data('count'))||0;
        const $details=$('#children-details').empty();
        for(let i=1;i<=numChildren;i++){
            const block=$(
                `
                <div class="child-details" style="margin-top: 40px;" data-child="${i}">
                    <h4 style="margin-bottom: 20px;">Child ${i}</h4>
                    <p>Date of Birth: <input style="line-height: 20px; border-style: solid;" type="date" class="child-dob"></p>
                    <p><strong>Fortnightly Days:</strong></p>
                    <div class="fortnight-days" style="margin-bottom: 20px;">
                        <div style="margin-bottom: 15px;">
                            <strong>Week 1:</strong><br>
                            <div style="display: flex; gap: 8px; margin-top: 8px; flex-wrap: wrap;">
                                <button type="button" class="fortnight-day-btn" data-week="1" data-day="Mon">Monday</button>
                                <button type="button" class="fortnight-day-btn" data-week="1" data-day="Tue">Tuesday</button>
                                <button type="button" class="fortnight-day-btn" data-week="1" data-day="Wed">Wednesday</button>
                                <button type="button" class="fortnight-day-btn" data-week="1" data-day="Thu">Thursday</button>
                                <button type="button" class="fortnight-day-btn" data-week="1" data-day="Fri">Friday</button>
                            </div>
                        </div>
                        <div>
                            <strong>Week 2:</strong><br>
                            <div style="display: flex; gap: 8px; margin-top: 8px; flex-wrap: wrap;">
                                <button type="button" class="fortnight-day-btn" data-week="2" data-day="Mon">Monday</button>
                                <button type="button" class="fortnight-day-btn" data-week="2" data-day="Tue">Tuesday</button>
                                <button type="button" class="fortnight-day-btn" data-week="2" data-day="Wed">Wednesday</button>
                                <button type="button" class="fortnight-day-btn" data-week="2" data-day="Thu">Thursday</button>
                                <button type="button" class="fortnight-day-btn" data-week="2" data-day="Fri">Friday</button>
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label><strong>Hours per day:</strong></label>
                        <div class="slider-container" style="display: flex; align-items: center; gap: 10px; margin-top: 8px;">
                            <span style="font-size: 14px; color: #666;">4hrs</span>
                            <div style="flex: 1; position: relative;">
                                <input type="range" class="child-hours-slider" min="4" max="12" value="8" step="0.25" style="width: 100%;">
                            </div>
                            <span style="font-size: 14px; color: #666;">12hrs</span>
                            <span class="calc-hours" style="font-weight: 600; color: <?php echo esc_attr(get_option('ccs_accent_color', '#0073aa')); ?>; min-width: 60px;">8 hours</span>
                        </div>
                    </div>
                    
                    <p>Fees per day ($): <input style="line-height: 20px; border-style: solid;" type="number" class="child-fee" step="0.01"></p>
                    
                    <div class="child-calculation-output" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0px 15px 0px; border-left: 4px solid <?php echo esc_attr(get_option('ccs_accent_color', '#0073aa')); ?>;">
                        <h5 style="margin: 0 0 10px 0; color: <?php echo esc_attr(get_option('ccs_accent_color', '#0073aa')); ?>; font-size: 14px;">📊 Live Calculation Preview</h5>
                        <div class="calculation-details" style="font-size: 13px; color: #666;">
                            <div>Hours per day: <span class="calc-hours">8</span> hours</div>
                            <div>Days per fortnight: <span class="calc-days">0</span> days</div>
                            <div>Fee per day: $<span class="calc-fee">0.00</span></div>
                            <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #ddd;">
                                <strong style="color: <?php echo esc_attr(get_option('ccs_accent_color', '#0073aa')); ?>;">Total per fortnight: $<span class="calc-total">0.00</span></strong>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            $details.append(block);
        }
        
        // Add event handlers for the new button-style fortnight days
        $('.fortnight-day-btn').off('click').on('click', function() {
            $(this).toggleClass('active');
            updateChildCalculation($(this).closest('.child-details'));
        });
        
        // Add event handlers for slider with completed line effect
        $('.child-hours-slider').off('input').on('input', function() {
            const value = $(this).val();
            $(this).siblings('.child-hours-output').text(value + ' hours');
            
            // Update slider completed line
            const min = $(this).attr('min');
            const max = $(this).attr('max');
            const percentage = ((value - min) / (max - min)) * 100;
            $(this).parent().css('--slider-progress', percentage + '%');
            
            // Update live calculation
            updateChildCalculation($(this).closest('.child-details'));
        });
        
        // Add event handlers for fee input
        $('.child-fee').off('input').on('input', function() {
            updateChildCalculation($(this).closest('.child-details'));
        });
    });


    // Next from step3
    $('#next3').click(function(){
        // Clear all previous errors
        $('.error-message').remove();
        $('.child-dob, .child-fee').css('border-color', '');
        
        let hasError = false;
        
        if(numChildren===0){ 
            showError('#child-buttons', 'Please select the number of children');
            return; 
        }
        
        // Validate each child's details
        $('#children-details .child-details').each(function(index){ 
            const $childBlock = $(this);
            const childNum = index + 1;
            
            if($childBlock.find('.child-dob').val()==='') {
                showError($childBlock.find('.child-dob'), 'Please enter date of birth for Child ' + childNum);
                hasError = true;
            }
            
            if($childBlock.find('.child-fee').val()==='') {
                showError($childBlock.find('.child-fee'), 'Please enter fees per day for Child ' + childNum);
                hasError = true;
            }
        });
        
        if(hasError) return;
        
        $('.childcare-step').hide(); 
        $('#step4').show();
        $('#summary-content').hide();
        $('#childcare-loader').show(); 
        setActiveStep(4);
        
        // Show loader for 2-3 seconds then calculate and show summary
        setTimeout(function() {
            calculateCCS();
            // Hide loader and show summary after calculation
            setTimeout(function() {
                $('#childcare-loader').fadeOut(300, function() {
                    $('#summary-content').fadeIn(400);
                });
            }, 500);
        }, 2500); // 2.5 seconds delay
    });


    $(document).on('input change', '.child-hours-slider', function(){
        $(this).siblings('.child-hours-output').text($(this).val() + ' hours');
        updateChildCalculation($(this).closest('.child-details'));
    });

    // Global event handler for fee input
    $(document).on('input change', '.child-fee', function(){
        updateChildCalculation($(this).closest('.child-details'));
    });

    // Global event handler for fortnight day buttons
    $(document).on('click', '.fortnight-day-btn', function(){
        updateChildCalculation($(this).closest('.child-details'));
    });

    // Live calculation update for individual child
    function updateChildCalculation(childElement) {
        const hoursPerDay = parseFloat(childElement.find('.child-hours-slider').val()) || 0;
        const feePerDay = parseFloat(childElement.find('.child-fee').val()) || 0;
        const daysWeek1 = childElement.find('.fortnight-day-btn[data-week="1"].active').length;
        const daysWeek2 = childElement.find('.fortnight-day-btn[data-week="2"].active').length;
        const totalDays = daysWeek1 + daysWeek2;
        const totalPerFortnight = totalDays * feePerDay;
        
        // Update the live calculation display
        childElement.find('.calc-hours').text(hoursPerDay);
        childElement.find('.calc-days').text(totalDays);
        childElement.find('.calc-fee').text(feePerDay.toFixed(2));
        childElement.find('.calc-total').text(totalPerFortnight.toFixed(2));
        
        // Add visual feedback for completeness
        const calculationOutput = childElement.find('.child-calculation-output');
        if (totalDays > 0 && feePerDay > 0) {
            calculationOutput.css('border-left-color', '#00a32a');
            calculationOutput.find('h5').css('color', '#00a32a');
        } else {
            calculationOutput.css('border-left-color', '#0073aa');
            calculationOutput.find('h5').css('color', '#0073aa');
        }
    }

    function getAge(dob){ if(!dob) return 0; const d=new Date(dob); const diff=Date.now()-d.getTime(); return new Date(diff).getUTCFullYear()-1970; }


    // CCS Calculations
    function calculateCCS(){
        const knowsCCS = $('#know_ccs_percentage').val();
        const income = parseFloat($('#family_ati').val()) || 0;
        const activityHours = parseFloat($('#activity').val()) || 0;
        
        // Get CCS percentages based on user selection
        let standardCCSPct = 0;
        let higherCCSPct = 0;
        let withholdingPct = 0;
        
        // Always get withholding percentage from the single field
        withholdingPct = parseFloat($('#ccs_withholding_percentage').val()) / 100 || 0.05;
        
        if (knowsCCS === 'yes') {
            // User knows their CCS percentage
            standardCCSPct = parseFloat($('#standard_ccs_percentage').val()) / 100 || 0;
            higherCCSPct = parseFloat($('#higher_ccs_percentage').val()) / 100 || 0;
        } else {
            // Calculate CCS percentage from income and activity hours
            const base = parseFloat(policy.income_base_threshold) || 0;
            const zero = parseFloat(policy.income_zero_threshold) || 0;
            const step = parseFloat(policy.income_step) || 1;
            const max_pct = parseFloat(policy.max_pct) || 0;
            
            if (income <= base) {
                standardCCSPct = max_pct;
            } else if (income >= zero) {
                standardCCSPct = 0;
            } else {
                standardCCSPct = Math.max(0, max_pct - Math.floor((income - base) / step) * 0.01);
            }
            
            // Use calculated values from readonly fields
            higherCCSPct = parseFloat($('#higher_ccs_percentage_calc').val()) / 100 || 0.95;
        }
        
        // Step 4.1 - Work out CCS hours per fortnight based on activity hours (using admin settings)
        let ccsHoursPerFortnight = 0;
        if (activityHours >= 48) {
            ccsHoursPerFortnight = policy.ccs_hours_48_plus || 100;
        } else if (activityHours >= 17) {
            ccsHoursPerFortnight = policy.ccs_hours_17_48 || 72;
        } else if (activityHours >= 8) {
            ccsHoursPerFortnight = policy.ccs_hours_8_16 || 36;
        } else {
            ccsHoursPerFortnight = 0;
        }
        
        const ccsHoursPerWeek = ccsHoursPerFortnight / 2;
        
        childrenData = [];
        let childIndex = 0;
        
        $('#children-details .child-details').each(function(){
            const dob = $(this).find('.child-dob').val();
            const hoursPerDay = parseFloat($(this).find('.child-hours-slider').val()) || 0;
            const feePerDay = parseFloat($(this).find('.child-fee').val()) || 0;
            const daysWeek1 = $(this).find('.fortnight-day-btn[data-week="1"].active').length;
            const daysWeek2 = $(this).find('.fortnight-day-btn[data-week="2"].active').length;
            const week1Fee = daysWeek1 * feePerDay;
            const week2Fee = daysWeek2 * feePerDay;
            const fortnightFee = week1Fee + week2Fee;
            
            // Determine if child is eligible for higher CCS (2nd child or younger, aged 5 or under)
            const age = getAge(dob);
            const higherCCSThreshold = parseFloat(policy.higher_ccs_threshold) || 367563;
            const isEligibleForHigherCCS = (childIndex >= 1 && age <= 5 && income < higherCCSThreshold);
            const ccs_pct = isEligibleForHigherCCS ? higherCCSPct : standardCCSPct;
            
            // Step 4.2 - Work out hourly fee
            const hourlyFee = hoursPerDay > 0 ? feePerDay / hoursPerDay : 0;
            
            // Step 4.3 - Get hourly CCS rate cap based on age and service type
            // Using Centre Based Day Care as default service type
            const cap = (age < 6) ? (hourly_caps.centre_based_day_care || 14.63) : (hourly_caps.oshc_school_age || 12.81);
            
            // Step 4.4 - Work out hourly CCS amount (lower of hourly fee and cap × CCS rate)
            const effectiveHourlyRate = Math.min(hourlyFee, cap);
            const hourlyCCSAmount = effectiveHourlyRate * ccs_pct;
            
            // Step 4.5 - Work out weekly CCS entitlement
            const weeklyCCSEntitlement = hourlyCCSAmount * ccsHoursPerWeek;
            
            // Calculate actual subsidy based on actual hours used, capped at weekly entitlement and actual fee
            const week1Hours = daysWeek1 * hoursPerDay;
            const week2Hours = daysWeek2 * hoursPerDay;
            const week1SubBeforeWithholding = Math.min(week1Hours * hourlyCCSAmount, weeklyCCSEntitlement, week1Fee);
            const week2SubBeforeWithholding = Math.min(week2Hours * hourlyCCSAmount, weeklyCCSEntitlement, week2Fee);
            
            // Step 4.6 - Work out withholding amount
            const week1Withholding = week1SubBeforeWithholding * withholdingPct;
            const week2Withholding = week2SubBeforeWithholding * withholdingPct;
            
            // Step 4.7 - Work out CCS paid to service provider (after withholding)
            const week1Sub = week1SubBeforeWithholding - week1Withholding;
            const week2Sub = week2SubBeforeWithholding - week2Withholding;
            const fortnightSub = week1Sub + week2Sub;
            
            // Step 4.8 - Work out out-of-pocket cost (gap fee)
            const week1OutOfPocket = Math.max(0, week1Fee - week1Sub);
            const week2OutOfPocket = Math.max(0, week2Fee - week2Sub);
            const outPocket = week1OutOfPocket + week2OutOfPocket;
            
            // Step 4.9 - Cost after EOY reconciliation (when withholding is returned)
            const week1AfterEOY = week1OutOfPocket - week1Withholding;
            const week2AfterEOY = week2OutOfPocket - week2Withholding;
            
            childrenData.push({
                dob, hoursPerDay, feePerDay, daysWeek1, daysWeek2, 
                week1Fee, week2Fee, fortnightFee,
                week1Sub, week2Sub, fortnightSub, 
                outPocket,
                week1Withholding, week2Withholding,
                week1AfterEOY, week2AfterEOY,
                suburb: $('#suburb').val(), 
                atsi: $('#atsi').val(), 
                enrolment: enrolmentSelection, 
                extraAnswer: extraAnswer,
                ccs_pct: ccs_pct,
                isHigherCCS: isEligibleForHigherCCS,
                hourlyFee: hourlyFee,
                hourlyCap: cap,
                hourlyCCSAmount: hourlyCCSAmount
            });
            
            childIndex++;
        });


        // build child select dropdown
        const $select=$('#child-select').empty();
        if(numChildren>1){
            $select.append('<option value="all">All Children</option>');
            for(let i=0;i<numChildren;i++) $select.append(`<option value="${i}">Child ${i+1}</option>` );
            $('#child-select-wrapper').show();
        }else{
            $('#child-select-wrapper').hide();
        }


        renderSummary('fortnight','all');
        // Loader is now hidden by the setTimeout in next3 click handler
    }


    // CCS Summary Render
    function renderSummary(period='fortnight', child='all'){
        const multipliers = { week:0.5, fortnight:1, month:26/12, year:26 };
        const mult = multipliers[period] || 1;


        const periodLabelMap = {
            week: 'per week',
            fortnight: 'per fortnight',
            month: 'per month',
            year: 'per year'
        };
        const label = periodLabelMap[period] || '';


        let totalFee=0, totalSub=0, totalOut=0;
        let weeklyHTML = '';
        let detailsHTML = '';


        if(child === 'all'){
            let week1Total=0, week2Total=0, week1SubTotal=0, week2SubTotal=0;


            childrenData.forEach((c,i) => {
                totalFee += c.fortnightFee * mult;
                totalSub += c.fortnightSub * mult;
                totalOut += c.outPocket * mult;


                week1Total += c.week1Fee;
                week2Total += c.week2Fee;
                week1SubTotal += c.week1Sub;
                week2SubTotal += c.week2Sub;


                // Per-child info card
                detailsHTML += `
                    <div class="child-detail-card" style="flex: 1; padding:20px; border:1px solid #f5f5f5; border-radius:5px; background:#fff;">
                        <h5 style="margin-bottom: 15px; color: ${summaryColors.heading};">Child ${i+1}</h5>
                        <p style="font-size: 16px; line-height: 20px;"><strong>Location:</strong> ${c.suburb || '-'}</p>
                        <p style="font-size: 16px; line-height: 20px;"><strong>CCS Percentage:</strong> ${(c.ccs_pct*100).toFixed(0)}%</p>
                        <p style="font-size: 16px; line-height: 20px;"><strong>Days per Fortnight:</strong> Week 1: ${c.daysWeek1}, Week 2: ${c.daysWeek2}</p>
                        <p style="font-size: 16px; line-height: 20px;"><strong>Session (hours/day):</strong> ${c.hoursPerDay}</p>
                        <p style="font-size: 16px; line-height: 20px;"><strong>Daily Fee:</strong> $${formatCurrency(c.feePerDay)}</p>
                    </div>
                `;
            });


            $('#summary-title').text('All Children');


            weeklyHTML = `
                <table style="width: 100%;">
                    <tr style="display: flex; flex-wrap: wrap; gap: 5px">
                        <td style="padding: 0px; border: 0px; flex: 1;">
                            <div>
                                <h4 style="margin:0 0 20px 0; font-size:16px; font-weight:600; color:${summaryColors.weekHeading}; text-align: center;">Week 1</h4>
                                <div style="display: flex; flex-flow: column; gap: 5px; align-items: center; padding: 20px; background: ${summaryColors.feeBg};">
                                    <span style="font-weight:400; color:#252525; font-size:14px; line-height:20px;">Total fee</span>
                                    <span style="font-weight:700; color:${summaryColors.totalFee}; font-size:20px; line-height:20px;">$${formatCurrency(week1Total)}</span>
                                </div>
                                <div style="display: flex; flex-flow: column; gap: 5px; align-items: center; padding: 20px; background: ${summaryColors.subsidyBg};">
                                    <span style="font-weight:400; color:#252525; font-size:14px; line-height:20px;">Est. subsidy</span>
                                    <span style="font-weight:700; color:${summaryColors.subsidy}; font-size:20px; line-height:20px;">$${formatCurrency(week1SubTotal)}</span>
                                </div>
                                <div style="display: flex; flex-flow: column; gap: 5px; align-items: center; padding: 20px; background: ${summaryColors.outOfPocketBg};">
                                    <span style="font-weight:400; color:#252525; font-size:14px; line-height:20px;">Out-of-pocket</span>
                                    <span style="font-weight:700; color:${summaryColors.outOfPocket}; font-size:20px; line-height:20px;">$${formatCurrency(Math.max(0,week1Total-week1SubTotal))}</span>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 0px; border: 0px; flex: 1;">
                            <div>
                                <h4 style="margin:0 0 20px 0; font-size:16px; font-weight:600; color:${summaryColors.weekHeading}; text-align: center;">Week 2</h4>
                                <div style="display: flex; flex-flow: column; gap: 5px; align-items: center; padding: 20px; background: ${summaryColors.feeBg};">
                                    <span style="font-weight:400; color:#252525; font-size:14px; line-height:20px;">Total fee</span>
                                    <span style="font-weight:700; color:${summaryColors.totalFee}; font-size:20px; line-height:20px;">$${formatCurrency(week2Total)}</span>
                                </div>
                                <div style="display: flex; flex-flow: column; gap: 5px; align-items: center; padding: 20px; background: ${summaryColors.subsidyBg};">
                                    <span style="font-weight:400; color:#252525; font-size:14px; line-height:20px;">Est. subsidy</span>
                                    <span style="font-weight:700; color:${summaryColors.subsidy}; font-size:20px; line-height:20px;">$${formatCurrency(week2SubTotal)}</span>
                                </div>
                                <div style="display: flex; flex-flow: column; gap: 5px; align-items: center; padding: 20px; background: ${summaryColors.outOfPocketBg};">
                                    <span style="font-weight:400; color:#252525; font-size:14px; line-height:20px;">Out-of-pocket</span>
                                    <span style="font-weight:700; color:${summaryColors.outOfPocket}; font-size:20px; line-height:20px;">$${formatCurrency(Math.max(0,week2Total-week2SubTotal))}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            `;
        } else {
            const c = childrenData[parseInt(child)];
            totalFee = c.fortnightFee * mult;
            totalSub = c.fortnightSub * mult;
            totalOut = c.outPocket * mult;


            $('#summary-title').text(`Child ${parseInt(child)+1}` );


            // Single child info card
            detailsHTML = `
                <div class="child-detail-card" style="flex: 1; padding:20px; border:1px solid #f5f5f5; border-radius:5px; background:#fff;">
                    <h5 style="margin-bottom: 15px; color: ${summaryColors.heading};">Child ${parseInt(child)+1}</h5>
                    <p style="font-size: 16px; line-height: 20px;"><strong>Location:</strong> ${c.suburb || '-'}</p>
                    <p style="font-size: 16px; line-height: 20px;"><strong>CCS Percentage:</strong> ${(c.ccs_pct*100).toFixed(0)}%</p>
                    <p style="font-size: 16px; line-height: 20px;"><strong>Days per Fortnight:</strong> Week 1: ${c.daysWeek1}, Week 2: ${c.daysWeek2}</p>
                    <p style="font-size: 16px; line-height: 20px;"><strong>Session (hours/day):</strong> ${c.hoursPerDay}</p>
                    <p style="font-size: 16px; line-height: 20px;"><strong>Daily Fee:</strong> $${formatCurrency(c.feePerDay)}</p>
                </div>
            `;


            weeklyHTML = `
                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                    <tr style="display: flex; flex-wrap: wrap; gap: 5px">
                        <td style="padding: 0px; border: 0px; flex: 1;">
                            <div>
                                <h4 style="margin:0 0 20px 0; font-size:16px; font-weight:600; color:${summaryColors.weekHeading}; text-align: center;">Week 1</h4>
                                <div style="display: flex; flex-flow: column; gap: 5px; align-items: center; padding: 20px; background: ${summaryColors.feeBg};">
                                    <span style="font-weight:400; color:#252525; font-size:14px; line-height:20px;">Total fee</span>
                                    <span style="font-weight:700; color:${summaryColors.totalFee}; font-size:20px; line-height:20px;">$${formatCurrency(c.week1Fee)}</span>
                                </div>
                                <div style="display: flex; flex-flow: column; gap: 5px; align-items: center; padding: 20px; background: ${summaryColors.subsidyBg};">
                                    <span style="font-weight:400; color:#252525; font-size:14px; line-height:20px;">Est. subsidy</span>
                                    <span style="font-weight:700; color:${summaryColors.subsidy}; font-size:20px; line-height:20px;">$${formatCurrency(c.week1Sub)}</span>
                                </div>
                                <div style="display: flex; flex-flow: column; gap: 5px; align-items: center; padding: 20px; background: ${summaryColors.outOfPocketBg};">
                                    <span style="font-weight:400; color:#252525; font-size:14px; line-height:20px;">Out-of-pocket</span>
                                    <span style="font-weight:700; color:${summaryColors.outOfPocket}; font-size:20px; line-height:20px;">$${formatCurrency(Math.max(0,c.week1Fee-c.week1Sub))}</span>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 0px; border: 0px; flex: 1;">
                            <div>
                                <h4 style="margin:0 0 20px 0; font-size:16px; font-weight:600; color:${summaryColors.weekHeading}; text-align: center;">Week 2</h4>
                                <div style="display: flex; flex-flow: column; gap: 5px; align-items: center; padding: 20px; background: ${summaryColors.feeBg};">
                                    <span style="font-weight:400; color:#252525; font-size:14px; line-height:20px;">Total fee</span>
                                    <span style="font-weight:700; color:${summaryColors.totalFee}; font-size:20px; line-height:20px;">$${formatCurrency(c.week2Fee)}</span>
                                </div>
                                <div style="display: flex; flex-flow: column; gap: 5px; align-items: center; padding: 20px; background: ${summaryColors.subsidyBg};">
                                    <span style="font-weight:400; color:#252525; font-size:14px; line-height:20px;">Est. subsidy</span>
                                    <span style="font-weight:700; color:${summaryColors.subsidy}; font-size:20px; line-height:20px;">$${formatCurrency(c.week2Sub)}</span>
                                </div>
                                <div style="display: flex; flex-flow: column; gap: 5px; align-items: center; padding: 20px; background: ${summaryColors.outOfPocketBg};">
                                    <span style="font-weight:400; color:#252525; font-size:14px; line-height:20px;">Out-of-pocket</span>
                                    <span style="font-weight:700; color:${summaryColors.outOfPocket}; font-size:20px; line-height:20px;">$${formatCurrency(Math.max(0,c.week2Fee-c.week2Sub))}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            `;
        }

        // Get household income information
        const knowsCCS = $('#know_ccs_percentage').val();
        const familyIncome = $('#family_ati').val() || '-';
        const activityHours = $('#activity option:selected').text();
        const ccsHours = $('#ccs_hours_display').val();
        const withholdingPct = $('#ccs_withholding_percentage').val() + '%';
        
        let standardCCS, higherCCS;
        if (knowsCCS === 'yes') {
            standardCCS = $('#standard_ccs_percentage').val() + '%';
            higherCCS = $('#higher_ccs_percentage').val() + '%';
        } else {
            standardCCS = $('#standard_ccs_percentage_calc').val() + '%';
            higherCCS = $('#higher_ccs_percentage_calc').val() + '%';
        }

        // Household Income Information Section
        $('#household-income-info').html(`
            <div class="household-income-info-container" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <table class="household-income-info-table" style="width: 100%; border-collapse: collapse;">
                    <tbody>
                        ${knowsCCS === 'no' ? `
                        <tr>
                            <td class="income-info-item" style="padding: 15px 0px !important; width: 50% !important; border: 0px !important;">
                                <p class="income-info-item-p" style="font-size: 12px; line-height: 16px; margin: 0px 0px 10px 0px !important;">Family Adjusted Taxable Income</p>
                                <p class="income-info-item-value" style="font-size: 16px; line-height: 20px; margin: 0px !important; font-weight: 600;">AU$ ${familyIncome !== '-' ? formatCurrency(parseFloat(familyIncome)) : '-'}</p>
                            </td>
                            <td class="income-info-item" style="padding: 15px 0px !important; width: 50% !important; border: 0px !important;">
                                <p class="income-info-item-p" style="font-size: 12px; line-height: 16px; margin: 0px 0px 10px 0px !important;">Hours of Recognised Activities</p>
                                <p class="income-info-item-value" style="font-size: 16px; line-height: 20px; margin: 0px !important; font-weight: 600;">${activityHours}</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="income-info-item" style="padding: 15px 0px !important; width: 50% !important; border: 0px !important;">
                                <p class="income-info-item-p" style="font-size: 12px; line-height: 16px; margin: 0px 0px 10px 0px !important;">Hours of Child Care Subsidy</p>
                                <p class="income-info-item-value" style="font-size: 16px; line-height: 20px; margin: 0px !important; font-weight: 600;">${ccsHours}</p>
                            </td>
                            <td class="income-info-item" style="padding: 15px 0px !important; width: 50% !important; border: 0px !important;">
                                <p class="income-info-item-p" style="font-size: 12px; line-height: 16px; margin: 0px 0px 10px 0px !important;">CCS Withholding Percentage</p>
                                <p class="income-info-item-value" style="font-size: 16px; line-height: 20px; margin: 0px !important; font-weight: 600;">${withholdingPct}</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="income-info-item" style="padding: 15px 0px !important; width: 50% !important; border: 0px !important;">
                                <p class="income-info-item-p" style="font-size: 12px; line-height: 16px; margin: 0px 0px 10px 0px !important;">Standard CCS Percentage</p>
                                <p class="income-info-item-value" style="font-size: 16px; line-height: 20px; margin: 0px !important; font-weight: 600;">${standardCCS}</p>
                            </td>
                            <td class="income-info-item" style="padding: 15px 0px !important; width: 50% !important; border: 0px !important;">
                                <p class="income-info-item-p" style="font-size: 12px; line-height: 16px; margin: 0px 0px 10px 0px !important;">Higher CCS Percentage</p>
                                <p class="income-info-item-value" style="font-size: 16px; line-height: 20px; margin: 0px !important; font-weight: 600;">${higherCCS}</p>
                            </td>
                        </tr>` : `
                        <tr>
                            <td class="income-info-item" style="padding: 15px 0px !important; width: 50% !important; border: 0px !important;">
                                <p class="income-info-item-p" style="font-size: 12px; line-height: 16px; margin: 0px 0px 10px 0px !important;">Hours of Recognised Activities</p>
                                <p class="income-info-item-value" style="font-size: 16px; line-height: 20px; margin: 0px !important; font-weight: 600;">${activityHours}</p>
                            </td>
                            <td class="income-info-item" style="padding: 15px 0px !important; width: 50% !important; border: 0px !important;">
                                <p class="income-info-item-p" style="font-size: 12px; line-height: 16px; margin: 0px 0px 10px 0px !important;">Hours of Child Care Subsidy</p>
                                <p class="income-info-item-value" style="font-size: 16px; line-height: 20px; margin: 0px !important; font-weight: 600;">${ccsHours}</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="income-info-item" style="padding: 15px 0px !important; width: 50% !important; border: 0px !important;">
                                <p class="income-info-item-p" style="font-size: 12px; line-height: 16px; margin: 0px 0px 10px 0px !important;">CCS Withholding Percentage</p>
                                <p class="income-info-item-value" style="font-size: 16px; line-height: 20px; margin: 0px !important; font-weight: 600;">${withholdingPct}</p>
                            </td>
                            <td class="income-info-item" style="padding: 15px 0px !important; width: 50% !important; border: 0px !important;">
                                <p class="income-info-item-p" style="font-size: 12px; line-height: 16px; margin: 0px 0px 10px 0px !important;">Standard CCS Percentage</p>
                                <p class="income-info-item-value" style="font-size: 16px; line-height: 20px; margin: 0px !important; font-weight: 600;">${standardCCS}</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="income-info-item" style="padding: 15px 0px !important; width: 50% !important; border: 0px !important;">
                                <p class="income-info-item-p" style="font-size: 12px; line-height: 16px; margin: 0px 0px 10px 0px !important;">Higher CCS Percentage</p>
                                <p class="income-info-item-value" style="font-size: 16px; line-height: 20px; margin: 0px !important; font-weight: 600;">${higherCCS}</p>
                            </td>
                            <td class="income-info-item empty" style="padding: 15px 0px !important; width: 50% !important; border: 0px !important;"></td>
                        </tr>`}
                    </tbody>
                </table>
            </div>
        `);

        // Overall summary with email-compatible table layout (no flexbox)
        $('#summary-overall').html(`
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin: 0;">
                <tr>
                    <td style="padding: 0px; border-bottom: 0;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                            <tr class="summary-row-tr" style="display: flex; flex-wrap: wrap; gap: 5px;">
                                <td class="summary-row-td" style="display: flex; flex: 1; flex-flow: column; gap: 5px; padding: 16px 20px; background: #E9F1F6; font-weight: 600; font-size: 16px; justify-content: center;">
                                    Total fees
                                </td>
                                <td class="summary-row-td" style="display: flex; flex: 1; flex-flow: column; gap: 5px; padding: 16px 20px; background: #E9F1F6; font-weight: 600; text-align: right; color: ${summaryColors.totalFee}; font-size: 20px; line-height: 24px;">
                                    $${formatCurrency(totalFee)} <span style="font-size: 16px; font-weight: 400; color: #666;">${label}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0px;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                            <tr class="summary-row-tr" style="display: flex; flex-wrap: wrap; gap: 5px;">
                                <td class="summary-row-td" style="display: flex; flex: 1; flex-flow: column; gap: 5px; padding: 16px 20px; background: #f0ecf9; font-weight: 600; font-size: 16px; justify-content: center;">
                                    Estimated Subsidy
                                </td>
                                <td class="summary-row-td" style="display: flex; flex: 1; flex-flow: column; gap: 5px; padding: 16px 20px; background: #f0ecf9; font-weight: 600; text-align: right; color: ${summaryColors.subsidy}; font-size: 20px; line-height: 24px;">
                                    $${formatCurrency(totalSub)} <span style="font-size: 16px; font-weight: 400; color: #666;">${label}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0px; border-top: 0;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                            <tr class="summary-row-tr" style="display: flex; flex-wrap: wrap; gap: 5px;">
                                <td class="summary-row-td" style="display: flex; flex: 1; flex-flow: column; gap: 5px; padding: 16px 20px; background: #ebf8fa; font-weight: 600; font-size: 16px; justify-content: center;">
                                    Out of pocket costs
                                </td>
                                <td class="summary-row-td" style="display: flex; flex: 1; flex-flow: column; gap: 5px; padding: 16px 20px; background: #ebf8fa; font-weight: 600; text-align: right; color: ${summaryColors.outOfPocket}; font-size: 20px; line-height: 24px;">
                                    $${formatCurrency(totalOut)} <span style="font-size: 16px; font-weight: 400; color: #666;">${label}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        `);


        // Weekly breakdown
        $('#summary-weekly').html(weeklyHTML);


        // Child detail cards
        $('#child-details-summary').html(detailsHTML);
    }


    // handle period buttons
    $('.summary-btn').click(function(){
        $('.summary-btn').removeClass('button-primary'); $(this).addClass('button-primary');
        const period=$(this).data('period');
        const child=$('#child-select').val()||'all';
        renderSummary(period,child);
    });


    // HubSpot Form Integration
    function loadHubSpotForm() {
        if (typeof hbspt !== 'undefined') {
            const hubspotConfig = {
                region: "<?php echo esc_js(get_option('ccs_hubspot_region', 'na1')); ?>",
                portalId: "<?php echo esc_js(get_option('ccs_hubspot_portal_id', '')); ?>",
                formId: "<?php echo esc_js(get_option('ccs_hubspot_form_id', '')); ?>",
                target: '#hubspot-form-container',
                onFormReady: function($form) {
                    console.log('HubSpot form loaded successfully');
                    console.log('HubSpot Config:', hubspotConfig);
                    
                    // Prepare summary text for HubSpot hidden field
                    const summaryText = 
                        'OVERALL SUMMARY:\n' +
                        $('#summary-overall').text().trim() + '\n\n' +
                        'FORTNIGHTLY BREAKDOWN:\n' +
                        $('#summary-weekly').text().trim() + '\n\n' +
                        'CHILD DETAILS:\n' +
                        $('#child-details-summary').text().trim();
                    
                    // Populate hidden field with summary
                    const hiddenFieldName = '<?php echo esc_js(get_option('ccs_hubspot_hidden_field', 'calculate_property')); ?>';
                    const hiddenField = $form.find('input[name="' + hiddenFieldName + '"]');
                    if (hiddenField.length > 0) {
                        hiddenField.val(summaryText);
                        console.log('Summary populated in HubSpot hidden field: ' + hiddenFieldName);
                    } else {
                        console.warn('Hidden field "' + hiddenFieldName + '" not found in form. Please check your HubSpot form settings.');
                    }
                },
                onFormSubmit: function($form) {
                    console.log('HubSpot form submitted');
                    
                    // Get form data
                    const formData = $form.serializeArray();
                    const userData = {};
                    formData.forEach(field => {
                        userData[field.name] = field.value;
                    });
                    
                    // Prepare summary HTML for email
                    const summaryHTML = `
                        <div style="font-family: Arial, sans-serif; max-width: 100%; margin: 0;">
                            <h2 style="color: #333; font-size: 20px; margin: 0 0 20px 0; font-weight: 600;">Your details</h2>
                            <div style="background: #ffffff; padding: 0; margin: 0 0 25px 0;">
                                ${$('#summary-overall').html()}
                            </div>
                            <div style="margin: 25px 0;">
                                <h3 style="color: #333; font-size: 18px; margin: 0 0 15px 0; font-weight: 600;">Household Income Information</h3>
                                ${$('#household-income-info').html()}
                            </div>
                            <div style="margin: 25px 0;">
                                <h3 style="color: #333; font-size: 18px; margin: 0 0 15px 0; font-weight: 600;">Fortnightly Breakdown</h3>
                                ${$('#summary-weekly').html()}
                            </div>
                            <div style="margin: 25px 0;">
                                <h3 style="color: #333; font-size: 18px; margin: 0 0 15px 0; font-weight: 600;">Child Details</h3>
                                ${$('#child-details-summary').html()}
                            </div>
                        </div>
                    `;
                    
                    // Get enrollment option text using shared function
                    const enrolmentText = getEnrollmentOptionText();
                    
                    // Send to WordPress to save submission and send email
                    $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                        action: 'send_summary_email',
                        user_name: userData.firstname + ' ' + (userData.lastname || ''),
                        user_email: userData.email,
                        user_phone: userData.phone || '',
                        summary_html: summaryHTML,
                        location: $('#suburb').val() || '',
                        atsi_status: $('#atsi').val() || '',
                        enrolment_option: enrolmentText
                    }, function(response) {
                        console.log('WordPress response:', response);
                        if(response.success){
                            $('#send-summary-response').html('<div style="color:green; padding:10px; background:#d4edda; border-radius:4px; margin-top:15px;">✓ Summary sent successfully to your email!</div>');
                        } else {
                            $('#send-summary-response').html('<div style="color:red; padding:10px; background:#f8d7da; border-radius:4px; margin-top:15px;">✗ Error: ' + (response.data || 'Please try again') + '</div>');
                        }
                    }).fail(function(xhr, status, error) {
                        console.error('AJAX error:', error);
                        $('#send-summary-response').html('<div style="color:red; padding:10px; background:#f8d7da; border-radius:4px; margin-top:15px;">✗ Connection error. Please try again.</div>');
                    });
                },
                onFormSubmitted: function() {
                    console.log('Form successfully submitted to HubSpot');
                }
            };
            
            // Log configuration before creating form
            console.log('Attempting to load HubSpot form with config:', hubspotConfig);
            
            // Validate configuration
            if (!hubspotConfig.portalId || !hubspotConfig.formId) {
                console.error('HubSpot configuration error: Portal ID or Form ID is missing');
                $('#hubspot-form-container').html('<div style="color:red; padding:15px; background:#f8d7da; border-radius:4px;"><strong>Configuration Error:</strong> Please configure HubSpot Portal ID and Form ID in admin settings.</div>');
                return;
            }
            
            // Create form with error handling
            try {
                hbspt.forms.create(hubspotConfig);
            } catch (error) {
                console.error('HubSpot form creation error:', error);
                $('#hubspot-form-container').html('<div style="color:red; padding:15px; background:#f8d7da; border-radius:4px;"><strong>Error:</strong> Could not load HubSpot form. Please check browser console for details.</div>');
            }
        } else {
            // Fallback if HubSpot script not loaded
            console.error('HubSpot script (hbspt) not loaded');
            $('#hubspot-form-container').html('<div style="color:red; padding:15px; background:#f8d7da; border-radius:4px;"><strong>Script Error:</strong> HubSpot script could not be loaded. Please check your internet connection.</div>');
        }
    }
    
    // Initialize form based on type
    function initForm() {
        const formType = '<?php echo esc_js(get_option('ccs_form_type', 'hubspot')); ?>';
        
        if (formType === 'custom') {
            // Show custom form
            $('#custom-form-container').show();
            $('#hubspot-form-container').hide();
            initCustomForm();
        } else {
            // Show HubSpot form
            $('#hubspot-form-container').show();
            $('#custom-form-container').hide();
            if ($('#hubspot-form-container').is(':empty')) {
                loadHubSpotForm();
            }
        }
    }
    
    // Custom form handler
    function initCustomForm() {
        $('#custom-summary-form').off('submit').on('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const firstname = $('#custom_firstname').val();
            const lastname = $('#custom_lastname').val();
            const email = $('#custom_email').val();
            const phone = $('#custom_phone').val();
            const fullName = firstname + (lastname ? ' ' + lastname : '');
            
            // Prepare summary HTML
            const summaryHTML = `
                <div style="font-family: Arial, sans-serif; max-width: 100%; margin: 0;">
                    <h2 style="color: #333; font-size: 20px; margin: 0 0 20px 0; font-weight: 600;">Your details</h2>
                    <div style="background: #ffffff; padding: 0; margin: 0 0 25px 0;">
                        ${$('#summary-overall').html()}
                    </div>
                    <div style="margin: 25px 0;">
                        <h3 style="color: #333; font-size: 18px; margin: 0 0 15px 0; font-weight: 600;">Household Income Information</h3>
                        ${$('#household-income-info').html()}
                    </div>
                    <div style="margin: 25px 0;">
                        <h3 style="color: #333; font-size: 18px; margin: 0 0 15px 0; font-weight: 600;">Fortnightly Breakdown</h3>
                        ${$('#summary-weekly').html()}
                    </div>
                    <div style="margin: 25px 0;">
                        <h3 style="color: #333; font-size: 18px; margin: 0 0 15px 0; font-weight: 600;">Child Details</h3>
                        ${$('#child-details-summary').html()}
                    </div>
                </div>
            `;
            
            // Disable submit button
            $('#custom-summary-form button[type="submit"]').prop('disabled', true).text('Sending...');
            
            // Send to WordPress
            $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                action: 'send_summary_email',
                user_name: fullName,
                user_email: email,
                user_phone: phone,
                summary_html: summaryHTML,
                location: $('#suburb').val() || '',
                atsi_status: $('#atsi').val() || '',
                enrolment_option: getEnrollmentOptionText()
            }, function(response) {
                console.log('WordPress response:', response);
                if(response.success){
                    $('#send-summary-response').html('<div style="color:green; padding:10px; background:#d4edda; border-radius:4px; margin-top:15px;">✓ Summary sent successfully to your email!</div>');
                    $('#custom-summary-form')[0].reset();
                } else {
                    $('#send-summary-response').html('<div style="color:red; padding:10px; background:#f8d7da; border-radius:4px; margin-top:15px;">✗ Error: ' + (response.data || 'Please try again') + '</div>');
                }
            }).fail(function(xhr, status, error) {
                console.error('AJAX error:', error);
                $('#send-summary-response').html('<div style="color:red; padding:10px; background:#f8d7da; border-radius:4px; margin-top:15px;">✗ Connection error. Please try again.</div>');
            }).always(function() {
                $('#custom-summary-form button[type="submit"]').prop('disabled', false).text('Send Summary');
            });
        });
    }
    
    // Initialize form when step 4 is shown
    $(document).on('click', '#next3', function() {
        setTimeout(initForm, 500);
    });



    // handle child selection
    $('#child-select').change(function(){ const period=$('.summary-btn.button-primary').data('period')||'fortnight'; renderSummary(period,$(this).val()); });


});
</script>



<?php
        return ob_get_clean();
    }
}

