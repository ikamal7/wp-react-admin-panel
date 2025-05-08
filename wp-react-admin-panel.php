<?php
/**
 * Plugin Name: WP React Admin Panel
 * Description: A React-based admin panel for WordPress customization settings
 * Version: 1.0.0
 * Author: Kamal Hosen
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

function wrp_admin_menu() {
    add_menu_page(
        'WP React Admin Panel',
        'React Admin',
        'manage_options',
        'wp-react-admin-panel',
        'wrp_admin_page',
        'dashicons-admin-generic',
        30
    );
}
add_action('admin_menu', 'wrp_admin_menu');

function wrp_admin_page() {
    echo '<div id="wp-react-admin-panel"></div>';
}

function wrp_admin_enqueue_scripts($hook) {
    if ('toplevel_page_wp-react-admin-panel' !== $hook) {
        return;
    }

    $asset_file = include(plugin_dir_path(__FILE__) . 'build/index.asset.php');

    wp_enqueue_script(
        'wp-react-admin-panel',
        plugin_dir_url(__FILE__) . 'build/index.js',
        $asset_file['dependencies'],
        $asset_file['version'],
        true
    );
 
    $css_file = plugin_dir_path(__FILE__) . 'build/index.css';
    if (file_exists($css_file)) {
        wp_enqueue_style(
            'wp-react-admin-panel',
            plugin_dir_url(__FILE__) . 'build/index.css',
            ['wp-components'],
            filemtime($css_file)
        );
    }
}
add_action('admin_enqueue_scripts', 'wrp_admin_enqueue_scripts');

function wrp_register_rest_route() {
    register_rest_route('wp/v2/settings', '/wrp_settings', array(
        array(
            'methods' => 'POST',
            'callback' => 'wrp_update_settings',
            'permission_callback' => function() {
                return current_user_can('manage_options');
            }
        ),
        array(
            'methods' => 'GET',
            'callback' => 'wrp_get_settings',
            'permission_callback' => function() {
                return current_user_can('manage_options');
            }
        )
    ));
}

function wrp_get_settings() {
    $settings = get_option('wrp_settings');
    if (!$settings) {
        $settings = array(
            'general' => array(
                'site_title' => '',
                'admin_email' => ''
            ),
            'appearance' => array(
                'admin_color' => '#ffffff',
                'menu_position' => 'left'
            ),
            'advanced' => array(
                'custom_css' => '',
                'custom_js' => ''
            )
        );
    }
    return new WP_REST_Response($settings, 200);
}
add_action('rest_api_init', 'wrp_register_rest_route');

function wrp_update_settings($request) {
    $settings = $request->get_json_params();
    $updated = update_option('wrp_settings', $settings);
    
    if ($updated) {
        return new WP_REST_Response($settings, 200);
    }
    
    return new WP_REST_Response(array('message' => 'Failed to update settings'), 500);
}

function wrp_register_settings() {
    register_setting(
        'wp_react_admin_panel',
        'wrp_settings',
        array(
            'type' => 'object',
            'default' => array(
                'general' => array(
                    'site_title' => '',
                    'admin_email' => ''
                ),
                'appearance' => array(
                    'admin_color' => '#ffffff',
                    'menu_position' => 'left'
                ),
                'advanced' => array(
                    'custom_css' => '',
                    'custom_js' => ''
                )
            ),
            'show_in_rest' => true,
        )
    );
}
add_action('init', 'wrp_register_settings');