<?php

function sdf_trigger_reprocess_specific_firm($frn, $token, $action) {
    if (!hash_equals("G8z#Xv2pKd!q", $token)) {
        return false;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'firm_api_tracking';

    if ($action == "update") {
        sdf_reprocess_specific_firm($frn);
    } else {
        // Ensure $frn is sanitized
        $frn = sanitize_text_field($frn);

        // Insert the record safely
        $wpdb->query(
            $wpdb->prepare(
                "INSERT IGNORE INTO $table_name (frn, status) VALUES (%s, %s)",
                $frn, 'pending'
            )
        );

        // Process the firm after inserting
        sdf_reprocess_specific_firm($frn);
    }

    return true;
}

function sdf_handle_reprocess_request(WP_REST_Request $request) {
    // Get request parameters
    $frn    = $request->get_param('frn');
    $token  = $request->get_param('token');
    $action = $request->get_param('action');

    // Validate required fields
    if (empty($frn) || empty($token) || empty($action)) {
        return new WP_REST_Response(['error' => 'Missing required parameters'], 400);
    }

    // Call your function
    $result = sdf_trigger_reprocess_specific_firm($frn, $token, $action);

    // Return response
    if ($result) {
        return new WP_REST_Response(['success' => 'Processing triggered successfully'], 200);
    } else {
        return new WP_REST_Response(['error' => 'Invalid token or processing failed'], 403);
    }
}


function sdf_register_custom_api_routes() {
    register_rest_route('sdf/v1', '/trigger-reprocess', [
        'methods'  => 'POST',
        'callback' => 'sdf_handle_reprocess_request',
        'permission_callback' => '__return_true',
    ]);
}
add_action('rest_api_init', 'sdf_register_custom_api_routes');