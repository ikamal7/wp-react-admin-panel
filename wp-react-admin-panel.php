<?php
/**
 * Plugin Name: WP React Admin Panel
 * Plugin URI: https://github.com/your-username/wp-react-admin-panel
 * Description: A React-based admin panel for WordPress customization settings
 * Version: 1.0.0
 * Requires at least: 5.8
 * Requires PHP: 7.2
 * Author: Kamal Hosen
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-react-admin-panel
 * Domain Path: /languages
 *
 * @package WP_React_Admin_Panel
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the admin menu page for the plugin.
 *
 * Creates a top-level menu item in the WordPress admin sidebar
 * with the title "React Admin" and appropriate capabilities.
 *
 * @since 1.0.0
 * @return void
 */
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

/**
 * Render the admin page content.
 *
 * Outputs the container div where the React application will be mounted.
 *
 * @since 1.0.0
 * @return void
 */
function wrp_admin_page() {
    echo '<div id="wp-react-admin-panel"></div>';
}

/**
 * Enqueue scripts and styles for the admin page.
 *
 * Loads the compiled React application and its dependencies only on the plugin's admin page.
 *
 * @since 1.0.0
 * @param string $hook The current admin page hook.
 * @return void
 */
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

/**
 * Register REST API endpoints for the plugin settings.
 *
 * Creates GET and POST endpoints to retrieve and update plugin settings.
 *
 * @since 1.0.0
 * @return void
 */
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

/**
 * Callback for the settings GET endpoint.
 *
 * Retrieves the current plugin settings from the database or returns default values.
 *
 * @since 1.0.0
 * @return WP_REST_Response The settings response object.
 */
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

/**
 * Callback for the settings POST endpoint.
 *
 * Updates the plugin settings in the database based on the request data.
 *
 * @since 1.0.0
 * @param WP_REST_Request $request The request object containing the settings data.
 * @return WP_REST_Response The response object with updated settings or error message.
 */
function wrp_update_settings($request) {
    $settings = $request->get_json_params();
    $updated = update_option('wrp_settings', $settings);
    
    if ($updated) {
        return new WP_REST_Response($settings, 200);
    }
    
    return new WP_REST_Response(array('message' => 'Failed to update settings'), 500);
}

/**
 * Register the plugin settings in WordPress.
 *
 * Defines the settings schema and makes the settings available via the REST API.
 *
 * @since 1.0.0
 * @return void
 */
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