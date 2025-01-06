<?php


// Deactivation: Clear cron jobs.
function sdf_deactivate() {
    wp_clear_scheduled_hook( 'sdf_daily_fetch_cron' );
    wp_clear_scheduled_hook( 'sdf_process_fetched_data_cron' );
}

