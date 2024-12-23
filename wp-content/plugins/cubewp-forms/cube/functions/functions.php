<?php

if (! function_exists('cwp_leads_create_database')) {
    function cwp_leads_create_database()
    {
        global $wpdb;
        $charset_collate       = $wpdb->get_charset_collate();
        $wpdb->query("CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "cwp_forms_leads` (
            `id` bigint(20) unsigned NOT NULL auto_increment,
            `lead_id` longtext NOT NULL,
            `user_id` bigint(20) DEFAULT NULL,
            `form_id` bigint(20) NOT NULL DEFAULT '0',
            `form_name` longtext NOT NULL,
            `post_author` bigint(20) UNSIGNED NOT NULL,
            `single_post` bigint(20) DEFAULT NULL,
            `fields` longtext NOT NULL,
            `dete_time` longtext NOT NULL,
            PRIMARY KEY (`id`)
        ) $charset_collate");
    }
    add_action('admin_init', 'cwp_leads_create_database', 20);
}

if (! function_exists('cwp_insert_leads')) {
    function cwp_insert_leads($data = array())
    {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . "cwp_forms_leads", array(
            'lead_id'       => isset($data['lead_id']) ? $data['lead_id'] : '',
            'user_id'       => isset($data['user_id']) ? $data['user_id'] : '',
            'form_id'       => isset($data['form_id']) ? $data['form_id'] : '',
            'form_name'     => isset($data['form_name']) ? $data['form_name'] : '',
            'post_author'   => isset($data['post_author']) ? $data['post_author'] : '',
            'single_post'   => isset($data['single_post']) ? $data['single_post'] : '',
            'fields'        => isset($data['fields']) ? serialize($data['fields']) : array(),
            'dete_time'        => isset($data['dete_time']) ? $data['dete_time'] : ''
        ), array('%s', '%d', '%d', '%s', '%d', '%s', '%s', '%d'));
    }
}

if (! function_exists('cwp_forms_all_leads')) {
    function cwp_forms_all_leads()
    {
        global $wpdb;
        $leads     = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cwp_forms_leads", ARRAY_A);
        if (!empty($leads) && count($leads) > 0) {
            return $leads;
        }
        return array();
    }
}

if (! function_exists('cwp_forms_all_leads_by_lead_id')) {
    function cwp_forms_all_leads_by_lead_id($leadid = '')
    {
        global $wpdb;
        $leads     = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cwp_forms_leads WHERE lead_id='{$leadid}'", ARRAY_A);
        if (!empty($leads) && count($leads) > 0) {
            return $leads;
        }
        return array();
    }
}

if (! function_exists('cwp_forms_all_leads_by_id')) {
    function cwp_forms_all_leads_by_id($id = '')
    {
        global $wpdb;
        $leads     = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cwp_forms_leads WHERE id={$id}", ARRAY_A);
        if (!empty($leads) && count($leads) > 0) {
            return $leads;
        }
        return array();
    }
}
if (! function_exists('cwp_forms_all_leads_by_post_author')) {
    function cwp_forms_all_leads_by_post_author($id = '')
    {
        global $wpdb;
        $leads     = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cwp_forms_leads WHERE post_author={$id}", ARRAY_A);
        if (!empty($leads) && count($leads) > 0) {
            return $leads;
        }
        return array();
    }
}
/**
 * Method cwp_dashboard_leads_tab
 *
 * @return string html
 * @since  1.0.0
 */
if (! function_exists('cwp_dashboard_leads_tab')) {
    function cwp_dashboard_leads_tab()
    {
        return CubeWp_Forms_Dashboard::cwp_leads();
    }
}

/**
 * Method cwp_remove_lead_from_post
 *
 * @param int $leadid
 *
 * @return void
 * @since  1.0.0
 */
if (! function_exists('cwp_remove_lead_from_post')) {
    function cwp_remove_lead_from_post($leadid = 0)
    {
        if ($leadid == 0)
            return;

        $form_id = cwp_form_id_by_lead_id($leadid);
        $form_data_id = get_post_meta($form_id, '_cwp_custom_form_data_id', true);
        $form_data_id = json_decode($form_data_id);
        unset($form_data_id->$leadid);
        update_post_meta($form_id, '_cwp_custom_form_data_id', json_encode($form_data_id));
    }
}

/**
 * Method cwp_remove_lead_from_author
 *
 * @param int $leadid
 *
 * @return void
 * @since  1.0.0
 */
if (! function_exists('cwp_remove_lead_from_author')) {
    function cwp_remove_lead_from_author($leadid = 0)
    {
        if ($leadid == 0)
            return;

        $author_id = cwp_author_id_by_lead_id($leadid);
        $user_form_data_id = json_decode(get_user_meta($author_id, '_cwp_custom_form_data_id', true));
        unset($user_form_data_id->$leadid);
        update_user_meta($author_id, '_cwp_custom_form_data_id', json_encode($user_form_data_id));
    }
}

/**
 * Method cwp_remove_lead
 *
 * @param int $leadid
 *
 * @return void
 * @since  1.0.0
 */
if (! function_exists('cwp_remove_lead')) {
    function cwp_remove_lead($leadid = 0)
    {
        if ($leadid == 0)
            return;
        global $wpdb;
        cwp_remove_lead_from_author($leadid);
        cwp_remove_lead_from_post($leadid);
        $wpdb->delete($wpdb->prefix . 'cwp_forms_leads', array('lead_id' => $leadid), array('%s'));
    }
}

/**
 * Method cwp_form_id_by_lead_id
 *
 * @param int $leadid
 *
 * @return int
 * @since  1.0.0
 */
if (! function_exists('cwp_form_id_by_lead_id')) {
    function cwp_form_id_by_lead_id($leadid = 0)
    {
        if ($leadid == 0)
            return;

        $form_data = cwp_forms_all_leads_by_lead_id($leadid);
        if (isset($form_data['form_id'])) {
            return $form_data['form_id'];
        }
    }
}

/**
 * Method cwp_post_id_by_lead_id
 *
 * @param int $leadid
 *
 * @return int
 * @since  1.0.0
 */
if (! function_exists('cwp_post_id_by_lead_id')) {
    function cwp_post_id_by_lead_id($leadid = 0)
    {
        if ($leadid == 0)
            return;

        $form_data = cwp_forms_all_leads_by_lead_id($leadid);
        if (isset($form_data['single_post'])) {
            return $form_data['single_post'];
        }
    }
}

/**
 * Method cwp_author_id_by_lead_id
 *
 * @param int $leadid
 *
 * @return int
 * @since  1.0.0
 */
if (! function_exists('cwp_author_id_by_lead_id')) {
    function cwp_author_id_by_lead_id($leadid = 0)
    {
        if ($leadid == 0)
            return;

        $form_data = cwp_forms_all_leads_by_lead_id($leadid);
        if (isset($form_data['post_author'])) {
            return $form_data['post_author'];
        }
    }
}

/**
 * Method cwp_lead_date_by_lead_id
 *
 * @param int $leadid
 *
 * @return int
 * @since  1.0.0
 */
if (! function_exists('cwp_lead_date_by_lead_id')) {
    function cwp_lead_date_by_lead_id($leadid = 0)
    {
        if ($leadid == 0)
            return;

        $form_data = cwp_forms_all_leads_by_lead_id($leadid);
        if (isset($form_data['dete_time'])) {
            return $form_data['dete_time'];
        }
    }
}
/**
 * Method cwp_upload_custom_form_gallery_images
 *
 * @param int $key
 * @param array $val
 * @param array $val
 * @param int $post_id
 *
 * @return array
 * @since  1.0.0
 */
if (! function_exists('cwp_upload_custom_form_gallery_images')) {
    function cwp_upload_custom_form_gallery_images($key = '', $val = array(), $files = array(), $post_id = 0)
    {

        $attachment_ids = array();
        if (isset($val) && !empty($val) && is_array($val)) {
            foreach ($val as $file_id) {
                if (isset($files['cwp_custom_form']['name']['fields'][$key][$file_id])) {
                    $file_names = $files['cwp_custom_form']['name']['fields'][$key][$file_id];
                    foreach ($file_names as $file_key => $file_name) {
                        if ($file_name != '') {
                            $file = array(
                                'name'     => $files['cwp_custom_form']['name']['fields'][$key][$file_id][$file_key],
                                'type'     => $files['cwp_custom_form']['type']['fields'][$key][$file_id][$file_key],
                                'tmp_name' => $files['cwp_custom_form']['tmp_name']['fields'][$key][$file_id][$file_key],
                                'error'    => $files['cwp_custom_form']['error']['fields'][$key][$file_id][$file_key],
                                'size'     => $files['cwp_custom_form']['size']['fields'][$key][$file_id][$file_key]
                            );
                            $attachment_ids[] = cwp_handle_attachment($file, $post_id);
                        }
                    }
                } else {
                    $attachment_ids[] = $file_id;
                }
            }
        }
        return $attachment_ids;
    }
}

/**
 * Method cwp_upload_custom_form_repeating_gallery_images
 *
 * @param int $key
 * @param int $_key
 * @param int $field_key
 * @param array $val
 * @param array $files
 * @param int $post_id
 *
 * @return array
 * @since  1.0.0
 */
if (! function_exists('cwp_upload_custom_form_repeating_gallery_images')) {
    function cwp_upload_custom_form_repeating_gallery_images($key = '', $_key = '', $field_key = '', $val = array(), $files = array(), $post_id = 0)
    {

        $attachment_ids = array();
        if (isset($val) && !empty($val) && is_array($val)) {
            foreach ($val as $file_id) {
                if (isset($files['cwp_custom_form']['name']['fields'][$key][$_key][$field_key][$file_id])) {
                    $file_names = $files['cwp_custom_form']['name']['fields'][$key][$_key][$field_key][$file_id];
                    foreach ($file_names as $file_key => $file_name) {
                        if ($file_name != '') {
                            $file = array(
                                'name'     => $files['cwp_custom_form']['name']['fields'][$key][$_key][$field_key][$file_id][$file_key],
                                'type'     => $files['cwp_custom_form']['type']['fields'][$key][$_key][$field_key][$file_id][$file_key],
                                'tmp_name' => $files['cwp_custom_form']['tmp_name']['fields'][$key][$_key][$field_key][$file_id][$file_key],
                                'error'    => $files['cwp_custom_form']['error']['fields'][$key][$_key][$field_key][$file_id][$file_key],
                                'size'     => $files['cwp_custom_form']['size']['fields'][$key][$_key][$field_key][$file_id][$file_key]
                            );
                            $attachment_ids[] = cwp_handle_attachment($file, $post_id);
                        }
                    }
                } else {
                    $attachment_ids[] = $file_id;
                }
            }
        }
        return $attachment_ids;
    }
}

/**
 * Method cwp_upload_custom_form_file
 *
 * @param int $key
 * @param array $val
 * @param array $files
 * @param int $post_id
 *
 * @return array
 * @since  1.0.0
 */
if (! function_exists('cwp_upload_custom_form_file')) {
    function cwp_upload_custom_form_file($key = '', $val = array(), $files = array(), $post_id = 0)
    {

        $attachment_id = '';
        if (isset($files['cwp_custom_form']['name']['fields'][$key]) && $files['cwp_custom_form']['name']['fields'][$key] != '') {
            $file = array(
                'name'     => $files['cwp_custom_form']['name']['fields'][$key],
                'type'     => $files['cwp_custom_form']['type']['fields'][$key],
                'tmp_name' => $files['cwp_custom_form']['tmp_name']['fields'][$key],
                'error'    => $files['cwp_custom_form']['error']['fields'][$key],
                'size'     => $files['cwp_custom_form']['size']['fields'][$key]
            );
            $attachment_id = cwp_handle_attachment($file, $post_id);
        } else if (isset($val) && $val != 0) {
            $attachment_id = $val;
        }
        return $attachment_id;
    }
}
add_filter('cubewp/custom_fields/custom_forms/fields', 'custom_form_fields_update', 9, 2);

/**
 * Method custom_form_fields_update
 *
 * @param array $fields_settings 
 * @param array $fieldData 
 *
 * @return array
 * @since  1.0.0
 */
function custom_form_fields_update($fields_settings = array(), $fieldData = array())
{
    unset($fields_settings['field_rest_api']);
    unset($fields_settings['field_admin_size']);
    unset($fields_settings['field_relationship']);
    unset($fields_settings['field_map_use']);
    return $fields_settings;
}

if (! function_exists('cubewp_add_recaptcha_settings_sections')) {
    function cubewp_add_recaptcha_settings_sections($sections)
    {
        $sections['recaptcha-settings'] = array(
            'title'  => __('reCAPTCHA Config', 'cubewp'),
            'id'     => 'recaptcha-settings',
            'icon'   => 'dashicons-shield',
            'fields' => array(
                array(
                    'id'      => 'recaptcha',
                    'type'    => 'switch',
                    'title'   => __('Enable reCAPTCHA', 'cubewp-framework'),
                    'default' => '0',
                    'desc'    => __('Enable if you reCAPTCHA on your CubeWP forms.', 'cubewp-framework'),
                ),
                array(
                    'id'       => 'recaptcha_type',
                    'type'     => 'select',
                    'title'    => __('Select reCAPTCHA Type', 'cubewp-framework'),
                    'subtitle' => '',
                    'desc'     => __('Select the type of reCAPTCHA you want to use on to your CubeWP forms.', 'cubewp-framework'),
                    'options'  => array(
                        'google_v2' => __('Google reCAPTCHA v2 Checkbox', 'cubewp-framework'),
                    ),
                    'default'  => 'google_v2',
                    'required' => array(
                        array('recaptcha', 'equals', '1')
                    )
                ),
                array(
                    'id'       => 'google_recaptcha_sitekey',
                    'type'     => 'text',
                    'title'    => __('Site Key', 'cubewp-framework'),
                    'default'  => '',
                    'desc'     => __('Please enter google reCAPTCHA v2 Or v3 site key here.', 'cubewp-framework'),
                    'required' => array(
                        array('recaptcha', 'equals', '1')
                    )
                ),
                array(
                    'id'       => 'google_recaptcha_secretkey',
                    'type'     => 'text',
                    'title'    => __('Secret Key', 'cubewp-framework'),
                    'default'  => '',
                    'desc'     => __('Please enter google reCAPTCHA v2 Or v3 secret key here.', 'cubewp-framework'),
                    'required' => array(
                        array('recaptcha', 'equals', '1')
                    )
                ),
            )
        );
        $sections['cubewp_forms_mailchimp'] = array(
            'title'  => __('Mailchimp', 'cubewp-framework'),
            'id'     => 'cubewp_forms_mailchimp',
            'icon'   => 'dashicons dashicons-images-alt2',
            'fields' => array(
                array(
                    'id'      => 'cubewp_forms_mailchimp',
                    'title'   => __('Mailchimp Integration', 'cubewp-forms'),
                    'desc'    => __('Enable if you want to enable mailchimp integration with CubeWP Forms.', 'cubewp-forms'),
                    'type'    => 'switch',
                    'default' => '0',
                ),
                array(
                    'id'    => 'cubewp_forms_mailchimp_key',
                    'title' => __('Mailchimp Key', 'cubewp-forms'),
                    'desc'  => __('Please enter api key you have for mailchimp account.', 'cubewp-forms'),
                    'type'  => 'text',
                    'required' => array(
                        array('cubewp_forms_mailchimp', 'equals', '1')
                    )
                ),
                array(
                    'id'    => 'cubewp_forms_mailchimp_prefix',
                    'title' => __('Mailchimp Server Prefix', 'cubewp-forms'),
                    'desc'  => __('Please enter Your Mailchimp Server Prefix  of your mailchimp account.', 'cubewp-forms'),
                    'type'  => 'text',
                    'placeholder'  => 'us21',
                    'required' => array(
                        array('cubewp_forms_mailchimp', 'equals', '1')
                    )
                )
            ),
        );
        return $sections;
    }

    add_filter('cubewp/options/sections', 'cubewp_add_recaptcha_settings_sections', 9, 1);
}


/**
 * Class method, define folder path for import files
 */
function cwp_path_for_import_cubewp_content($path)
{
    $redirect_url = $path;
    if (isset($_COOKIE['cubewp-forms-template-style'])) {
        // Get the value of the cookie
        $redirect_url = $_COOKIE['cubewp-forms-template-style'];
    }
    return $redirect_url;
}
add_filter('cubewp/import/content/path', 'cwp_path_for_import_cubewp_content');

/**
 * Class method, define folder path for import files
 */
function cwp_redirect_after_sucess($path)
{
    $redirect_url = $path;
    if (isset($_COOKIE['cubewp-forms-template-style'])) {
        // Get the value of the cookie
        $redirect_url = admin_url('admin.php?page=cubewp-form-fields');
    }
    return $redirect_url;
}
add_filter('cubewp/after/import/redirect', 'cwp_redirect_after_sucess');

function cubewp_forms_mailchimp_cubes_fields($fields, $cube)
{
    global $cwpOptions;
    if (isset($cwpOptions['cubewp_forms_mailchimp']) && $cwpOptions['cubewp_forms_mailchimp'] == '1') {
        $fields['cubewp_mailchimp_field_key'] = array(
            'class'       => 'group-field field-cubewp_mailchimp_field_key mailchimp-based',
            'name'        => 'cwp[fields][' . $cube['name'] . '][cubewp_mailchimp_field_key]',
            'label'       => esc_html__('Mailchimp Field ID', 'cubewp-forms'),
            'type'        => 'text',
            'id'                => 'cubewp_mailchimp_field_' . $cube['id'],
            'placeholder'        => 'xxxxxxxxxx',
            'value'       => $cube['cubewp_mailchimp_field_key'] ?? '',
        );
    }
    return $fields;
}
add_filter('cubewp/custom_fields/custom_forms/fields', 'cubewp_forms_mailchimp_cubes_fields', 11, 2);

add_action('cubewp_forms_mailchimp_errors',  'cubewp_forms_display_errors_page');
function cubewp_forms_display_errors_page()
{
?>
    <div class="wrap">
        <h2>Mailchimp API Errors</h2>
        <button class="cubewp-forms-clear-mailchimp-logs" style="float:right">Clear Log</button>
        <table class="widefat">
            <thead>
                <tr>
                    <th>Error Message</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                global $wpdb;
                $table_name = $wpdb->prefix . 'cubewp_mailchimp_errors';

                // Check if the table exists
                $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");

                if ($table_exists) {
                    // Table exists, proceed with the query
                    $errors = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC", ARRAY_A);

                    // Output the results
                    foreach ($errors as $error) {
                        echo '<tr>';
                        echo '<td>' . esc_html($error['error_message']) . '</td>';
                        echo '<td>' . esc_html($error['error_date']) . '</td>';
                        echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}

add_action('wp_ajax_clear_mailchimp_logs', 'clear_mailchimp_logs');
function clear_mailchimp_logs()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cubewp_mailchimp_errors';
    $wpdb->query("TRUNCATE TABLE $table_name");
    wp_die();
}

add_action('wp_ajax_get_email_template_data', 'get_email_template_data_callback');
function get_email_template_data_callback()
{

    $fields = array();
    // Get the selected option values from the AJAX request
    $selectedOptions = isset($_POST['selectedOptions']) ? $_POST['selectedOptions'] : '';
    if (!empty($selectedOptions)) {
        $groupFields = explode(',', get_post_meta($selectedOptions, '_cwp_group_fields', true));
        $fields = array_merge($fields, $groupFields);
    }
    $custom_fields =  CWP()->get_custom_fields('custom_forms');

    if (!empty($fields)) {
        echo '<div class="cubewp-email-template-shortcode-ajax">';
        foreach ($fields as $field) {
            if (isset($custom_fields[$field]['label']) && !empty($field)) {
    ?>
                <div class="cubewp-email-template-shortcode">
                    <span class="cubewp-email-template-shortcode-label"><?php echo esc_attr($custom_fields[$field]['label']); ?></span>
                    <span class="cubewp-email-template-shortcode-value">{<?php echo esc_attr($field); ?>}</span>
                </div>
<?php
            }
        }
        echo '</div>';
    }
    // Always remember to exit after sending the response
    wp_die();
}

if (! function_exists('cubewp_forms_get_email_template')) {
    function cubewp_forms_get_email_template($recipient, $form_id)
    {
        $args = array(
            'post_type'      => CubeWp_Forms_Emails::$post_type,
            'post_status'    => 'publish',
            'fields'         => 'ids',
            'posts_per_page' => 1,
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'     => 'email_recipient',
                    'value'   => $recipient,
                    'compare' => '=='
                ),
                array(
                    'key'     => 'admin_email_post_types',
                    'value'   => $form_id,
                    'compare' => '=='
                ),
            )
        );
        $templates = get_posts($args);
        return $templates;
    }
}

if (! function_exists("CubeWp_Forms_Sanitize_Fields_Array")) {
    function CubeWp_Forms_Sanitize_Fields_Array($input, $fields_of)
    {

        $sanitize = new CubeWp_Sanitize();
        $return   = $input;
        if ($fields_of == 'custom_forms') {
            $return = $sanitize->sanitize_post_type_meta($input, $fields_of);
        }

        return $return;
    }
}
