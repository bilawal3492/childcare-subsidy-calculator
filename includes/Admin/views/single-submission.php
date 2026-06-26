<?php
if (!defined('ABSPATH')) { exit; }
        $submission = get_post($post_id);
        
        if (!$submission || $submission->post_type !== 'ccs_submission') {
            echo '<div class="wrap"><div class="notice notice-error"><p>Submission not found.</p></div></div>';
            return;
        }
        
        $user_name = get_post_meta($post_id, 'user_name', true);
        $user_email = get_post_meta($post_id, 'user_email', true);
        $user_phone = get_post_meta($post_id, 'user_phone', true);
        $submission_date = get_post_meta($post_id, 'submission_date', true);
        $location = get_post_meta($post_id, 'location', true);
        $atsi_status = get_post_meta($post_id, 'atsi_status', true);
        $enrolment_option = get_post_meta($post_id, 'enrolment_option', true);
        $shadow_result = get_post_meta($post_id, 'ccs_shadow_result', true);
        $shadow_diffs = get_post_meta($post_id, 'ccs_shadow_diffs', true);
        $server_figures = get_post_meta($post_id, 'ccs_server_figures', true);

        if (empty($submission_date)) {
            $submission_date = $submission->post_date;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html($submission->post_title); ?></h1>
            <a href="<?php echo admin_url('admin.php?page=ccs-submissions'); ?>" class="page-title-action">← Back to All Submissions</a>
            <hr class="wp-header-end">
            
            <div style="display:grid; grid-template-columns:2fr 1fr; gap:20px; margin-top:20px;">
                <!-- Main Content -->
                <div>
                    <div style="background:#fff; padding:20px; border:1px solid #ccc; border-radius:8px;">
                        <h2 style="margin-top:0;">Calculation Summary</h2>
                        <?php echo wp_kses_post($submission->post_content); ?>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div>
                    <?php if (!empty($shadow_result)): ?>
                    <?php
                        $is_match = ($shadow_result === 'match');
                        $badge_bg = $is_match ? '#edfaef' : '#fcf0f1';
                        $badge_border = $is_match ? '#46b450' : '#d63638';
                        $badge_text = $is_match ? '#1a7f37' : '#b32d2e';
                    ?>
                    <div style="background:<?php echo $badge_bg; ?>; padding:15px 20px; border:1px solid <?php echo $badge_border; ?>; border-left-width:4px; border-radius:8px; margin-bottom:20px;">
                        <h3 style="margin-top:0; color:<?php echo $badge_text; ?>;">
                            Calculation Check: <?php echo $is_match ? '✓ Match' : '✗ Mismatch'; ?>
                        </h3>
                        <p style="margin:0; font-size:13px; color:#555;">
                            <?php if ($is_match): ?>
                                Server-side engine agrees with the browser calculation.
                            <?php else: ?>
                                Server-side engine differs from the browser numbers:
                            <?php endif; ?>
                        </p>
                        <?php if (!$is_match && !empty($shadow_diffs)): ?>
                            <?php $diffs = json_decode($shadow_diffs, true); ?>
                            <?php if (is_array($diffs)): ?>
                            <table style="width:100%; margin-top:10px; font-size:12px; border-collapse:collapse;">
                                <tr style="text-align:left; color:#555;">
                                    <th style="padding:4px;">Field</th><th style="padding:4px;">Browser</th><th style="padding:4px;">Server</th>
                                </tr>
                                <?php foreach ($diffs as $field => $vals): ?>
                                <tr>
                                    <td style="padding:4px;"><?php echo esc_html($field); ?></td>
                                    <td style="padding:4px;"><?php echo esc_html($vals['browser'] ?? ''); ?></td>
                                    <td style="padding:4px;"><?php echo esc_html($vals['server'] ?? ''); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </table>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php
                    $figs = !empty($server_figures) ? json_decode($server_figures, true) : null;
                    if (is_array($figs) && !empty($figs['totals'])):
                        $t = $figs['totals'];
                    ?>
                    <div style="background:#fff; padding:20px; border:1px solid #ccc; border-radius:8px; margin-bottom:20px;">
                        <h3 style="margin-top:0;">Verified Figures <span style="font-size:11px; font-weight:400; color:#888;">(server-computed)</span></h3>
                        <table style="width:100%; font-size:13px; border-collapse:collapse;">
                            <tr><td style="padding:4px 0; color:#555;">Standard CCS</td><td style="text-align:right; font-weight:600;"><?php echo esc_html(number_format((float)($figs['standard_pct'] ?? 0), 2)); ?>%</td></tr>
                            <tr><td style="padding:4px 0; color:#555;">Higher CCS</td><td style="text-align:right; font-weight:600;"><?php echo esc_html(number_format((float)($figs['higher_pct'] ?? 0), 2)); ?>%</td></tr>
                            <tr><td style="padding:4px 0; color:#555;">CCS hours / fortnight</td><td style="text-align:right; font-weight:600;"><?php echo esc_html((int)($figs['ccs_hours_per_fortnight'] ?? 0)); ?></td></tr>
                            <tr><td colspan="2" style="border-top:1px solid #eee; padding-top:6px;"></td></tr>
                            <tr><td style="padding:4px 0; color:#555;">Total fees (fortnight)</td><td style="text-align:right; font-weight:600;">$<?php echo esc_html(number_format((float)($t['fortnightFee'] ?? 0), 2)); ?></td></tr>
                            <tr><td style="padding:4px 0; color:#555;">Subsidy (fortnight)</td><td style="text-align:right; font-weight:600;">$<?php echo esc_html(number_format((float)($t['fortnightSub'] ?? 0), 2)); ?></td></tr>
                            <tr><td style="padding:4px 0; color:#555;">Out of pocket (fortnight)</td><td style="text-align:right; font-weight:600;">$<?php echo esc_html(number_format((float)($t['outPocket'] ?? 0), 2)); ?></td></tr>
                        </table>
                    </div>
                    <?php endif; ?>

                    <div style="background:#fff; padding:20px; border:1px solid #ccc; border-radius:8px; margin-bottom:20px;">
                        <h3 style="margin-top:0;">Contact Details</h3>
                        
                        <p style="margin:15px 0;">
                            <strong>👤 Name:</strong><br>
                            <?php echo $user_name ? esc_html($user_name) : '<span style="color:#999;">Not provided</span>'; ?>
                        </p>
                        
                        <p style="margin:15px 0;">
                            <strong>📧 Email:</strong><br>
                            <?php if ($user_email): ?>
                                <a href="mailto:<?php echo esc_attr($user_email); ?>" style="word-break:break-all;">
                                    <?php echo esc_html($user_email); ?>
                                </a>
                            <?php else: ?>
                                <span style="color:#999;">Not provided</span>
                            <?php endif; ?>
                        </p>
                        
                        <p style="margin:15px 0;">
                            <strong>📱 Phone:</strong><br>
                            <?php if ($user_phone): ?>
                                <a href="tel:<?php echo esc_attr($user_phone); ?>">
                                    <?php echo esc_html($user_phone); ?>
                                </a>
                            <?php else: ?>
                                <span style="color:#999;">Not provided</span>
                            <?php endif; ?>
                        </p>
                        
                        <p style="margin:15px 0;">
                            <strong>📅 Submitted:</strong><br>
                            <?php echo date('F j, Y', strtotime($submission_date)); ?><br>
                            <span style="color:#666; font-size:13px;">
                                <?php echo date('g:i a', strtotime($submission_date)); ?>
                            </span>
                        </p>
                    </div>
                    
                    <div style="background:#fff; padding:20px; border:1px solid #ccc; border-radius:8px; margin-bottom:20px;">
                        <h3 style="margin-top:0;">📍 Location & Details</h3>
                        
                        <p style="margin:15px 0;">
                            <strong>Location:</strong><br>
                            <?php echo $location ? esc_html($location) : '<span style="color:#999;">Not provided</span>'; ?>
                        </p>
                        
                        <p style="margin:15px 0;">
                            <strong>ATSI Status:</strong><br>
                            <?php echo $atsi_status ? ucfirst(esc_html($atsi_status)) : '<span style="color:#999;">Not provided</span>'; ?>
                        </p>
                        
                        <p style="margin:15px 0;">
                            <strong>Enrolment Option:</strong><br>
                            <?php echo $enrolment_option ? esc_html($enrolment_option) : '<span style="color:#999;">Not provided</span>'; ?>
                        </p>
                        <?php
                        $consent_privacy = get_post_meta($post_id, 'consent_privacy', true);
                        $consent_contact = get_post_meta($post_id, 'consent_contact', true);
                        $consent_timestamp = get_post_meta($post_id, 'consent_timestamp', true);
                        if ($consent_timestamp !== '' || $consent_privacy !== ''):
                        ?>
                        <p style="margin:15px 0;">
                            <strong>Consent:</strong><br>
                            Privacy policy: <?php echo $consent_privacy ? '✓ Agreed' : '<span style="color:#999;">No record</span>'; ?><br>
                            Contact opt-in: <?php echo $consent_contact ? '✓ Yes' : 'No'; ?>
                            <?php if ($consent_timestamp): ?>
                                <br><span style="color:#999; font-size:12px;">Recorded: <?php echo esc_html($consent_timestamp); ?></span>
                            <?php endif; ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    
                    <div style="background:#fff; padding:20px; border:1px solid #ccc; border-radius:8px;">
                        <p style="margin:0;">
                            <a href="<?php echo admin_url('admin.php?page=ccs-submissions&action=delete&post_id=' . $post_id); ?>" 
                               class="button button-secondary" 
                               onclick="return confirm('Are you sure you want to delete this submission?');"
                               style="width:100%; text-align:center; color:#b32d2e;">
                                🗑️ Delete Submission
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php
