<?php

// Function to register the 'location' taxonomy
function register_location_taxonomy() {
    // Register the taxonomy
    $args = array(
        'hierarchical' => true, // Set to true for a category-like structure
        'labels' => array(
            'name' => 'Locations',
            'singular_name' => 'Location',
            'menu_name' => 'Location',
            'all_items' => 'All Locations',
            'edit_item' => 'Edit Location',
            'view_item' => 'View Location',
            'update_item' => 'Update Location',
            'add_new_item' => 'Add New Location',
            'new_item_name' => 'New Location Name',
        ),
        'show_ui' => true, // Show the taxonomy in the WordPress admin
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'location'), // Custom URL structure
    );

    // Register taxonomy for the custom post type 'listing'
    register_taxonomy('location', 'listing', $args);
}

// Activation: Create the database table and schedule cron jobs.
function sdf_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'firm_api_tracking';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        frn VARCHAR(255) NOT NULL,
        status ENUM('pending', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );


    if ( ! wp_next_scheduled( 'sdf_daily_fetch_cron' ) ) {
        wp_schedule_event( time(), 'daily', 'sdf_daily_fetch_cron' );
    }

    if ( ! wp_next_scheduled( 'sdf_process_fetched_data_cron' ) ) {
        wp_schedule_event( time(), 'hourly', 'sdf_process_fetched_data_cron' );
    }
    
    register_location_taxonomy();
    
}


