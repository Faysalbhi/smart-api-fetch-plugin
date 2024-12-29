<?php
/*
Plugin Name: Smart Data Fetcher
Description: Fetch data from an API and process it to store in custom and default WordPress tables.
Version: 1.0
Author: smartwebsource.com
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define constants.
define( 'SMART_DATA_FETCHER_VERSION', '1.0' );
define( 'SMART_DATA_FETCHER_PATH', plugin_dir_path( __FILE__ ) );

// Include necessary files.
require_once SMART_DATA_FETCHER_PATH . 'includes/deactivation.php';
require_once SMART_DATA_FETCHER_PATH . 'includes/global-variables.php';
require_once SMART_DATA_FETCHER_PATH . 'includes/update-post-table.php';

// // Function to register the 'location' taxonomy
// function register_location_taxonomy() {
//     // Register the taxonomy
//     $args = array(
//         'hierarchical' => true, // Set to true for a category-like structure
//         'labels' => array(
//             'name' => 'Locations',
//             'singular_name' => 'Location',
//             'menu_name' => 'Location',
//             'all_items' => 'All Locations',
//             'edit_item' => 'Edit Location',
//             'view_item' => 'View Location',
//             'update_item' => 'Update Location',
//             'add_new_item' => 'Add New Location',
//             'new_item_name' => 'New Location Name',
//         ),
//         'show_ui' => true, // Show the taxonomy in the WordPress admin
//         'show_admin_column' => true,
//         'query_var' => true,
//         'rewrite' => array('slug' => 'location'), // Custom URL structure
//     );

//     // Register taxonomy for the custom post type 'listing'
//     register_taxonomy('location', 'listing', $args);
// }

// Activation: Create the database table and schedule cron jobs.
function sdf_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'firm_api_tracking';
    $charset_collate = $wpdb->get_charset_collate();

    // Create table if it does not exist
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        frn VARCHAR(255) NOT NULL,
        status ENUM('pending', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) $charset_collate;";

    // Require upgrade function
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );

    // Schedule cron jobs if not scheduled already
    if ( ! wp_next_scheduled( 'sdf_daily_fetch_cron' ) ) {
        wp_schedule_event( time(), 'daily', 'sdf_daily_fetch_cron' );
    }

    if ( ! wp_next_scheduled( 'sdf_process_fetched_data_cron' ) ) {
        wp_schedule_event( time(), 'hourly', 'sdf_process_fetched_data_cron' );
    }
}
register_activation_hook(__FILE__, 'sdf_activate');
register_deactivation_hook( __FILE__, 'sdf_deactivate' );
// add_action('init', 'register_location_taxonomy');



// Fetch data from the first API.
add_action( 'sdf_daily_fetch_cron', 'sdf_fetch_data_from_api' );
function sdf_fetch_data_from_api() {
    global $wpdb, $firm_activity_api;
    $table_name = $wpdb->prefix . 'firm_api_tracking';

    $response = wp_remote_get( $firm_activity_api );

    if ( is_wp_error( $response ) ) {
        error_log( 'Error fetching data: ' . $response->get_error_message() );
        return;
    }

    $response_code = wp_remote_retrieve_response_code( $response );
    if ( $response_code !== 200 ) {
        error_log( "Error fetching data: HTTP Response Code $response_code" );
        return;
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( ! isset( $body['data'] ) || ! is_array( $body['data'] ) ) {
        error_log( 'Unexpected API response structure.' );
        return;
    }

    $firms = [];
    $firms = array_merge($firms, array_column($body['data'], 'frn'));

    $total_pages = isset( $body['meta']['total'], $body['meta']['per_page'] ) 
        ? ceil( $body['meta']['total'] / $body['meta']['per_page'] ) 
        : 1;

    // Fetch remaining pages
    for ( $page = 2; $page <= $total_pages; $page++ ) {
        $next_url = isset( $body['links']['next'] ) ? $body['links']['next'] : null;
        if ( empty( $next_url ) ) {
            break;
        }

        $per_page_response = wp_remote_get( $next_url );
        if ( is_wp_error( $per_page_response ) ) {
            error_log( 'Error fetching paginated data: ' . $per_page_response->get_error_message() );
            continue;
        }

        $response_code = wp_remote_retrieve_response_code( $per_page_response );
        if ( $response_code !== 200 ) {
            error_log( "Error fetching paginated data: HTTP Response Code $response_code" );
            continue;
        }

        $page_body = json_decode( wp_remote_retrieve_body( $per_page_response ), true );
        if ( isset( $page_body['data'] ) && is_array( $page_body['data'] ) ) {
            $firms = array_merge($firms, array_column($page_body['data'], 'frn'));
        }
    }

    // Insert new firms into the database
    foreach ( $firms as $frn ) {
        $exists = $wpdb->get_var(
            $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE frn = %s", $frn )
        );

        if ( $exists ) {
            continue; // Skip if already exists
        }

        $wpdb->insert(
            $table_name,
            [
                'frn' => sanitize_text_field( $frn ),
                'status'     => 'pending',
            ],
            [ '%s', '%s' ]
        );
    }
}




// Process the data from the `firm_api_tracking` table.
add_action( 'sdf_process_fetched_data_cron', 'sdf_process_data_and_create_posts' );
function sdf_process_data_and_create_posts() {
    global $wpdb, $single_firm_activity_api;
    $table_name = $wpdb->prefix . 'firm_api_tracking';

    // Fetch one pending record.
    $entry = $wpdb->get_row( "SELECT * FROM $table_name WHERE status = 'pending' LIMIT 1" );

    if ( ! $entry ) {
        // No pending entries, exit.
        return;
    }

    // Construct API URL
    $api_url = $single_firm_activity_api . $entry->frn;
    
    // Fetch data from the API
    $response = wp_remote_get( $api_url );
    
    if ( is_wp_error( $response ) ) {
        error_log( 'Error fetching firm data: ' . $response->get_error_message() );
        return;
    }

    $response_code = wp_remote_retrieve_response_code( $response );
    
    if ( $response_code !== 200 ) {
        error_log( "Error fetching firm data: HTTP Response Code $response_code" );
        return;
    }

    $data = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( isset( $data['data'] ) && is_array( $data['data'] ) ) {
        foreach ( $data['data'] as $firm_data ) {
            // Check if 'firm_name' and 'firm_status' are available in the response
            if ( isset( $firm_data['firm_name']) ) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'firm_api_tracking';

                

                // Insert data into the `posts` table
                $post_id = wp_insert_post( [
                    'post_title'   => sanitize_text_field( $firm_data['firm_name'] ),
                    'post_content' => wp_kses_post( $firm_data['description'] ),
                    'post_status'  => 'publish',
                    'post_author'  => 1,
                    'post_type'  => 'listing',
                ] );
                
                // Set featured image if available
                if (!empty($firm_data['image_url_1'])) {
                    import_featured_image($firm_data['image_url_1'], $post_id);
                }

                update_post_table($post_id, $firm_data);

                // $location_name = $firm_data['country'] ?? 'Faysal';
                
                // if(isset($location_name)){
                //     $location_id = 74;
                // }
                
                // if(isset($location_id)){
                //     $result = wp_set_object_terms( $post_id, $location_id, 'service' );
                // }
                // print_r($result);
                // die();

                // $terms = array('Georgia', 'New York'); // Replace with your terms
                // $taxonomy = 'location'; // Replace with your taxonomy name
            
                // // Assign terms to the taxonomy
                // wp_set_object_terms($post_id, $terms, $taxonomy);

                if ( $post_id ) {
                    // If the post is successfully inserted, update the `firm_api_tracking` table
                    $updated = $wpdb->update(
                        $table_name,
                        ['status' => 'completed'],
                        ['frn' => $firm_data['frn']],  // Use the 'frn' column to identify the record
                        ['%s'],
                        ['%s']
                    );

                    // Check if the update failed
                    if ( $updated === false ) {
                        error_log( "Failed to update status for FRN: {$firm_data['frn']}" );
                    }
                } else {
                    error_log( "Failed to insert post for firm FRN: {$firm_data['frn']}" );
                }
            } else {
                error_log( 'API response is missing required fields: firm_name or firm_status.' );
            }
        }
    } else {
        error_log( 'API response is missing or invalid.' );
    }

}



function import_featured_image($image_url, $post_id) {
    // Include the file that defines media_sideload_image if it's not already loaded
    if (!function_exists('media_sideload_image')) {
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }

    // Use media_sideload_image to download and attach the image
    $image_id = media_sideload_image($image_url, $post_id, null, 'id');
    
    // Check for errors and set as the featured image if successful
    if (!is_wp_error($image_id)) {
        set_post_thumbnail($post_id, $image_id);
    }
}





