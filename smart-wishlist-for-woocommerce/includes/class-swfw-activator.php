<?php

/**
 * Fired during plugin activation
 *
 * @link       https://example.com/
 * @since      1.0.0
 *
 * @package    Swfw
 * @subpackage Swfw/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Swfw
 * @subpackage Swfw/includes
 * @author     Example <admin@example.com>
 */
class Swfw_Activator
{
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function swfw_activate()
	{
		$swfw_wishlist_page_title = esc_html__('Wishlist', 'swfw');
		$swfw_wishlist_page_slug = sanitize_title($swfw_wishlist_page_title);

		// Check if the page doesn't already exist
		$swfw_wishlist_page = get_page_by_path($swfw_wishlist_page_slug);

		if (!$swfw_wishlist_page) {
			// Create a new page post
			$swfw_page_args = array(
				'post_title' => $swfw_wishlist_page_title,
				'post_content' => ' ',
				'post_status' => 'publish',
				'post_type' => 'page',
				'post_name' => $swfw_wishlist_page_slug,
			);

			$swfw_page_id = wp_insert_post($swfw_page_args);
			// Set the new page as the wishlist page
			$swfw_selected_page['swfw_page_show'] = $swfw_page_id;
			update_option('swfw_general_settings_fields', $swfw_selected_page);
			// Store the success message in a variable
			$swfw_success_message = esc_html__('Wishlist page created with ID:', 'swfw') . ' ' . esc_html__($swfw_page_id, 'swfw');
		} else {
			// Update the existing wishlist page ID only if the admin has selected it
			$swfw_selected_page = get_option('swfw_general_settings_fields');
			$swfw_admin_selected_page = isset($swfw_selected_page['swfw_page_show']) ? $swfw_selected_page['swfw_page_show'] : '';

			if ($swfw_admin_selected_page == $swfw_wishlist_page->ID) {
				$swfw_selected_page['swfw_page_show'] = $swfw_wishlist_page->ID;
				update_option('swfw_general_settings_fields', $swfw_selected_page);
			}
			// Store the success message in a variable
			$swfw_success_message = esc_html__('Wishlist page already exists with ID:', 'swfw') . ' ' . esc_html__($swfw_wishlist_page->ID, 'swfw');
		}

		// Display the success message outside of the activation function
		add_action('admin_notices', function () use ($swfw_success_message) {
			echo '<div class="swfw-notice notice notice-success is-dismissible"><p>' . esc_html__($swfw_success_message, 'swfw') . '</p></div>';
		});
	}

}