<?php


// Activation: Create the database table and schedule cron jobs.
function sdf_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'firm_api_tracking';
    $charset_collate = $wpdb->get_charset_collate();

    // Check if the table exists
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;

    if (!$table_exists) {
        // Create the table if it does not exist
        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            frn VARCHAR(255) NOT NULL UNIQUE,
            status ENUM('pending', 'completed') DEFAULT 'pending',
            post_id BIGINT(20) UNSIGNED NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) $charset_collate;";
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    } else {
        // Get existing columns
        $existing_columns = $wpdb->get_col("DESCRIBE $table_name");

        // Define required columns
        $required_columns = [
            'frn' => "ADD COLUMN frn VARCHAR(255) NOT NULL UNIQUE",
            'status' => "ADD COLUMN status ENUM('pending', 'completed') DEFAULT 'pending'",
            'post_id' => "ADD COLUMN post_id BIGINT(20) UNSIGNED NULL",
            'created_at' => "ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
            'updated_at' => "ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
        ];

        // Loop through required columns and add if missing
        foreach ($required_columns as $column => $alter_query) {
            if (!in_array($column, $existing_columns)) {
                $wpdb->query("ALTER TABLE $table_name $alter_query;");
            }
        }
    }



    if ( ! wp_next_scheduled( 'sdf_api_fetch_cron' ) ) {
        $timestamp = mktime(2, 0, 0, date('n'), date('j') + 1, date('Y'));
        wp_schedule_event( $timestamp, 'daily', 'sdf_api_fetch_cron' );
    }

    if ( ! wp_next_scheduled( 'sdf_process_fetched_data_cron' ) ) {
        $timestamp = mktime(5, 0, 0, date('n'), date('j') + 1, date('Y'));
        wp_schedule_event( $timestamp, 'daily', 'sdf_process_fetched_data_cron' );
    }
    
}


