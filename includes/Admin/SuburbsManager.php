<?php

namespace CCSCalculator\Includes\Admin;

use CCSCalculator\Includes\Database\SuburbsTable;

if (!defined('ABSPATH')) {
    exit;
}

class SuburbsManager
{
    private $suburbs_table = null;
    
    public function register()
    {
        add_action('admin_menu', [$this, 'add_menu'], 20);
        add_action('admin_post_ccs_import_suburbs', [$this, 'handle_import']);
        add_action('ccs_import_suburbs', [$this, 'background_import']);
        add_action('admin_init', [$this, 'maybe_create_table']);
    }
    
    private function get_suburbs_table()
    {
        if ($this->suburbs_table === null) {
            $this->suburbs_table = new SuburbsTable();
        }
        return $this->suburbs_table;
    }
    
    public function maybe_create_table()
    {
        if (get_option('ccs_needs_suburbs_table') === '1') {
            $table = $this->get_suburbs_table();
            $table->create_table();
            
            // Schedule import if table is empty
            if (!$table->has_data()) {
                wp_schedule_single_event(time() + 10, 'ccs_import_suburbs');
            }
            
            delete_option('ccs_needs_suburbs_table');
        }
    }
    
    public function add_menu()
    {
        add_submenu_page(
            'child-care-subsidy',
            'Suburbs Database',
            'Suburbs Database',
            'manage_options',
            'child-care-suburbs',
            [$this, 'render_page']
        );
    }
    
    public function render_page()
    {
        echo '<div class="ccs-admin-wrap">';
        $table = $this->get_suburbs_table();
        $count = $table->get_count();
        $has_data = $count > 0;
        
        if (isset($_GET['imported'])) {
            echo '<div class="notice notice-success"><p>Successfully imported ' . intval($_GET['imported']) . ' suburbs!</p></div>';
        }
        
        ?>
        <div class="wrap">
            <h1>Australian Suburbs Database</h1>
            
            <div class="card" style="max-width: 800px;">
                <h2>Database Status</h2>
                <table class="form-table">
                    <tr>
                        <th>Total Suburbs:</th>
                        <td><strong><?php echo number_format($count); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <?php if ($has_data): ?>
                                <span style="color: green;">✓ Active</span>
                            <?php else: ?>
                                <span style="color: red;">✗ No data</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Data Source:</th>
                        <td>Australian Postcodes Database (GitHub)</td>
                    </tr>
                </table>
            </div>
            
            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>Import Suburbs Data</h2>
                <p>Import or update the Australian suburbs database from the official source.</p>
                <p><strong>Note:</strong> This will replace all existing suburb data. The import may take 1-2 minutes.</p>
                
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" onsubmit="return confirm('This will replace all existing suburb data. Continue?');">
                    <input type="hidden" name="action" value="ccs_import_suburbs">
                    <?php wp_nonce_field('ccs_import_suburbs'); ?>
                    
                    <p>
                        <button type="submit" class="button button-primary button-large">
                            <?php echo $has_data ? 'Re-import Suburbs Data' : 'Import Suburbs Data'; ?>
                        </button>
                    </p>
                </form>
            </div>
            
            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>How It Works</h2>
                <ul>
                    <li>Suburbs are stored in a WordPress database table for fast searching</li>
                    <li>AJAX-powered autocomplete provides instant search results</li>
                    <li>Users can search by suburb name or postcode</li>
                    <li>Data is fetched dynamically - no large PHP files needed</li>
                    <li>Includes all Australian states and territories</li>
                </ul>
            </div>
        </div>
        <?php
        echo '</div>';
    }
    
    public function handle_import()
    {
        check_admin_referer('ccs_import_suburbs');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        set_time_limit(300); // 5 minutes
        
        $table = $this->get_suburbs_table();
        $result = $table->import_from_csv();
        
        if ($result['success']) {
            wp_redirect(add_query_arg([
                'page' => 'child-care-suburbs',
                'imported' => $result['imported']
            ], admin_url('admin.php')));
        } else {
            wp_die('Import failed: ' . $result['message']);
        }
        
        exit;
    }
    
    public function background_import()
    {
        $table = $this->get_suburbs_table();
        $table->import_from_csv();
    }
}
