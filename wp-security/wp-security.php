<?php
/**
 * Plugin Name: WP Security
 * Description: Restrict theme & plugin editing unless admin enters a security token.
 * Version: 1.0
 * Author: Shivani Gupta
 * Text Domain: wp-sec-si
 */

if (!defined('ABSPATH')) exit; // Prevent direct access

// Define security token (Change this token manually)
define('ADMIN_SECURITY_TOKEN', 'mySecureToken123!');

// Enqueue JavaScript for popup only on the editor pages
function wpsec_enqueue_admin_token_popup_script($hook) {
    // Enqueue jQuery if it's not already loaded
    if (!wp_script_is('jquery', 'enqueued')) {
        wp_enqueue_script('jquery');
    }

    // Enqueue the custom script only on theme/plugin editor pages
    if ($hook === 'theme-editor.php' || $hook === 'plugin-editor.php') {
        wp_enqueue_script('wpsec-admin-token-popup', plugin_dir_url(__FILE__) . 'security.js', ['jquery'], null, true);
        
        // Localize script with Ajax URL and nonce
        wp_localize_script('wpsec-admin-token-popup', 'wpsec_admin_token', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wpsec_admin_token_nonce'),
            'is_token_valid' => json_encode(get_transient('wpsec_admin_token_valid') ? true : false), // Added token validity as a JS value
        ]);
    }
}
add_action('admin_enqueue_scripts', 'wpsec_enqueue_admin_token_popup_script');

// Add the Admin Token Menu
function wpsec_add_token_menu() {
    add_menu_page(
        'Security Token',             // Page title
        'Security Token',             // Menu title
        'manage_options',             // Capability
        'wpsec-token-settings',       // Menu slug
        'wpsec_token_settings_page',  // Callback function to render the page
        'dashicons-lock',             // Icon
        9                             // Position
    );
}
add_action('admin_menu', 'wpsec_add_token_menu');

// Admin token settings page
function wpsec_token_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Security Token Settings', 'wp-sec-si'); ?></h1>
        <form method="post" action="">
            <label for="admin_token"><?php esc_html_e('Enter Security Token to Unlock Editors:', 'wp-sec-si'); ?></label>
            <input type="text" name="admin_token" id="admin_token" value="" autocomplete="off" />
            <input type="submit" name="wpsec_save_token" value="<?php esc_html_e('Save Token', 'wp-sec-si'); ?>" class="button button-primary" />
        </form>

        <?php
        if (isset($_POST['wpsec_save_token'])) {
            $input_token = sanitize_text_field($_POST['admin_token']);
            if ($input_token === ADMIN_SECURITY_TOKEN) {
                set_transient('wpsec_admin_token_valid', true, 30 * MINUTE_IN_SECONDS); // Store token validity for 30 minutes
                echo '<div class="updated"><p>' . esc_html__('Token matched. Access granted!', 'wp-sec-si') . '</p></div>';
            } else {
                echo '<div class="error"><p>' . esc_html__('Invalid token. Please try again.', 'wp-sec-si') . '</p></div>';
            }
        }
        ?>
    </div>
    <?php
}

// Verify admin token via AJAX
function wpsec_check_admin_token() {
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wpsec_admin_token_nonce')) {
        wp_send_json_error(['message' => esc_html__('Invalid nonce!', 'wp-sec-si')]);
    }

    // Check if the token is provided
    if (!isset($_POST['admin_token'])) {
        wp_send_json_error(['message' => esc_html__('Token required!', 'wp-sec-si')]);
    }

    // Sanitize the token input
    $admin_token = $_POST['admin_token']; // Remove sanitize_text_field() for exact comparison

    // Verify the token
    if ($admin_token === ADMIN_SECURITY_TOKEN) {
        set_transient('wpsec_admin_token_valid', true, 30 * MINUTE_IN_SECONDS); // Store for 30 minutes
        wp_send_json_success(['message' => esc_html__('Access granted', 'wp-sec-si')]);
    } else {
        wp_send_json_error(['message' => esc_html__('Invalid token!', 'wp-sec-si')]);
    }
}

add_action('wp_ajax_wpsec_check_admin_token', 'wpsec_check_admin_token');

// Restrict theme & plugin editing unless token is valid
function wpsec_restrict_theme_plugin_editing() {
    if (!get_transient('wpsec_admin_token_valid')) {
        define('DISALLOW_FILE_EDIT', true);
        define('DISALLOW_FILE_MODS', true);
    }
}
add_action('init', 'wpsec_restrict_theme_plugin_editing');

function wpsec_disable_theme_customization() {
    if (!get_transient('wpsec_admin_token_valid')) {
        remove_submenu_page('themes.php', 'customize.php'); // Hide Customizer menu
        
        // Direct Access Restriction
        if (isset($_GET['return']) && strpos($_SERVER['REQUEST_URI'], 'customize.php') !== false) {
            wp_die(__('Access Denied: Security Token Required!', 'wp-sec-si'), 'Security Restriction', 403);
        }
    }
}
add_action('admin_init', 'wpsec_disable_theme_customization');

// Add translation support
function wpsec_load_plugin_textdomain() {
    load_plugin_textdomain('wp-sec-si', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
// add_action('plugins_loaded', 'wpsec_load_plugin_textdomain');

// Cleanup expired token via cron job
function wpsec_cleanup_expired_token() {
    delete_transient('wpsec_admin_token_valid');
}
add_action('wpsec_cleanup_token_cron', 'wpsec_cleanup_expired_token');

// Schedule the cleanup event to run every 30 minutes
if (!wp_next_scheduled('wpsec_cleanup_token_cron')) {
    wp_schedule_event(time(), 'every_30_minutes', 'wpsec_cleanup_token_cron');
}

// Register a custom interval for the cron job
function wpsec_custom_cron_interval($schedules) {
    $schedules['every_30_minutes'] = array(
        'interval' => 30 * MINUTE_IN_SECONDS,
        'display' => 'Once Every 30 Minutes'
    );
    return $schedules;
}
add_filter('cron_schedules', 'wpsec_custom_cron_interval');

