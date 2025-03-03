<?php
/*
Plugin Name: Smart Data Fetcher
Description: Fetch data from an API and process it to store in custom and default WordPress tables.
Version: 1.1
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
// require_once SMART_DATA_FETCHER_PATH . 'includes/fetch-latitude-longitude.php';
require_once SMART_DATA_FETCHER_PATH . 'includes/menu.php';
require_once SMART_DATA_FETCHER_PATH . 'includes/update-post-table.php';
require_once SMART_DATA_FETCHER_PATH . 'includes/deactivation.php';
require_once SMART_DATA_FETCHER_PATH . 'includes/ajax-handlers.php';
require_once SMART_DATA_FETCHER_PATH . 'includes/handle-reprocess-request.php';


register_activation_hook(__FILE__, 'sdf_activate');
register_deactivation_hook( __FILE__, 'sdf_deactivate' );


// Fetch data from the API (http://sdc.smartwebsource.net/) and insert into firm_api_tracking table
add_action( 'sdf_api_fetch_cron', 'sdf_fetch_data_from_api' );
function sdf_fetch_data_from_api() {
    global $wpdb, $firm_activity_api;
    $table_name = $wpdb->prefix . 'firm_api_tracking';
    $args = [
        'timeout' => 30,
    ];
    $response = wp_remote_get( $firm_activity_api, $args );
    
    if ( is_wp_error( $response ) ) {
        return wp_send_json_error(array('message' => 'Error fetching data: ' . $response->get_error_message()));
    }

    $response_code = wp_remote_retrieve_response_code( $response );
    if ( $response_code !== 200 ) {
        return wp_send_json_error(array('message' => 'Error fetching data: HTTP Response Code: ' . $response->get_error_message()));
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( ! isset( $body['data'] ) || ! is_array( $body['data'] ) ) {
        return wp_send_json_error(array('message' => 'Unexpected API response structure.'));
    }

    $firms = [];
    $firms = array_merge($firms, array_column($body['data'], 'frn'));

    $total_pages = isset( $body['meta']['total'], $body['meta']['per_page'] ) 
        ? ceil( $body['meta']['total'] / $body['meta']['per_page'] ) 
        : 1;
        // return wp_send_json_error(array('message' => $total_pages));
    // Fetch remaining pages
    $next_url = isset( $body['links']['next'] ) ? $body['links']['next'] : null;
    for ( $page = 2; $page <= $total_pages; $page++ ) {
       
        if ( empty( $next_url ) ) {
            break;
        }

        if($page == 25){
            break;
        }

        $per_page_response = wp_remote_get( $next_url, $args );
        if ( is_wp_error( $per_page_response ) ) {
            continue;
        }

        $response_code = wp_remote_retrieve_response_code( $per_page_response );
        if ( $response_code !== 200 ) {
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

    return count($firms); // Return the number of firms fetched
}



// Process the data from the `firm_api_tracking` table to post table.
add_action( 'sdf_process_fetched_data_cron', 'sdf_process_data_and_create_posts' );

function sdf_process_data_and_create_posts() {
    global $wpdb, $single_firm_activity_api, $geocoding;
    $table_name = $wpdb->prefix . 'firm_api_tracking';

    // Fetch pending records (limit to 10)
    $entries = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'pending' LIMIT 10");
    
    if (!$entries) {
        return wp_send_json_error(['message' => "No pending firms found."]);
    }

    $processed = 0;

    foreach ($entries as $entry) {
        $api_url = $single_firm_activity_api . $entry->frn;
        $response = wp_remote_get($api_url);
        
        if (is_wp_error($response)) {
            error_log('Error fetching firm data: ' . $response->get_error_message());
            continue; // Skip this entry
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            error_log("Error fetching firm data: HTTP Response Code $response_code");
            continue; // Skip this entry
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        if (!isset($data['data']) || !is_array($data['data']) || empty($data['data'])) {
            error_log("API response has no valid data for FRN: " . $entry->frn);
            continue; // Skip this entry
        }

        foreach ($data['data'] as $firm_data) {
            if (isset($firm_data['firm_name'])) {
                // Insert new post
                $post_id = wp_insert_post([
                    'post_title'   => sanitize_text_field($firm_data['firm_name']),
                    'post_content' => wp_kses_post($firm_data['description']),
                    'post_status'  => 'publish',
                    'post_author'  => 1,
                    'post_type'    => 'listing',
                ]);

                $image_urls = [];

                // Check and add image URLs if they exist
                if (!empty($firm_data['image_url_2'])) {
                    $image_urls[] = $firm_data['image_url_2'];
                }
                if (!empty($firm_data['image_url_1'])) {
                    $image_urls[] = $firm_data['image_url_1'];
                    import_featured_image($firm_data['image_url_1'], $post_id);
                }

                // Call the function only if there are valid image URLs
                if (!empty($image_urls)) {
                    add_gallery_images_to_post($post_id, $image_urls);
                }

                update_post_table($post_id, $firm_data);
                sync_category($post_id, $firm_data);

                if (isset($firm_data['city'])) {
                    sync_location($post_id, $firm_data['city']);
                }

                if ($post_id) {
                    $updated = $wpdb->update(
                        $table_name,
                        ['status' => 'completed', 'post_id' => $post_id],
                        ['frn' => $firm_data['frn']],
                        ['%s', '%d'],
                        ['%s']
                    );

                    if ($updated === false) {
                        error_log("Database update failed for FRN: " . $firm_data['frn']);
                    } else {
                        ++$processed;
                    }
                }
            }
            break;
        }
    }

    return $processed;
}


function sdf_reprocess_specific_firm($frn){
    global $wpdb, $single_firm_activity_api, $geocoding;
    $table_name = $wpdb->prefix . 'firm_api_tracking';

    // Construct API URL
    $api_url = $single_firm_activity_api . $frn;
        
    // Fetch data from the API
    $response = wp_remote_get( $api_url );
    if ( is_wp_error( $response ) ) {
        return wp_send_json_error(array('message' => 'Error fetching firm data: ' .$response->get_error_message()));
    }

    $response_code = wp_remote_retrieve_response_code( $response );
    if ( $response_code !== 200 ) {
        error_log( "Error fetching firm data: HTTP Response Code $response_code" );
        return wp_send_json_error(array('message' => "Error fetching firm data: HTTP Response Code $response_code"));
    }

    $data = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( isset( $data['data'] ) && is_array( $data['data'] ) ) {
        foreach ( $data['data'] as $firm_data ) {
            // Check if 'firm_name' and 'firm_status' are available in the response
            if ( isset( $firm_data['firm_name']) ) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'firm_api_tracking';

                // Securely retrieve post_id
                $post_id = $wpdb->get_var(
                    $wpdb->prepare("SELECT post_id FROM $table_name WHERE frn = %s", $frn)
                );

                if ($post_id) {
                    // If post_id exists, update the post
                    $post_data = [
                        'ID'           => $post_id,
                        'post_title'   => sanitize_text_field($firm_data['firm_name']),
                        'post_content' => wp_kses_post($firm_data['description']),
                    ];
                    wp_update_post($post_data);
                } else {
                    // If post_id doesn't exist, create a new post
                    $post_id = wp_insert_post([
                        'post_title'   => sanitize_text_field($firm_data['firm_name']),
                        'post_content' => wp_kses_post($firm_data['description']),
                        'post_status'  => 'publish',
                        'post_author'  => 1,
                        'post_type'    => 'listing',
                    ]);

                }


                $image_urls = [];

                // Check and add image URLs if they exist
                if (!empty($firm_data['image_url_2'])) {
                    $image_urls[] = $firm_data['image_url_2'];
                }
                if (!empty($firm_data['image_url_1'])) {
                    $image_urls[] = $firm_data['image_url_1'];
                    import_featured_image($firm_data['image_url_1'], $post_id);
                }

                // Call the function only if there are valid image URLs
                if (!empty($image_urls)) {
                    add_gallery_images_to_post($post_id, $image_urls);
                }
                
                // Insert into the post table
                update_post_table($post_id, $firm_data);

                // Sync category
                sync_category($post_id, $firm_data);

                // Sync location
                if (isset($firm_data['city'])) {
                    sync_location($post_id, $firm_data['city']);
                }
               
                if ( $post_id ) {
                    // If the post is successfully inserted, update the `firm_api_tracking` table
                    $updated = $wpdb->update(
                        $table_name,
                        [
                            'status' => 'completed', 
                            'post_id' => $post_id
                        ], 
                        ['frn' => $frn],
                        ['%s','%d' ],
                        ['%s']
                    );

                    if ($updated !== false) {
                        return $post_id;
                    }
                }
            }
        }
    }

    return false;
}



function import_featured_image($image_url, $post_id) {
    // Include necessary WordPress files if not already loaded
    if (!function_exists('media_sideload_image')) {
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }

    // Check if the post already has a featured image
    $old_image_id = get_post_thumbnail_id($post_id);

    // If a featured image exists, delete it along with the attachment file
    if (!empty($old_image_id)) {
        wp_delete_attachment($old_image_id, true); // true = delete files as well
    }

    // Download and attach the new image
    $image_id = media_sideload_image($image_url, $post_id, null, 'id');

    // If no error, set it as the new featured image
    if (!is_wp_error($image_id)) {
        set_post_thumbnail($post_id, $image_id);
    } else {
        error_log("Failed to import image: " . $image_url);
    }
}

function add_gallery_images_to_post($post_id, $image_urls) {
    // Retrieve existing gallery images
    $existing_gallery = get_post_meta($post_id, 'gallery_image_ids', true);

    if (!empty($existing_gallery)) {
        $existing_image_ids = explode(',', $existing_gallery);
        
        // Delete existing images from media library
        foreach ($existing_image_ids as $image_id) {
            wp_delete_attachment((int) $image_id, true);
        }
    }

    // Now, add new images
    $image_ids = [];
    
    foreach ($image_urls as $image_url) {
        $image_id = media_sideload_image($image_url, $post_id, null, 'id');

        if (!is_wp_error($image_id)) {
            $image_ids[] = (int) $image_id;
        }
    }

    if (!empty($image_ids)) {
        // Save new images as a comma-separated string
        update_post_meta($post_id, 'gallery_image_ids', implode(',', $image_ids));
    }
}





function sync_category($post_id, $firm_data) {
    // Define the mapping of field names to category slugs
    $category_map = [
        'insurance' => 'Insurance',
        'mortgages_home_finance' => 'Mortgages',
        'consumer_credit' => 'Consumer credit',
        'investments' => 'Investments',
        'pensions' => 'Pensions',
        'other_activities' => 'Other Services',
    ];

    // Initialize the category_id array
    $category_id = [];

    // Iterate over the mapping and add category IDs if the corresponding field is true
    foreach ($category_map as $field => $category_name) {
        if (!empty($firm_data[$field]) && $firm_data[$field] == 1) {
            $category = term_exists($category_name, 'listing-category'); // Ensure taxonomy is specified
            if ($category && !empty($category['term_id'])) {
                $category_id[] = $category['term_id']; // Extract only term_id
            }
        }
    }

    // If no categories were found, default to "Other Services"
    if (empty($category_id)) {
        $default_category = term_exists('Other Services', 'listing-category');
        if ($default_category && !empty($default_category['term_id'])) {
            $category_id[] = $default_category['term_id'];
        }
    }

    // Assign categories only if valid term IDs are found
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

// Enqueue admin script for AJAX functionality
function sdf_enqueue_admin_scripts() {
    // Ensure the script is loaded only on the correct pages
    if (isset($_GET['page']) && ($_GET['page'] === 'sdf-fetch' || $_GET['page'] === 'sdf-process' || $_GET['page'] === 'sdf-tracking')) {
        wp_enqueue_script('sdf-fetch-js', plugin_dir_url(__FILE__) . 'includes/js/sdf-ajax-fetch.js', array('jquery'), null, true);

        // Localize the script with necessary variables for AJAX
        wp_localize_script('sdf-fetch-js', 'sdf_ajax_obj', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('sdf_fetch_nonce'),
        ));
    }
}
add_action('admin_enqueue_scripts', 'sdf_enqueue_admin_scripts');

