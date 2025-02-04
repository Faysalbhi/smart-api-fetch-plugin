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
define( 'SMART_DATA_FETCHER_PATH', plugin_dir_path( __FILE__ ) );

// Include necessary files.
require_once SMART_DATA_FETCHER_PATH . 'includes/global-variables.php';
require_once SMART_DATA_FETCHER_PATH . 'includes/activation.php';
require_once SMART_DATA_FETCHER_PATH . 'includes/fetch-latitude-longitude.php';
require_once SMART_DATA_FETCHER_PATH . 'includes/menu.php';
require_once SMART_DATA_FETCHER_PATH . 'includes/update-post-table.php';
require_once SMART_DATA_FETCHER_PATH . 'includes/deactivation.php';


register_activation_hook(__FILE__, 'sdf_activate');
register_deactivation_hook( __FILE__, 'sdf_deactivate' );


// Fetch data from the API (http://sdc.smartwebsource.net/) and insert into firm_api_tracking table
add_action( 'sdf_api_fetch_cron', 'sdf_fetch_data_from_api' );
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
    $next_url = isset( $body['links']['next'] ) ? $body['links']['next'] : null;
    for ( $page = 2; $page <= $total_pages; $page++ ) {
       
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
        $next_url = isset( $page_body['links']['next'] ) ? $page_body['links']['next'] : null;
    }


    // Insert new firms into the firm_api_tracking
    $values = [];
    foreach ( $firms as $frn ) {
        $values[] = $wpdb->prepare( "(%s, %s)", sanitize_text_field( $frn ), 'pending' );
    }


    if ( ! empty( $values ) ) {
        $sql = "INSERT IGNORE INTO $table_name (frn, status) VALUES " . implode( ', ', $values );
        $wpdb->query( $sql );
    }
}


// Process the data from the `firm_api_tracking` table to post table.
add_action( 'sdf_process_fetched_data_cron', 'sdf_process_data_and_create_posts' );
function sdf_process_data_and_create_posts() {
    global $wpdb, $single_firm_activity_api, $geocoding;
    $table_name = $wpdb->prefix . 'firm_api_tracking';

    // Fetch one pending record.
    $entries = $wpdb->get_results( "SELECT * FROM $table_name WHERE status = 'pending' LIMIT 10" );

    if ( ! $entries ) {
        // No pending entries, exit.
        return;
    }
    $processed = 0;

    foreach($entries as $entry){
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
                    
                    //Insert Into Post Table
                    update_post_table($post_id, $firm_data);

                    //Sync Category
                    sync_category($post_id, $firm_data);

                    //Sync Location
                    if(isset($geocoding['city_name'])){
                        sync_location($post_id, $geocoding['city_name']);
                    }


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
                        }else{
                            ++$processed;
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

    return $processed;
        
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

function sync_category($post_id, $firm_data){
       // Define the mapping of field names to category IDs
       $category_map = [
        'insurance' => term_exists('Insurance'),
        'mortgages_home_finance' => term_exists('Mortgages'),
        'consumer_credit' => term_exists('Consumer credit'),
        'investments' => term_exists('Investments'),
        'pensions' => term_exists('Pensions'),
        'other_activities' => term_exists('Other Services'),
    ];

    // Initialize the category_id array
    $category_id = [];

    // Iterate over the mapping and add category IDs if the corresponding field is true
    foreach ($category_map as $field => $category) {
        if ($firm_data[$field] == 1) {
            $category_id[] = $category;
        }else{
            $category_id[0] = term_exists('Other Services');

        }
    }
    
    // If there are any categories, set them
    if (!empty($category_id)) {
       wp_set_post_terms($post_id, $category_id, 'listing-category');
    }
}


function sync_location($post_id, $location) {
    // Check if the taxonomy term exists
    $term = term_exists($location, 'location');

    if (!$term) {
        // If the term doesn't exist, create it
        $term_data = wp_insert_term(
            $location,
            'location' 
        );

        if (is_wp_error($term_data)) {
            return $term_data;
        }

        $location_id = $term_data['term_id'];
    } else {
        $location_id = $term['term_id'];
    }

    // Assign the term to the post
    wp_set_post_terms($post_id, [$location_id], 'location');
}






