<?php

namespace CCSCalculator\Includes\Database;

if (!defined('ABSPATH')) {
    exit;
}

class SuburbsTable
{
    private $table_name;
    
    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'ccs_suburbs';
    }
    
    /**
     * Create suburbs table
     */
    public function create_table()
    {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            suburb varchar(100) NOT NULL,
            postcode varchar(4) NOT NULL,
            state varchar(3) NOT NULL,
            PRIMARY KEY (id),
            KEY suburb_idx (suburb),
            KEY postcode_idx (postcode),
            KEY state_idx (state)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Search suburbs by name or postcode
     */
    public function search($query, $limit = 50)
    {
        global $wpdb;
        
        $query = sanitize_text_field($query);
        $query = $wpdb->esc_like($query);
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT suburb, postcode, state 
            FROM {$this->table_name} 
            WHERE suburb LIKE %s OR postcode LIKE %s 
            ORDER BY 
                CASE 
                    WHEN suburb LIKE %s THEN 1
                    ELSE 2
                END,
                suburb ASC 
            LIMIT %d",
            '%' . $query . '%',
            $query . '%',
            $query . '%',
            $limit
        ));
        
        return $results;
    }
    
    /**
     * Import suburbs from CSV URL
     */
    public function import_from_csv($csv_url = null)
    {
        global $wpdb;
        
        if (!$csv_url) {
            $csv_url = 'https://raw.githubusercontent.com/matthewproctor/australianpostcodes/master/australian_postcodes.csv';
        }
        
        // Download CSV
        $response = wp_remote_get($csv_url, ['timeout' => 60]);
        
        if (is_wp_error($response)) {
            return ['success' => false, 'message' => 'Failed to download CSV'];
        }
        
        $csv_data = wp_remote_retrieve_body($response);
        $lines = explode("\n", $csv_data);
        
        // Clear existing data
        $wpdb->query("TRUNCATE TABLE {$this->table_name}");
        
        $imported = 0;
        $skipped = 0;
        
        // Skip header
        array_shift($lines);
        
        foreach ($lines as $line) {
            $data = str_getcsv($line);
            
            if (count($data) >= 4 && !empty($data[1]) && !empty($data[2])) {
                $postcode = sanitize_text_field($data[1]);
                $suburb = sanitize_text_field($data[2]);
                $state = sanitize_text_field($data[3]);
                
                // Validate postcode
                if (preg_match('/^\d{4}$/', $postcode) && !empty($suburb)) {
                    $wpdb->insert(
                        $this->table_name,
                        [
                            'suburb' => strtoupper($suburb),
                            'postcode' => $postcode,
                            'state' => strtoupper($state)
                        ],
                        ['%s', '%s', '%s']
                    );
                    $imported++;
                } else {
                    $skipped++;
                }
            }
        }
        
        return [
            'success' => true,
            'imported' => $imported,
            'skipped' => $skipped
        ];
    }
    
    /**
     * Get total count
     */
    public function get_count()
    {
        global $wpdb;
        return $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}");
    }
    
    /**
     * Check if table has data
     */
    public function has_data()
    {
        return $this->get_count() > 0;
    }
}
