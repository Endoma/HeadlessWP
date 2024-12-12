<?php
function add_categories_to_pages() {
    register_taxonomy_for_object_type( 'category', 'page' );
}
add_action( 'init', 'add_categories_to_pages' );


function add_tags_to_pages() {
    register_taxonomy_for_object_type( 'post_tag', 'page' );
}
add_action( 'init', 'add_tags_to_pages');

add_theme_support( 'post-thumbnails' );

// Remove tags support from posts
function unregister_tags() {
    unregister_taxonomy_for_object_type('post_tag', 'post');
}
add_action('init', 'unregister_tags');

function register_menus() {
    register_nav_menus(
        array(
            'menu' => __( 'Menu' ),
            'footer' => __( 'Footer' ),
        )
    );
}
add_action( 'init', 'register_menus' );

function enqueue_headlesswp_script() {
    wp_enqueue_script(
        'headlesswp-admin-script',
        get_template_directory_uri() . '/headlesswp-script.js',
        array('jquery'),
        '1.0',
        true
    );
    wp_localize_script('headlesswp-admin-script', 'customApiConfig', [
        'url' => get_option('headlesswp_api_url'),
        'secret' => get_option('headlesswp_api_secret'),
    ]);
}

add_action('admin_enqueue_scripts', 'enqueue_headlesswp_script');


// Register the settings
function headlesswp_api_settings_init() {
    // Add a new settings section
    add_settings_section(
        'headlesswp_api_settings_section',
        'Headless WP API Settings',
        'headlesswp_api_settings_section_callback',
        'general'
    );

    // Register the API URL setting
    register_setting('general', 'headlesswp_api_url', [
        'type' => 'string',
        'description' => 'API URL for cache invalidation',
        'sanitize_callback' => 'esc_url',
        'show_in_rest' => true,
    ]);

    // Add the API URL field
    add_settings_field(
        'headlesswp_api_url',
        'API URL',
        'headlesswp_api_url_callback',
        'general',
        'headlesswp_api_settings_section'
    );

    // Register the secret key setting
    register_setting('general', 'headlesswp_api_secret', [
        'type' => 'string',
        'description' => 'Secret key for API',
        'sanitize_callback' => 'sanitize_text_field',
        'show_in_rest' => true,
    ]);

    // Add the secret key field
    add_settings_field(
        'headlesswp_api_secret',
        'API Secret',
        'headlesswp_api_secret_callback',
        'general',
        'headlesswp_api_settings_section'
    );

    // Register the APP Url setting
    register_setting('general', 'headlesswp_app_url', [
        'type' => 'string',
        'description' => 'The URL of the App',
        'sanitize_callback' => 'sanitize_text_field',
        'show_in_rest' => true,
    ]);

    // Add the secret key field
    add_settings_field(
        'headlesswp_app_url',
        'APP Url',
        'headlesswp_app_url_callback',
        'general',
        'headlesswp_api_settings_section'
    );
}

add_action('admin_init', 'headlesswp_api_settings_init');

// Section description callback
function headlesswp_api_settings_section_callback() {
    echo '<p>Enter the API URL and secret key for your application.</p>';
}

// API URL field callback
function headlesswp_api_url_callback() {
    $value = get_option('headlesswp_api_url', '');
    echo '<input type="url" id="headlesswp_api_url" name="headlesswp_api_url" value="' . esc_attr($value) . '" class="regular-text">';
}

// Secret key field callback
function headlesswp_api_secret_callback() {
    $value = get_option('headlesswp_api_secret', '');
    echo '<input type="text" id="headlesswp_api_secret" name="headlesswp_api_secret" value="' . esc_attr($value) . '" class="regular-text">';
}

// Secret key field callback
function headlesswp_app_url_callback() {
    $value = get_option('headlesswp_app_url', '');
    echo '<input type="text" id="headlesswp_app_url" name="headlesswp_app_url" value="' . esc_attr($value) . '" class="regular-text">';
}

function invalidate_frontend_cache_on_save($post_id, $post, $update) {
    // Skip if this is an auto-draft or revision
    if (wp_is_post_revision($post_id) || get_post_status($post_id) === 'auto-draft') {
        return;
    }

    // Your API endpoint and secret key
    $api_url = get_option('headlesswp_api_url');
    $secret_key = get_option('headlesswp_api_secret');

    // Determine the post type
    $post_type = get_post_type($post_id);

    // Build the API URL
    $url = add_query_arg([
        'secret' => $secret_key,
        'type' => $post_type,
        'id' => $post_id,
    ], $api_url);

    // Trigger the API request
    $response = wp_remote_get($url);

    // Optional: Log errors for debugging
    if (is_wp_error($response)) {
        error_log('Cache invalidation failed: ' . $response->get_error_message());
    }
}
add_action('save_post', 'invalidate_frontend_cache_on_save', 10, 3);



