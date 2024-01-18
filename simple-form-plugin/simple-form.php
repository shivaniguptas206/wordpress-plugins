<?php
/**
* Plugin Name: Simple Form Plugin
* Plugin URI: https://www.example.com/
* Description: A plugin to add car details via a form.
* Version: 0.1
* Author: Shivani Gupta
* Author URI: https://www.example.com/
* Text Domain: sfp
**/

register_activation_hook(__FILE__, 'sfp_plugin_activate');
register_deactivation_hook(__FILE__, 'sfp_plugin_deactivate');

function sfp_plugin_activate() {
    // Activation tasks if needed
}

function sfp_plugin_deactivate() {
    // Deactivation tasks if needed
}

require_once plugin_dir_path(__FILE__) . 'admin/class-admin.php';
require_once plugin_dir_path(__FILE__) . 'public/class-public.php';
