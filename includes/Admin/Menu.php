<?php

namespace CCSCalculator\Includes\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class Menu
{
    public function register()
    {
        add_action('admin_menu', [$this, 'add_menus']);
        add_action('admin_enqueue_scripts', function($hook) {
            if (strpos($hook, 'child-care') !== false) {
                wp_enqueue_style('ccs-admin-beauty', CCS_CALCULATOR_PLUGIN_URL . 'assets/css/admin-beauty.css', [], '2.0.2');
            }
        });
    }

    public function add_menus()
    {
        // Main menu
        add_menu_page(
            'CCS Calculator',
            'CCS Calculator',
            'manage_options',
            'child-care-subsidy',
            [$this, 'childcare_ccs_main_page'],
            'dashicons-calculator',
            30
        );

        // Submenu: Dashboard (rename default)
        add_submenu_page(
            'child-care-subsidy',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'child-care-subsidy',
            [$this, 'childcare_ccs_main_page']
        );

        // Submenu: Calculator Settings
        add_submenu_page(
            'child-care-subsidy',
            'Calculator Settings',
            'Calculator Settings',
            'manage_options',
            'child-care-settings',
            [$this, 'childcare_ccs_settings_page']
        );

        // Submenu: Form Integration
        add_submenu_page(
            'child-care-subsidy',
            'Form Integration',
            'Form Integration',
            'manage_options',
            'child-care-hubspot',
            [$this, 'childcare_ccs_hubspot_page']
        );

        // Submenu: Appearance
        add_submenu_page(
            'child-care-subsidy',
            'Appearance',
            'Appearance',
            'manage_options',
            'child-care-styling',
            [$this, 'childcare_ccs_styling_page']
        );

        // Submenu: Custom CSS
        add_submenu_page(
            'child-care-subsidy',
            'Custom CSS',
            'Custom CSS',
            'manage_options',
            'child-care-custom-css',
            [$this, 'childcare_ccs_custom_css_page']
        );

        // Submenu: Submissions - Custom page to avoid CPT issues
        add_submenu_page(
            'child-care-subsidy',
            'Submissions',
            'Submissions',
            'manage_options',
            'ccs-submissions',
            [$this, 'childcare_ccs_submissions_page']
        );

        // Note: Suburbs Database menu is added by SuburbsManager class

        // Submenu: Email Template
        add_submenu_page(
            'child-care-subsidy',
            'Email Template',
            'Email Template',
            'manage_options',
            'child-care-email',
            [$this, 'childcare_ccs_email_page']
        );

        // Submenu: How to Use
        add_submenu_page(
            'child-care-subsidy',
            'How to Use',
            'How to Use',
            'manage_options',
            'child-care-shortcode',
            [$this, 'childcare_ccs_shortcode_page']
        );

        // Submenu: Changelog
        add_submenu_page(
            'child-care-settings',
            'Changelog',
            'Changelog',
            'manage_options',
            'child-care-changelog',
            [$this, 'childcare_ccs_changelog_page']
        );
    }

    public function childcare_ccs_settings_page()
    {
        require __DIR__ . '/views/settings-page.php';
    }

    public function childcare_ccs_hubspot_page()
    {
        require __DIR__ . '/views/hubspot-page.php';
    }

    public function childcare_ccs_styling_page()
    {
        require __DIR__ . '/views/styling-page.php';
    }

    public function childcare_ccs_custom_css_page()
    {
        require __DIR__ . '/views/custom-css-page.php';
    }

    public function childcare_ccs_email_page()
    {
        require __DIR__ . '/views/email-page.php';
    }

    public function childcare_ccs_shortcode_page()
    {
        require __DIR__ . '/views/shortcode-page.php';
    }

    public function childcare_ccs_main_page()
    {
        require __DIR__ . '/views/main-page.php';
    }

    public function childcare_ccs_submissions_page()
    {
        require __DIR__ . '/views/submissions-page.php';
    }

    private function view_single_submission($post_id)
    {
        require __DIR__ . '/views/single-submission.php';
    }

    public function childcare_ccs_changelog_page()
    {
        require __DIR__ . '/views/changelog-page.php';
    }
}

