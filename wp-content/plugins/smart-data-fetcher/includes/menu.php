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
    ?>
    <div class='wrap'>
        <h1>Fetch Firms from API</h1>
        <p>Click the button below to fetch data from the API.</p>

        <!-- Button to trigger AJAX -->
        <button class="button button-primary sdf-ajax-call-button" data-action="sdf_fetch_data" data-nonce="<?php echo wp_create_nonce('sdf_fetch_nonce'); ?>">Fetch Data</button>

        <!-- WordPress spinner element (hidden by default) -->
        <div id="sdf_fetch_data_spinner" style="display:none; 
            position: absolute; 
            top: 20%; 
            left: 50%; 
            transform: translate(-50%, -50%); 
            text-align: center;
            font-size: 50px;">
            <span class="spinner" style="background-size: 40px 40px !important; width: 40px !important; height: 40px !important;"></span> <!-- Default WordPress spinner -->
        </div>

        <!-- Status area to show feedback -->
        <p id="sdf_fetch_data_status"></p>
    </div>
    <?php
}

function process_firm_data() {
    ?>
    <div class='wrap'>
        <h1>Process Fetched Firms</h1>
        <p>Click the button below to process firms from the tracking list.</p>

        <!-- Button to trigger AJAX -->
        <button class="button button-primary sdf-ajax-call-button" data-action="sdf_process_firm_data" data-nonce="<?php echo wp_create_nonce('sdf_process_nonce'); ?>">Process Firms</button>

        <div id="sdf_process_firm_data_spinner" style="
            position: absolute; 
            top: 20%; 
            left: 50%; 
            transform: translate(-50%, -50%); 
            text-align: center;
            font-size: 50px;">
            <span class="spinner" style="background-size: 40px 40px !important; width: 40px !important; height: 40px !important;"></span> <!-- Default WordPress spinner -->
        </div>

        <!-- Status area to show feedback -->
        <p id="sdf_process_firm_data_status"></p>
    </div>
    <?php
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
            "SELECT status, created_at, updated_at, frn , post_id
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
                        <th scope="col">POST ID</th>
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
                            <td id="post_<?= esc_attr($row['frn']) ?>" ><?= esc_html($row['post_id'] ?? 'NULL') ?></td>
                            <td id="frn_<?= esc_attr($row['frn']) ?>" style="color: <?= ($row['status'] === 'completed') ? 'green' : 'orange'; ?>;">
                                <?= esc_html($row['status']) ?>
                            </td>
                            <td><?= esc_html($row['created_at']) ?></td>
                            <td><?= esc_html($row['updated_at']) ?></td>
                            <td>
                            <a href="#" class="sdf-reprocess-button" data-frn="<?= esc_attr($row['frn']) ?>" data-nonce="<?php echo wp_create_nonce('sdf_reprocess_nonce'); ?>" style="color: <?= ($row['status'] === 'completed') ? 'green' : 'orange'; ?>;">Reprocess</a>
                            </td>
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





