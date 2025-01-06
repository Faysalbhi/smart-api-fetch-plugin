<?php


add_action('plugins_loaded', 'sdf_plugin_init');

function sdf_plugin_init() {
    add_action('admin_menu', 'sdf_admin_menu');
}

function sdf_admin_menu() {
    add_menu_page('Sync Firms', 'Sync Firms', 'edit_posts', 'sdf', 'sdf_main_page', 'dashicons-admin-multisite', 4);
    add_submenu_page('sdf', __('Firm Tracking List'), __('Firm Tracking List'), 'edit_posts', 'sdf-child', 'sdf_child_page');
}

function sdf_main_page() {
    // do_action( 'sdf_process_fetched_data_cron' );
    sdf_process_data_and_create_posts();
}
 
function sdf_child_page() {
    global $wpdb;

    // Table name
    $table_name = $wpdb->prefix . 'firm_api_tracking';

    // Pagination parameters
    $items_per_page = 15; // Number of rows per page
    $current_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
    $offset = ( $current_page - 1 ) * $items_per_page;

    // Get the total number of rows
    $total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );

    $total_pending = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE status = %s",
            'pending'
        )
    );

    // Retrieve rows with status = 'pending'
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT status, created_at, updated_at, frn 
            FROM $table_name 
            WHERE status = %s 
            ORDER BY id DESC 
            LIMIT %d OFFSET %d", 
            'pending', 
            $items_per_page, 
            $offset
        ), 
        ARRAY_A
    );
    
    // Start output
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Tracking Table</h1>
        <hr class="wp-header-end">

        <?php if ( !empty($results) ) : ?>
            <h3>Total Pending Firms: <?=$total_pending?></h3>
            <table class="wp-list-table widefat fixed striped table-view-list">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column">FRN</th>
                        <th scope="col" class="manage-column">Status</th>
                        <th scope="col" class="manage-column">Created At</th>
                        <th scope="col" class="manage-column">Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $results as $row ) : ?>
                        <tr>
                            <td><?php echo esc_html( $row['frn'] ); ?></td>
                            <td><?php echo esc_html( $row['status'] ); ?></td>
                            <td><?php echo esc_html( $row['created_at'] ); ?></td>
                            <td><?php echo esc_html( $row['updated_at'] ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php
            // Pagination
            $total_pages = ceil( $total_items / $items_per_page );

            if ( $total_pages > 1 ) {
                $page_links = paginate_links( array(
                    'base'      => add_query_arg( 'paged', '%#%' ),
                    'format'    => '',
                    'current'   => $current_page,
                    'total'     => $total_pages,
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                ) );

                if ( $page_links ) {
                    echo '<div class="tablenav"><div class="tablenav-pages">' . $page_links . '</div></div>';
                }
            }
            ?>

        <?php else : ?>
            <p>No data found in the tracking table.</p>
        <?php endif; ?>
    </div>
    <?php
}



