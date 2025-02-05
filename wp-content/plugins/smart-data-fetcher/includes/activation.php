<?php


// Activation: Create the database table and schedule cron jobs.
function sdf_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'firm_api_tracking';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        frn VARCHAR(255) NOT NULL UNIQUE,
        status ENUM('pending', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );


    if ( ! wp_next_scheduled( 'sdf_api_fetch_cron' ) ) {
        $timestamp = mktime(2, 0, 0, date('n'), date('j') + 1, date('Y'));
        wp_schedule_event( $timestamp, 'daily', 'sdf_api_fetch_cron' );
    }

    if ( ! wp_next_scheduled( 'sdf_process_fetched_data_cron' ) ) {
        $timestamp = mktime(5, 0, 0, date('n'), date('j') + 1, date('Y'));
        wp_schedule_event( $timestamp, 'daily', 'sdf_process_fetched_data_cron' );
    }
    
}


