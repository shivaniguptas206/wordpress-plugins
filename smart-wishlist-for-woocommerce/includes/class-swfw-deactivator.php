<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://example.com/
 * @since      1.0.0
 *
 * @package    Swfw
 * @subpackage Swfw/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Swfw
 * @subpackage Swfw/includes
 * @author     Example <admin@example.com>
 */
class Swfw_Deactivator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function swfw_deactivate()
	{
		$swfw_wishlist_page_slug = sanitize_title('Wishlist');

		// Check if the page exists
		$swfw_wishlist_page = get_page_by_path($swfw_wishlist_page_slug);

		if ($swfw_wishlist_page) {
			// Delete the specific wishlist page
			wp_delete_post($swfw_wishlist_page->ID, true);

			// Display a success message
			add_action('admin_notices', function () {
				$swfw_success_message = esc_html__('Wishlist page has been removed.', 'swfw');
				echo '<div class="swfw-notice notice notice-success is-dismissible"><p>' . esc_html__($swfw_success_message, 'swfw') . '</p></div>';
			});
		}
	}
}