<?php

namespace CCSCalculator\Includes\Ajax;

use CCSCalculator\Includes\Database\SuburbsTable;

if (!defined('ABSPATH')) {
    exit;
}

class SuburbSearch
{
    private $suburbs_table = null;
    
    public function register()
    {
        add_action('wp_ajax_ccs_search_suburbs', [$this, 'search_suburbs']);
        add_action('wp_ajax_nopriv_ccs_search_suburbs', [$this, 'search_suburbs']);
    }
    
    private function get_suburbs_table()
    {
        if ($this->suburbs_table === null) {
            $this->suburbs_table = new SuburbsTable();
        }
        return $this->suburbs_table;
    }
    
    public function search_suburbs()
    {
        $query = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
        
        if (empty($query)) {
            wp_send_json_success([]);
            return;
        }
        
        $table = $this->get_suburbs_table();
        $results = $table->search($query, 50);
        
        $formatted = array_map(function($item) {
            return [
                'suburb' => $item->suburb,
                'postcode' => $item->postcode,
                'state' => $item->state,
                'display' => $item->suburb . ' — ' . $item->postcode . ' (' . $item->state . ')'
            ];
        }, $results);
        
        wp_send_json_success($formatted);
    }
}
