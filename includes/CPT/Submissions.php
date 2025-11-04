<?php

namespace CCSCalculator\Includes\CPT;

if (!defined('ABSPATH')) {
    exit;
}

class Submissions
{
    public function register()
    {
        add_action('init', [$this, 'register_cpt'], 0);  // Priority 0 to register early
        add_filter('manage_ccs_submission_posts_columns', [$this, 'columns']);
        add_action('manage_ccs_submission_posts_custom_column', [$this, 'custom_column'], 10, 2);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('admin_init', [$this, 'check_and_flush_rewrites']);
        
        // Flush rewrite rules on activation
        register_activation_hook(CCS_CALCULATOR_PLUGIN_DIR . 'childcare-subsidy-calculator.php', [$this, 'flush_rewrites']);
    }
    
    public function check_and_flush_rewrites()
    {
        // Check if we need to flush
        if (get_option('ccs_flush_rewrite_rules') === 'yes') {
            flush_rewrite_rules();
            delete_option('ccs_flush_rewrite_rules');
        }
    }
    
    public function flush_rewrites()
    {
        $this->register_cpt();
        flush_rewrite_rules();
        update_option('ccs_flush_rewrite_rules', 'yes');
    }

    public function register_cpt()
    {
        $labels = [
            'name' => 'CCS Submissions',
            'singular_name' => 'CCS Submission',
            'menu_name' => 'CCS Submissions',
            'add_new_item' => 'Add New Submission',
            'edit_item' => 'Edit Submission',
            'view_item' => 'View Submission',
            'all_items' => 'All Submissions',
            'search_items' => 'Search Submissions',
            'not_found' => 'No submissions found',
        ];
        register_post_type('ccs_submission', [
            'labels' => $labels,
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'show_in_admin_bar' => false,
            'supports' => ['title', 'editor', 'custom-fields'],
            'capability_type' => 'post',
            'capabilities' => [
                'create_posts' => 'manage_options',
                'edit_post' => 'manage_options',
                'read_post' => 'manage_options',
                'delete_post' => 'manage_options',
                'edit_posts' => 'manage_options',
                'edit_others_posts' => 'manage_options',
                'delete_posts' => 'manage_options',
                'publish_posts' => 'manage_options',
                'read_private_posts' => 'manage_options',
            ],
            'map_meta_cap' => true,
            'has_archive' => false,
            'hierarchical' => false,
            'rewrite' => false,
            'query_var' => 'ccs_submission',
            'can_export' => true,
            'delete_with_user' => false,
        ]);
    }

    public function columns($columns)
    {
        $columns['user_email'] = 'Email';
        $columns['user_phone'] = 'Phone';
        $columns['date_submitted'] = 'Date Submitted';
        return $columns;
    }

    public function custom_column($column, $post_id)
    {
        if ($column === 'user_email') echo esc_html(get_post_meta($post_id, 'user_email', true));
        if ($column === 'user_phone') echo esc_html(get_post_meta($post_id, 'user_phone', true));
        if ($column === 'date_submitted') echo get_the_date('Y-m-d H:i', $post_id);
    }
    
    public function add_meta_boxes()
    {
        add_meta_box(
            'ccs_submission_details',
            'Submission Details',
            [$this, 'render_submission_details'],
            'ccs_submission',
            'side',
            'high'
        );
    }
    
    public function render_submission_details($post)
    {
        $user_email = get_post_meta($post->ID, 'user_email', true);
        $user_phone = get_post_meta($post->ID, 'user_phone', true);
        $submission_date = get_post_meta($post->ID, 'submission_date', true);
        
        if (empty($submission_date)) {
            $submission_date = $post->post_date;
        }
        ?>
        <div style="padding:10px 0;">
            <p style="margin:10px 0;">
                <strong>📧 Email:</strong><br>
                <?php if ($user_email): ?>
                    <a href="mailto:<?php echo esc_attr($user_email); ?>" style="word-break:break-all;">
                        <?php echo esc_html($user_email); ?>
                    </a>
                <?php else: ?>
                    <span style="color:#999;">Not provided</span>
                <?php endif; ?>
            </p>
            
            <p style="margin:10px 0;">
                <strong>📱 Phone:</strong><br>
                <?php if ($user_phone): ?>
                    <a href="tel:<?php echo esc_attr($user_phone); ?>">
                        <?php echo esc_html($user_phone); ?>
                    </a>
                <?php else: ?>
                    <span style="color:#999;">Not provided</span>
                <?php endif; ?>
            </p>
            
            <p style="margin:10px 0;">
                <strong>📅 Submitted:</strong><br>
                <?php echo date('F j, Y', strtotime($submission_date)); ?><br>
                <span style="color:#666; font-size:12px;">
                    <?php echo date('g:i a', strtotime($submission_date)); ?>
                </span>
            </p>
            
            <hr style="margin:15px 0;">
            
            <p style="margin:10px 0; font-size:12px; color:#666;">
                <strong>Note:</strong> The calculation summary is shown in the main editor above.
            </p>
        </div>
        <?php
    }
}

