<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://example.com/
 * @since      1.0.0
 *
 * @package    Swfw
 * @subpackage Swfw/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Swfw
 * @subpackage Swfw/includes
 * @author     Example <admin@example.com>
 */
class Swfw_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function swfw_load_plugin_textdomain() {

		load_plugin_textdomain(
			'swfw',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}
}
