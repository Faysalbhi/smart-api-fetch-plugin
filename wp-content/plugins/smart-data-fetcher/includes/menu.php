<?php


add_action('plugins_loaded', 'sdf_plugin_init');

function sdf_plugin_init() {
    add_action('admin_menu', 'sdf_admin_menu');
}

function sdf_admin_menu() {
    // Main menu (Uses 'sdf-fetch' as menu_slug to avoid duplicate submenu)
    add_menu_page(
        'Firms Management', // Page title
        'SDF Control', // Menu title
        'edit_posts', // Capability
        'sdf-fetch', // Menu slug (Matches first submenu)
        'sync_firms_from_api', // Default page callback function
        'dashicons-admin-multisite', // Icon
        4 // Position
    );

    // Submenu 1: Fetch Firms from API (This is now the default page)
    add_submenu_page(
        'sdf-fetch',
        'Fetch Firms from API',
        'Fetch Firms from API',
        'edit_posts',
        'sdf-fetch',
        'sync_firms_from_api'
    );

    // Submenu 2: Process Fetched Firms
    add_submenu_page(
        'sdf-fetch',
        'Process Fetched Firms',
        'Process Tracking List',
        'edit_posts',
        'sdf-process',
        'process_firm_data'
    );

    // Submenu 3: Firm Tracking List
    add_submenu_page(
        'sdf-fetch',
        'Firm Tracking List',
        'Tracking List',
        'edit_posts',
        'sdf-tracking',
        'display_firm_tracking_list'
    );
}


function sync_firms_from_api() {
    echo "<div class='wrap'><h1>Fetch Firms from API</h1><p>Data is being fetched from the API...</p></div>";
    sdf_fetch_data_from_api();
}

function process_firm_data() {
    $processed_count = sdf_process_data_and_create_posts();
    echo "<div class='wrap'><h1>Process Fetched Firms</h1><p>{$processed_count} firms processed from the tracking table.</p></div>";
}

function display_firm_tracking_list() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'firm_api_tracking';

    // Pagination parameters
    $items_per_page = 15;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $items_per_page;

    // Get total items count
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

    $total_pending = $wpdb->get_var(
        $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE status = %s", 'pending')
    );

    // Fetch pending firms
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT status, created_at, updated_at, frn 
             FROM $table_name 
             ORDER BY id DESC 
             LIMIT %d OFFSET %d",
            $items_per_page,
            $offset
        ),
        ARRAY_A
    );

    // Output table
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Firm Tracking List</h1>
        <hr class="wp-header-end">

        <?php if (!empty($results)) : ?>
            <h3>Total Pending Firms: <?= esc_html($total_pending) ?></h3>
            <table class="wp-list-table widefat fixed striped table-view-list">
                <thead>
                    <tr>
                        <th scope="col">FRN</th>
                        <th scope="col">Status</th>
                        <th scope="col">Created At</th>
                        <th scope="col">Updated At</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row) : ?>
                        <tr>
                            <td><?= esc_html($row['frn']) ?></td>
                            <td><?= esc_html($row['status']) ?></td>
                            <td><?= esc_html($row['created_at']) ?></td>
                            <td><?= esc_html($row['updated_at']) ?></td>
                            <td><a href="#"></a>Reprocess</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php
            // Pagination
            $total_pages = ceil($total_items / $items_per_page);
            if ($total_pages > 1) {
                $page_links = paginate_links([
                    'base'      => add_query_arg('paged', '%#%'),
                    'format'    => '',
                    'current'   => $current_page,
                    'total'     => $total_pages,
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                ]);

                if ($page_links) {
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





