<?php

class SmartDataFetcher_JobHandler {

    public static function init() {
        
        // Create table for tracking fetched data
        register_activation_hook( __FILE__, [ __CLASS__, 'create_tracking_table' ] );
        // Schedule the job on plugin activation.
        register_activation_hook( __FILE__, [ __CLASS__, 'schedule_jobs' ] );

        // Clear the job on plugin deactivation.
        register_deactivation_hook( __FILE__, [ __CLASS__, 'clear_jobs' ] );

        // Hook the job handler to the action.
        add_action( 'smart_data_fetcher_cron_job', [ __CLASS__, 'handle_job' ] );
    }

    function create_tracking_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'firm_api_tracking';
        $charset_collate = $wpdb->get_charset_collate();
    
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            frn_number VARCHAR(64) NOT NULL UNIQUE,
            status VARCHAR(32) DEFAULT pending,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate;";
    
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function schedule_jobs() {
        if ( ! wp_next_scheduled( 'smart_data_fetcher_cron_job' ) ) {
            wp_schedule_event( time(), 'hourly', 'smart_data_fetcher_cron_job' ); // Run hourly.
        }
    }

    public static function clear_jobs() {
        $timestamp = wp_next_scheduled( 'smart_data_fetcher_cron_job' );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, 'smart_data_fetcher_cron_job' );
        }
    }

    public static function handle_job() {
        $data = SmartDataFetcher_DataFetcher::fetch_api_data();
        SmartDataFetcher_DataFetcher::insert_into_firm_api_tracking_table( $data );
    }
}
