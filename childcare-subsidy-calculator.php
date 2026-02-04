<?php
/**
 * Plugin Name: The Child Care Subsidy Calculator
 * Description: Calculate Australian Child Care Subsidy (CCS) with multi-child support, age-based caps, and professional email delivery.
 * Version: 2.1.0
 * Author: i9 Education
 * Author URI: https://i9.edu.au/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: childcare-subsidy-calculator
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Verify system requirements on activation
 */
function ccs_check_requirements() {
    $plugin_data = get_file_data(__FILE__, [
        'RequiresWP' => 'Requires at least',
        'RequiresPHP' => 'Requires PHP'
    ]);
    
    $required_wp = $plugin_data['RequiresWP'] ?? '5.0';
    $required_php = $plugin_data['RequiresPHP'] ?? '7.4';
    
    global $wp_version;
    $errors = [];
    
    // Check WordPress version
    if (version_compare($wp_version, $required_wp, '<')) {
        $errors[] = sprintf(
            'WordPress %s or higher is required. You are running version %s.',
            $required_wp,
            $wp_version
        );
    }
    
    // Check PHP version
    if (version_compare(PHP_VERSION, $required_php, '<')) {
        $errors[] = sprintf(
            'PHP %s or higher is required. You are running version %s.',
            $required_php,
            PHP_VERSION
        );
    }
    
    if (!empty($errors)) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            '<h1>Plugin Activation Failed</h1>' .
            '<p><strong>Child Care Subsidy Calculator</strong> could not be activated due to the following requirements:</p>' .
            '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>' .
            '<p>Please upgrade your system to meet these requirements and try again.</p>' .
            '<p><a href="' . admin_url('plugins.php') . '">&larr; Back to Plugins</a></p>',
            'Plugin Activation Error',
            ['back_link' => true]
        );
    }
}
register_activation_hook(__FILE__, 'ccs_check_requirements');

// Runtime check for PHP version (backup check)
if (version_compare(PHP_VERSION, '7.4', '<')) {
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p><strong>Child Care Subsidy Calculator:</strong> This plugin requires PHP 7.4 or higher. You are running PHP ' . PHP_VERSION . '. Please contact your hosting provider to upgrade.</p></div>';
    });
    return;
}

// Removed debug notice - plugin is working

// Define plugin paths
if (!defined('CCS_CALCULATOR_PLUGIN_DIR')) {
    define('CCS_CALCULATOR_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (!defined('CCS_CALCULATOR_PLUGIN_URL')) {
    define('CCS_CALCULATOR_PLUGIN_URL', plugin_dir_url(__FILE__));
}

// Manually require all class files (no autoloader)
require_once CCS_CALCULATOR_PLUGIN_DIR . 'includes/Admin/Menu.php';
require_once CCS_CALCULATOR_PLUGIN_DIR . 'includes/Admin/Settings.php';
require_once CCS_CALCULATOR_PLUGIN_DIR . 'includes/Admin/SuburbsManager.php';
require_once CCS_CALCULATOR_PLUGIN_DIR . 'includes/Ajax/Email.php';
require_once CCS_CALCULATOR_PLUGIN_DIR . 'includes/Ajax/SuburbSearch.php';
require_once CCS_CALCULATOR_PLUGIN_DIR . 'includes/CPT/Submissions.php';
require_once CCS_CALCULATOR_PLUGIN_DIR . 'includes/Database/SuburbsTable.php';
require_once CCS_CALCULATOR_PLUGIN_DIR . 'includes/Frontend/Assets.php';
require_once CCS_CALCULATOR_PLUGIN_DIR . 'includes/Frontend/Shortcode.php';

// Activation hook
register_activation_hook(__FILE__, function() {
    $defaults = [
        'income_base_threshold' => 85279,
        'income_zero_threshold' => 535279,
        'income_step'           => 5000,
        'max_pct'               => 0.90,
        'hourly_caps' => [
            'centre_below_school_age' => 14.63,
            'centre_school_age'       => 12.81,
            'family_day_care_all'     => 13.56,
            'oshc_below_school_age'   => 14.63,
            'oshc_school_age'         => 12.81,
            'in_home_family'          => 39.80
        ],
        'last_updated'          => date('Y-m-d'),
        'disclaimer_text'       => 'This is an estimate only. Final entitlements determined by Services Australia. From 5 January 2026, all CCS eligible families can get at least 72 hours (3 days) of subsidised child care per fortnight under the 3 Day Guarantee.',
    ];
    update_option('childcare_ccs_policy', $defaults, true);
    update_option('ccs_needs_suburbs_table', '1', true);
    update_option('ccs_plugin_activated', current_time('mysql'), true);
});

// Initialize components
add_action('init', function() {
    // Register Menu
    $menu = new CCSCalculator\Includes\Admin\Menu();
    $menu->register();

    // Register Settings
    $settings = new CCSCalculator\Includes\Admin\Settings();
    $settings->register();

    // Register Submissions CPT
    $submissions = new CCSCalculator\Includes\CPT\Submissions();
    $submissions->register();

    // Register Email AJAX
    $email = new CCSCalculator\Includes\Ajax\Email();
    $email->register();

    // Register Shortcode
    $shortcode = new CCSCalculator\Includes\Frontend\Shortcode();
    $shortcode->register();

    // Register Assets
    $assets = new CCSCalculator\Includes\Frontend\Assets();
    $assets->register();

    // Register Suburbs Manager
    $suburbs_manager = new CCSCalculator\Includes\Admin\SuburbsManager();
    $suburbs_manager->register();

    // Register Suburb Search
    $suburb_search = new CCSCalculator\Includes\Ajax\SuburbSearch();
    $suburb_search->register();
}, 10);

// Plugin loaded successfully
