<?php

namespace CCSCalculator\Includes\Privacy;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Privacy & data-retention handling for CCS submissions.
 *
 * - Registers WordPress personal-data exporter and eraser so calculator
 *   submissions are covered by data-subject (GDPR / Australian Privacy Act)
 *   export and erasure requests.
 * - Provides an optional, admin-controlled retention policy that purges old
 *   submissions on a daily cron. Disabled by default (0 days = keep forever)
 *   so lead data is never deleted unless the site owner opts in.
 */
class Privacy
{
    const CRON_HOOK = 'ccs_purge_old_submissions';

    public function register()
    {
        add_filter('wp_privacy_personal_data_exporters', [$this, 'register_exporter']);
        add_filter('wp_privacy_personal_data_erasers', [$this, 'register_eraser']);

        add_action(self::CRON_HOOK, [$this, 'purge_old_submissions']);
        add_action('init', [$this, 'maybe_schedule_cron']);
    }

    /* ---------------------------------------------------------------- *
     *  Retention
     * ---------------------------------------------------------------- */

    public function maybe_schedule_cron()
    {
        if (!wp_next_scheduled(self::CRON_HOOK)) {
            wp_schedule_event(time() + HOUR_IN_SECONDS, 'daily', self::CRON_HOOK);
        }
    }

    /**
     * Delete submissions older than the configured retention period.
     * Does nothing unless the admin has set ccs_retention_days > 0.
     */
    public function purge_old_submissions()
    {
        $days = (int) get_option('ccs_retention_days', 0);
        if ($days <= 0) {
            return; // retention disabled — never auto-delete
        }

        $query = new \WP_Query([
            'post_type'      => 'ccs_submission',
            'post_status'    => 'any',
            'posts_per_page' => 500,
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'date_query'     => [
                ['column' => 'post_date_gmt', 'before' => $days . ' days ago'],
            ],
        ]);

        foreach ($query->posts as $post_id) {
            wp_delete_post($post_id, true);
        }
    }

    /* ---------------------------------------------------------------- *
     *  Exporter
     * ---------------------------------------------------------------- */

    public function register_exporter($exporters)
    {
        $exporters['ccs-calculator'] = [
            'exporter_friendly_name' => 'Child Care Subsidy Calculator',
            'callback'               => [$this, 'export_data'],
        ];
        return $exporters;
    }

    public function export_data($email, $page = 1)
    {
        $items = [];

        foreach ($this->find_submissions_by_email($email) as $post) {
            $id = $post->ID;
            $items[] = [
                'group_id'    => 'ccs_submissions',
                'group_label' => 'Child Care Subsidy Calculator Submissions',
                'item_id'     => 'ccs-submission-' . $id,
                'data'        => [
                    ['name' => 'Name',            'value' => get_post_meta($id, 'user_name', true)],
                    ['name' => 'Email',           'value' => get_post_meta($id, 'user_email', true)],
                    ['name' => 'Phone',           'value' => get_post_meta($id, 'user_phone', true)],
                    ['name' => 'Location',        'value' => get_post_meta($id, 'location', true)],
                    ['name' => 'ATSI status',     'value' => get_post_meta($id, 'atsi_status', true)],
                    ['name' => 'Enrolment',       'value' => get_post_meta($id, 'enrolment_option', true)],
                    ['name' => 'Consent (privacy)', 'value' => get_post_meta($id, 'consent_privacy', true) ? 'Yes' : 'No'],
                    ['name' => 'Consent (contact)', 'value' => get_post_meta($id, 'consent_contact', true) ? 'Yes' : 'No'],
                    ['name' => 'Submitted',       'value' => get_post_meta($id, 'submission_date', true)],
                ],
            ];
        }

        return ['data' => $items, 'done' => true];
    }

    /* ---------------------------------------------------------------- *
     *  Eraser
     * ---------------------------------------------------------------- */

    public function register_eraser($erasers)
    {
        $erasers['ccs-calculator'] = [
            'eraser_friendly_name' => 'Child Care Subsidy Calculator',
            'callback'             => [$this, 'erase_data'],
        ];
        return $erasers;
    }

    public function erase_data($email, $page = 1)
    {
        $removed = false;

        foreach ($this->find_submissions_by_email($email) as $post) {
            wp_delete_post($post->ID, true);
            $removed = true;
        }

        return [
            'items_removed'  => $removed,
            'items_retained' => false,
            'messages'       => [],
            'done'           => true,
        ];
    }

    /* ---------------------------------------------------------------- *
     *  Helper
     * ---------------------------------------------------------------- */

    private function find_submissions_by_email($email)
    {
        $email = sanitize_email($email);
        if (empty($email)) {
            return [];
        }

        return get_posts([
            'post_type'   => 'ccs_submission',
            'post_status' => 'any',
            'numberposts' => -1,
            'meta_query'  => [
                ['key' => 'user_email', 'value' => $email],
            ],
        ]);
    }
}
