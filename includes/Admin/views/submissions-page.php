<?php
if (!defined('ABSPATH')) { exit; }
        if (!current_user_can('manage_options')) return;
        
        // Handle view action - show single submission
        if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['post_id'])) {
            $this->view_single_submission(intval($_GET['post_id']));
            return;
        }
        
        // Handle flush rewrite rules
        if (isset($_GET['action']) && $_GET['action'] === 'flush_rewrites') {
            flush_rewrite_rules();
            echo '<div class="notice notice-success is-dismissible"><p><strong>✓ Rewrite rules flushed!</strong> You can now view submissions.</p></div>';
        }
        
        // Handle delete action
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['post_id'])) {
            $post_id = intval($_GET['post_id']);
            wp_delete_post($post_id, true);
            echo '<div class="notice notice-success is-dismissible"><p>Submission deleted successfully.</p></div>';
        }
        
        // Get all submissions
        $args = [
            'post_type' => 'ccs_submission',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        ];
        
        $submissions = get_posts($args);
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">CCS Submissions</h1>
            <hr class="wp-header-end">
            
            <?php if (empty($submissions)): ?>
                <div style="background:#fff; padding:40px; text-align:center; border:1px solid #ccc; border-radius:8px; margin-top:20px;">
                    <p style="font-size:18px; color:#666;">📋 No submissions yet</p>
                    <p style="color:#999;">Submissions will appear here when users complete the calculator.</p>
                </div>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width:30%;">Name</th>
                            <th style="width:25%;">Email</th>
                            <th style="width:15%;">Phone</th>
                            <th style="width:20%;">Date</th>
                            <th style="width:10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $submission): 
                            $user_email = get_post_meta($submission->ID, 'user_email', true);
                            $user_phone = get_post_meta($submission->ID, 'user_phone', true);
                            $submission_date = get_post_meta($submission->ID, 'submission_date', true);
                            if (empty($submission_date)) {
                                $submission_date = $submission->post_date;
                            }
                        ?>
                        <tr>
                            <td>
                                <strong>
                                    <a href="<?php echo admin_url('admin.php?page=ccs-submissions&action=view&post_id=' . $submission->ID); ?>">
                                        <?php echo esc_html($submission->post_title); ?>
                                    </a>
                                </strong>
                            </td>
                            <td>
                                <?php if ($user_email): ?>
                                    <a href="mailto:<?php echo esc_attr($user_email); ?>">
                                        <?php echo esc_html($user_email); ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color:#999;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $user_phone ? esc_html($user_phone) : '<span style="color:#999;">—</span>'; ?>
                            </td>
                            <td>
                                <?php echo date('M j, Y g:i a', strtotime($submission_date)); ?>
                            </td>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=ccs-submissions&action=view&post_id=' . $submission->ID); ?>" class="button button-small">
                                    View
                                </a>
                                <a href="<?php echo admin_url('admin.php?page=ccs-submissions&action=delete&post_id=' . $submission->ID); ?>" 
                                   class="button button-small" 
                                   onclick="return confirm('Are you sure you want to delete this submission?');"
                                   style="color:#b32d2e;">
                                    Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <p style="margin-top:20px; color:#666;">
                    <strong>Total Submissions:</strong> <?php echo count($submissions); ?>
                </p>
            <?php endif; ?>
        </div>
        <?php
