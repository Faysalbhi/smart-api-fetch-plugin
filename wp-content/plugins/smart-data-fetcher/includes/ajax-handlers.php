<?php
// Handle the Fetch Data Action
function sdf_fetch_data() {
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'sdf_fetch_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed.' ) );
    }

    // Fetch data logic (you can call your function here)
    $data_fetched = sdf_fetch_data_from_api();
    
    if (is_int($data_fetched)) {
        wp_send_json_success( array( 'message' => "$data_fetched Data fetched successfully."));
    } else {
        wp_send_json_error( array( 'message' => $data_fetched ) );
    }
}
add_action( 'wp_ajax_sdf_fetch_data', 'sdf_fetch_data' );

// Handle the Process Firms Action
function sdf_process_firm_data() {
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'sdf_process_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed.' ) );
    }

    // Process data logic (call your function here)
    $processed_count = sdf_process_data_and_create_posts();

    if ($processed_count > 0) {
        wp_send_json_success( array( 'message' => "$processed_count firms processed successfully." ) );
    } else {
        wp_send_json_error( array( 'message' => 'No firms to process.' ) );
    }
}
add_action( 'wp_ajax_sdf_process_firm_data', 'sdf_process_firm_data' );

// Handle the ReProcess Firms Action
function sdf_reprocess_firm_data() {
    global $wpdb;

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sdf_reprocess_nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce.'));
    }

    if (!isset($_POST['frn'])) {
        wp_send_json_error(array('message' => 'FRN is missing.'));
    }

    $frn = sanitize_text_field($_POST['frn']);
    $result = sdf_reprocess_specific_firm($frn);

    if ($result) {
        // Update the status in the database
        $table_name = $wpdb->prefix . 'firm_api_tracking';
        $wpdb->update($table_name, ['status' => 'completed'], ['frn' => $frn], ['%s'], ['%s']);

        wp_send_json_success(array('message' => 'Reprocessed successfully.'));
    } else {
        wp_send_json_error(array('message' => 'Reprocessing failed.'));
    }
}
add_action('wp_ajax_sdf_reprocess_specific_firm', 'sdf_reprocess_firm_data');

