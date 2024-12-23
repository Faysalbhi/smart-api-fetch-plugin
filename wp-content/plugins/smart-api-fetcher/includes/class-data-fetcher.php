<?php

class SmartDataFetcher_DataFetcher {

    public static function fetch_api_data() {
        $response = wp_remote_get( 'http://sdc.smartwebsource.net/api/v1/firm-activities?token=HDZ5HTKE9jiBJURKjQZsLnmSeAkJFadQ' );

        if ( is_wp_error( $response ) ) {
            error_log( 'API fetch failed: ' . $response->get_error_message() );
            return [];
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( ! is_array( $data ) ) {
            error_log( 'Invalid API response' );
            return [];
        }

        return $data;
    }

    public static function insert_into_firm_api_tracking_table( $data ) {
        foreach ( $data as $item ) {
            // Check for duplicates using metadata.
            $existing_post = get_posts( [
                'meta_key'   => '_api_source_id',
                'meta_value' => $item['id'],
                'post_type'  => 'post',
            ] );

            if ( $existing_post ) {
                continue; // Skip if duplicate exists.
            }

            // Insert post.
            $post_data = [
                'post_title'   => sanitize_text_field( $item['title'] ),
                'post_content' => wp_kses_post( $item['content'] ),
                'post_status'  => 'publish',
                'post_author'  => 1,
                'post_type'    => 'post',
            ];

            $post_id = wp_insert_post( $post_data );

            if ( $post_id ) {
                update_post_meta( $post_id, '_api_source_id', sanitize_text_field( $item['id'] ) );
            }
        }
    }
}
