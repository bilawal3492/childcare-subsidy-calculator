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

        // Batch rows into multi-row INSERTs inside a transaction. This replaces
        // ~18,000 individual INSERT queries with ~36 batched queries, and keeps
        // the import atomic (a mid-import failure rolls back rather than leaving
        // a half-populated table after the TRUNCATE).
        $batch = [];
        $batch_size = 500;

        $wpdb->query('START TRANSACTION');

        foreach ($lines as $line) {
            $data = str_getcsv($line);

            if (count($data) >= 4 && !empty($data[1]) && !empty($data[2])) {
                $postcode = sanitize_text_field($data[1]);
                $suburb = sanitize_text_field($data[2]);
                $state = sanitize_text_field($data[3]);

                // Validate postcode
                if (preg_match('/^\d{4}$/', $postcode) && !empty($suburb)) {
                    $batch[] = [strtoupper($suburb), $postcode, strtoupper($state)];
                    $imported++;

                    if (count($batch) >= $batch_size) {
                        $this->insert_batch($batch);
                        $batch = [];
                    }
                } else {
                    $skipped++;
                }
            }
        }

        // Flush any remaining rows.
        if (!empty($batch)) {
            $this->insert_batch($batch);
        }

        $wpdb->query('COMMIT');

        return [
            'success' => true,
            'imported' => $imported,
            'skipped' => $skipped
        ];
    }

    /**
     * Insert a batch of suburb rows in a single multi-row, prepared INSERT.
     *
     * @param array $batch Array of [suburb, postcode, state] rows.
     */
    private function insert_batch(array $batch)
    {
        global $wpdb;

        if (empty($batch)) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($batch), '(%s,%s,%s)'));
        $values = [];
        foreach ($batch as $row) {
            $values[] = $row[0];
            $values[] = $row[1];
            $values[] = $row[2];
        }

        $sql = "INSERT INTO {$this->table_name} (suburb, postcode, state) VALUES {$placeholders}";
        $wpdb->query($wpdb->prepare($sql, $values));
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
