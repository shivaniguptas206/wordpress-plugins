<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://example.com/
 * @since      1.0.0
 *
 * @package    Swfw
 * @subpackage Swfw/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Swfw
 * @subpackage Swfw/public
 * @author     Example <admin@example.com>
 */
class Swfw_Public
{
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $swfw_plugin_name    The ID of this plugin.
	 */
	private $swfw_plugin_name;
	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $swfw_version    The current version of this plugin.
	 */
	private $swfw_version;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $swfw_plugin_name       The name of the plugin.
	 * @param      string    $swfw_version    The version of this plugin.
	 */
	public function __construct($swfw_plugin_name, $swfw_version)
	{

		$this->swfw_plugin_name = $swfw_plugin_name;
		$this->swfw_version = $swfw_version;

	}
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function swfw_enqueue_styles()
	{
		wp_enqueue_style($this->swfw_plugin_name, plugin_dir_url(__FILE__) . 'css/swfw-public.css', array(), $this->swfw_version, 'all');

	}
	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	
	public function swfw_enqueue_scripts()
	{
		// Enqueue the swfw-public-wishlist.js script
		wp_enqueue_script('swfw-public-wishlist', plugin_dir_url(__FILE__) . 'js/swfw-public-wishlist.js', array('jquery'), '1.0.0', false);
	
		// Enqueue the swfw-public.js script
		wp_enqueue_script('swfw-public', plugin_dir_url(__FILE__) . 'js/swfw-public-remove-wishlist.js', array('jquery'), '1.0.0', false);
	
		// Generate and localize the nonce token and other localized messages
		$swfw_localized_data = array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('swfw_wishlist_nonce'),
			'remove_success' => esc_html__('Product removed successfully.', 'swfw'),
			'remove_error' => esc_html__('An error occurred while removing the product.', 'swfw'),
			'remove_error_wishlist' => esc_html__('An error occurred while product adding.', 'swfw'),
			'no_products_selected' => esc_html__('Please select products to remove.', 'swfw'),
			'no_products_select_add_into_cart' => esc_html__('Please select products to add in cart.', 'swfw'),
			'empty_wishlist_message' => esc_html__('Your wishlist is empty.', 'swfw'),
			'product_added_message' => esc_html__('Product added to wishlist.', 'swfw'), // Localized alert message
			'copy_error_message' => esc_html__('Unable to copy to clipboard', 'swfw'), // Add this line for the copy error message

		);
	
		wp_localize_script('swfw-public', 'swfw_ajax_object', $swfw_localized_data);
	}
	/**
	 * Callback function for adding the "Add to Wishlist" button on the Shop Page and product page.
	 * 
	 * This function determines whether the button should be displayed based on the current page and user settings.
	 * If the button should be displayed, it generates the HTML markup for the button, message, and view wishlist link.
	 * It also defines the onclick event for the button to display a message and handle login requirements.
	 * 
	 * Note: This function serves as a demonstration and should be hooked to the appropriate action or filter.
	 * @since    1.0.0
	 */
	function swfw_wishlistify_add_wishlist_button()
	{
		global $product;

		$swfw_wishlist_product_page = isset(get_option('swfw_general_settings_fields')['swfw_wishlist_product_page']) ? sanitize_text_field(get_option('swfw_general_settings_fields')['swfw_wishlist_product_page']) : false;
		$swfw_settings = get_option('swfw_general_settings_fields');
		$swfw_enable_wishlist_button = isset($swfw_settings['swfw_enable_wishlist_button']) ? sanitize_text_field($swfw_settings['swfw_enable_wishlist_button']) : '0';

		// Retrieve the wishlist array from user meta
		$swfw_user_id = get_current_user_id();
		$swfw_wishlists = get_user_meta($swfw_user_id, 'swfw_smart_wishlist_meta', true) ?: array();
		
		$swfw_product_id = absint($product->get_id());

		// Check if the product exists in any of the wishlists
		$swfw_product_in_wishlist = false;
		foreach ($swfw_wishlists as $swfw_wishlist_name => $swfw_wishlist_data) {
			if (isset($swfw_wishlist_data['product_ids']) && is_array($swfw_wishlist_data['product_ids']) && in_array($swfw_product_id, $swfw_wishlist_data['product_ids'])) {
				$swfw_product_in_wishlist = true;
				break;
			}
		}

		if ($swfw_product_in_wishlist && (is_shop() || ($swfw_wishlist_product_page && is_product())) && $swfw_enable_wishlist_button === '1') {
			$swfw_options = get_option('swfw_add_to_wishlist_options_fields');
			$swfw_view_button_type_options = isset($swfw_options['swfw_view_button_type_options']) ? sanitize_text_field($swfw_options['swfw_view_button_type_options']) : 'swfw_show_view_wishlist';
			$swfw_view_wishlist_text = isset($swfw_options['swfw_view_wishlist_text']) ? sanitize_text_field($swfw_options['swfw_view_wishlist_text']) : '';
			$swfw_wishlist_page_url = get_permalink(get_option('swfw_general_settings_fields')['swfw_page_show']);

			if ($swfw_view_button_type_options === 'swfw_show_view_wishlist_icon') {
				// Show "View Wishlist" icon
				?>
				<div class="swfw-success-message"></div>
				<button class="swfw-view-wishlist-button swfw-button"><a href="<?php echo esc_url($swfw_wishlist_page_url); ?>"><i
							class="fa fa-eye" aria-hidden="true"></i></a></button>
				<?php
			} elseif (!empty($swfw_view_wishlist_text)) {
				// Show customized "View Wishlist" text
				?>
				<button class="swfw-view-wishlist-button swfw-button"><a href="<?php echo esc_url($swfw_wishlist_page_url); ?>"><?php echo esc_html__($swfw_view_wishlist_text, 'swfw'); ?></a></button>
				<?php
			} else {
				// Show default "View Wishlist" button
				?>
				<button class="swfw-view-wishlist-button swfw-button"><a href="<?php echo esc_url($swfw_wishlist_page_url); ?>"><?php esc_html_e('View Wishlist', 'swfw'); ?></a></button>
				<?php
			}
		} else {

			if ($swfw_enable_wishlist_button === '1' && (is_shop() || ($swfw_wishlist_product_page && is_product()))) {

				// The product is not in the wishlist, display "Add to Wishlist" button
				$swfw_options = get_option('swfw_general_settings_fields');
				$swfw_wishlist_name = isset($swfw_options['swfw_name_string']) ? sanitize_text_field($swfw_options['swfw_name_string']) : '';

				if (empty($swfw_wishlist_name)) {
					$swfw_wishlist_name = esc_html__('Add to Wishlist', 'swfw');
				}

				$swfw_wishlist_button = isset($swfw_options['swfw_wishlist_button']) ? sanitize_text_field($swfw_options['swfw_wishlist_button']) : '';
				$swfw_add_to_wishlist_icon = isset($swfw_options['swfw_add_to_wishlist_icon']) ? sanitize_text_field($swfw_options['swfw_add_to_wishlist_icon']) : 'fa fa-heart';
				$swfw_add_to_wishlist_both = isset($swfw_options['swfw_add_to_wishlist_both']) ? sanitize_text_field($swfw_options['swfw_add_to_wishlist_both']) : 'both';

				if ($swfw_wishlist_button === 'swfw_show_wishlist_icon' || $swfw_wishlist_button === 'swfw_show_wishlist_both') {
					// Display the icon
					?>
					<button class="swfw-wishlistify-button swfw-button swfw-add-to-wishlist"
						data-product-id="<?php echo esc_attr($swfw_product_id); ?>" data-user-id="<?php echo esc_attr($swfw_user_id); ?>">
						<?php
						$swfw_options_wishlist = get_option('swfw_add_to_wishlist_options_fields');
						// Customize the icon style
						$swfw_add_to_wishlist_icon_style = isset($swfw_options_wishlist['swfw_add_to_wishlist_icon_style']) ? sanitize_text_field($swfw_options_wishlist['swfw_add_to_wishlist_icon_style']) : '';

						if (!empty($swfw_add_to_wishlist_icon_style)) {
							echo '<i class="' . esc_attr($swfw_add_to_wishlist_icon_style) . '" aria-hidden="true"></i>';
						} else {
							echo '<i class="' . esc_attr($swfw_add_to_wishlist_icon) . ' " aria-hidden="true"></i>';
						}
						// Display the text if both icon and text are selected
						if ($swfw_add_to_wishlist_both === 'both' && $swfw_wishlist_button === 'swfw_show_wishlist_both') {
							echo esc_html__($swfw_wishlist_name, 'swfw');
						}
						?>
					</button>
					<?php

				} else {
					// Display the default button
					?>
					<button class="swfw-wishlistify-button swfw-button swfw-add-to-wishlist"
						data-product-id="<?php echo esc_attr($swfw_product_id); ?>"
						data-user-id="<?php echo esc_attr($swfw_user_id); ?>"><?php echo esc_html__($swfw_wishlist_name, 'swfw'); ?></button>
					<?php
				}
				// Display the unlogged user message
				?>
				<div class="swfw-unlogged-message">
					<?php esc_html_e('Please add this product to your wishlist.', 'swfw'); ?>
					<a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>"><?php esc_html_e('My Account', 'swfw'); ?></a>
				</div>
				<?php
			}
		}
	}

	/**
	 * Callback function for adding a product to the wishlist via AJAX.
	 * 
	 * This function handles the AJAX request sent when a user adds a product to their wishlist.
	 * It retrieves the product data from the POST request and updates the wishlist cookie.
	 * The product ID is added to the list of wishlist product IDs stored in the cookie.
	 * If the request is successful, it sends a JSON response with a success message.
	 * If the request is invalid, it sends a JSON response with an error message.
	 * @since    1.0.0
	 */
	function swfw_add_to_wishlist()
	{
		// Verify the nonce
		if (!wp_verify_nonce($_POST['swfwnonce'], 'swfw_wishlist_nonce')) {
			// Nonce verification failed, handle the error
			$swfw_secure = esc_html__('Invalid nonce.', 'swfw');
			wp_send_json_error($swfw_secure);
		}

		if (isset($_POST['product_id'])) {
			$swfw_product_id = isset($_POST['product_id']) ? sanitize_key($_POST['product_id']) : '';

			// Get the user ID
			$swfw_user_id = get_current_user_id();

			// Validate the product ID
			if ($swfw_product_id > 0) {
				// Get the existing wishlist array from user meta
				$swfw_wishlists = get_user_meta($swfw_user_id, 'swfw_smart_wishlist_meta', true);
				if (!$swfw_wishlists) {
					$swfw_wishlists = array(); // Initialize an empty array if wishlists don't exist
				}

				$swfw_multi_wishlist_options = get_option('swfw_pro_version_settings_fields');
				$swfw_wishlist_collection_enabled = isset($swfw_multi_wishlist_options['swfw_wishlist_collection']) && $swfw_multi_wishlist_options['swfw_wishlist_collection'] === '1';

				if ($swfw_wishlist_collection_enabled) {
					// Wishlist collection is enabled, check if the wishlist name is provided
					if (isset($_POST['wishlist_name']) && !empty($_POST['wishlist_name'])) {
						$swfw_wishlist_name = sanitize_text_field($_POST['wishlist_name']);
					} else {
						// Default wishlist name if not provided
						$swfw_wishlist_name = 'MyWishlist';
					}

					// Check if the current wishlist exists, or create a new one
					if (!isset($swfw_wishlists[$swfw_wishlist_name])) {
						$swfw_wishlists[$swfw_wishlist_name] = array(
							'product_ids' => array(),
						);
					}

					// Check if the product ID already exists in the current wishlist
					if (!in_array($swfw_product_id, $swfw_wishlists[$swfw_wishlist_name]['product_ids'])) {
						// Add the product ID to the current wishlist
						$swfw_wishlists[$swfw_wishlist_name]['product_ids'][] = $swfw_product_id;

						// Update the wishlist in user meta
						update_user_meta($swfw_user_id, 'swfw_smart_wishlist_meta', $swfw_wishlists);

						// Send the updated wishlists array as the response data
						wp_send_json_success(
							array(
								'user_id' => absint($swfw_user_id),
								'wishlists' => $swfw_wishlists // Modified response key to represent the wishlists array
							)
						);
					} else {
						// Send a JSON error response indicating that the product is already in the wishlist
						wp_send_json_error(
							array(
								'message' => esc_html__('Product is already added to the wishlist.', 'swfw'),
								'user_id' => absint($swfw_user_id)
							)
						);
					}
				} else {
					// Wishlist collection is disabled, create a single wishlist
					if (!isset($swfw_wishlists['MyWishlist'])) {
						$swfw_wishlists['MyWishlist'] = array(
							'product_ids' => array(),
						);
					}

					// Check if the product ID already exists in the wishlist
					if (!in_array($swfw_product_id, $swfw_wishlists['MyWishlist']['product_ids'])) {
						// Add the product ID to the wishlist
						$swfw_wishlists['MyWishlist']['product_ids'][] = $swfw_product_id;

						// Update the wishlist in user meta
						update_user_meta($swfw_user_id, 'swfw_smart_wishlist_meta', $swfw_wishlists);

						// Send the updated wishlist array as the response data
						wp_send_json_success(
							array(
								'user_id' => absint($swfw_user_id),
								'wishlists' => $swfw_wishlists // Modified response key to represent the wishlists array
							)
						);
					} else {
						// Send a JSON error response indicating that the product is already in the wishlist
						wp_send_json_error(
							array(
								'message' => esc_html__('Product is already added to the wishlist.', 'swfw'),
								'user_id' => absint($swfw_user_id)
							)
						);
					}
				}
			} else {
				// Send a JSON error response for an invalid product ID
				wp_send_json_error(esc_html__('Invalid product ID.', 'swfw'));
			}
		} else {
			// Send a JSON error response for an invalid request
			wp_send_json_error(esc_html__('Invalid request.', 'swfw'));
		}
	}

	/**
	 * Callback function for removing a product from the wishlist via AJAX.
	 *
	 * This function handles the AJAX request sent when a user removes a product from their wishlist.
	 * It retrieves the product ID from the POST request and checks if it exists in the wishlist.
	 * If the product ID is found, it removes it from the wishlist and updates the wishlist session variable.
	 * It then generates the updated wishlist HTML using the updated list of product IDs.
	 * If the removal is successful, it sends a JSON response with the updated wishlist HTML.
	 * If the product ID is not found in the wishlist, it sends a JSON response with an error message.
	 * If the wishlist session variable is empty or not set, it sends a JSON response with an error message.
	 * If the request is invalid, it sends a JSON response with an error message.
	 * @since    1.0.0
	 */
	function swfw_remove_product_from_wishlist()
	{
		// Verify the nonce
		if (!wp_verify_nonce($_POST['swfwremovenonce'], 'swfw_wishlist_nonce')) {
			// Nonce verification failed, handle the error
			$swfw_remove_nonce_msg = esc_html__('Invalid nonce.', 'swfw');
			wp_send_json_error($swfw_remove_nonce_msg);
		}

		if (isset($_POST['productID'])) {
			$swfw_productID = sanitize_key($_POST['productID']);

			// Validate the product ID
			if ($swfw_productID > 0) {
				// Get the user ID
				$swfw_user_id = get_current_user_id();

				// Get the existing wishlist array from user meta
				$swfw_wishlist = get_user_meta($swfw_user_id, 'swfw_smart_wishlist_meta', true);

				// Check if the wishlist exists and is not empty
				if ($swfw_wishlist && !empty($swfw_wishlist['MyWishlist']['product_ids'])) {
					// Find the index of the product ID to be removed
					$swfw_index = array_search($swfw_productID, $swfw_wishlist['MyWishlist']['product_ids']);

					if ($swfw_index !== false) {
						// Remove the product ID from the array
						unset($swfw_wishlist['MyWishlist']['product_ids'][$swfw_index]);

						// Update the wishlist in user meta
						update_user_meta($swfw_user_id, 'swfw_smart_wishlist_meta', $swfw_wishlist);

						// Generate the updated wishlist HTML (if needed)
						$swfw_updatedWishlistHTML = $this->swfw_wishlist_product_shortcode();

						// Send the updated wishlist HTML as the response (if needed)
						wp_send_json_success($swfw_updatedWishlistHTML);

						// Send a success response without the updated HTML (if the HTML is being generated on the client-side)
						wp_send_json_success();
					} else {
						// Send an error response if the product ID is not found in the wishlist
						wp_send_json_error(esc_html__('Product not found in wishlist.', 'swfw'));
					}
				} else {
					// Send an error response if the wishlist is empty or doesn't exist
					wp_send_json_error(esc_html__('Empty wishlist.', 'swfw'));
				}
			} else {
				// Send an error response for an invalid product ID
				wp_send_json_error(esc_html__('Invalid product ID.', 'swfw'));
			}
		} else {
			// Send an error response for an invalid request
			wp_send_json_error(esc_html__('Invalid request.', 'swfw'));
		}
	}

	/**
	 * Shortcode callback function for adding or removing shortcode.
	 * 
	 * This function is responsible for adding or removing the shortcode based on the selected page.
	 * It retrieves the selected page from the options, removes the shortcode from the previous page if necessary,
	 * adds the shortcode to the selected page, and updates the necessary data.
	 * 
	 * @since 1.0.0
	 */
	function swfw_add_remove_shortcode($swfw_content)
	{
		// Start or resume the session
		if (!session_id()) {
			session_start();
		}

		// Get the selected page from the options
		$swfw_selected_page = get_option('swfw_general_settings_fields');
		$swfw_wishlist_page = isset($swfw_selected_page['swfw_page_show']) ? sanitize_key($swfw_selected_page['swfw_page_show']) : '';

		// Validate the wishlist page ID
		if (!empty($swfw_wishlist_page) && $swfw_wishlist_page !== '') {
			// Get the previous page ID from the session variable
			$swfw_previous_wishlist_page = isset($_SESSION['swfw_previous_page_id']) ? sanitize_key($_SESSION['swfw_previous_page_id']) : '';

			// Remove the shortcode from the previous page
			if (!empty($swfw_previous_wishlist_page) && $swfw_previous_wishlist_page !== $swfw_wishlist_page) {
				$swfw_previous_page = get_post($swfw_previous_wishlist_page);

				if ($swfw_previous_page instanceof WP_Post) {
					$swfw_previous_content = $swfw_previous_page->post_content;
					$swfw_previous_content = str_replace('[swfw_smart_wishlist]', '', $swfw_previous_content);
					$swfw_previous_page->post_content = $swfw_previous_content;
					wp_update_post($swfw_previous_page);
				}
			}

			// Store the current selected page as the previous page
			$swfw_selected_page['swfw_previous_page_show'] = $swfw_wishlist_page;
			update_option('swfw_general_settings_fields', $swfw_selected_page);

			// Add the shortcode to the selected page
			$swfw_new_page = get_post($swfw_wishlist_page);
			if ($swfw_new_page instanceof WP_Post) {
				$swfw_new_content = $swfw_new_page->post_content;
				if (!has_shortcode($swfw_new_content, 'swfw_smart_wishlist')) {
					$swfw_new_content .= '[swfw_smart_wishlist]';
					$swfw_new_page->post_content = $swfw_new_content;
					wp_update_post($swfw_new_page);
				}
			}
			// Store the current page ID as the previous page ID in the session variable
			$_SESSION['swfw_previous_page_id'] = $swfw_wishlist_page;
		}

		// Return the modified content
		return $swfw_content;
	}
	/**
	 * Callback function for changing the button CSS style.
	 *
	 * This function retrieves the style options from the database and outputs the custom CSS style for the button based on the selected options.
	 * It applies the button radius, button color, button text size, button text style, button font family, and text color CSS styles to the button.
	 * The CSS styles are outputted inside the <style> tags.
	 *
	 * @since    1.0.0
	 */
	function swfw_change_button_css_style()
	{
		// Retrieve the style options from the database
		$swfw_options = get_option('swfw_style_options');

		// Retrieve and sanitize the button style values
		$swfw_button_radius = isset($swfw_options['swfw_button_radius']) ? sanitize_text_field($swfw_options['swfw_button_radius']) : '';
		$swfw_button_text_style = isset($swfw_options['swfw_button_text_style']) ? sanitize_text_field($swfw_options['swfw_button_text_style']) : '';
		$swfw_button_color = isset($swfw_options['swfw_button_color']) ? sanitize_hex_color($swfw_options['swfw_button_color']) : '';
		$swfw_button_color_enable = isset($swfw_options['swfw_button_color_enable']) ? $swfw_options['swfw_button_color_enable'] : '';

		// Retrieve and sanitize the text color style values
		$swfw_text_color_css = isset($swfw_options['swfw_text_color_css']) ? sanitize_hex_color($swfw_options['swfw_text_color_css']) : '';
		$swfw_text_color_enable = isset($swfw_options['swfw_text_color_enable']) ? $swfw_options['swfw_text_color_enable'] : '';

		// Retrieve and sanitize the button text size value
		$swfw_button_text_size = isset($swfw_options['swfw_button_text_size']) ? sanitize_text_field($swfw_options['swfw_button_text_size']) : '';

		// Check if CSS customization is enabled
		if (isset($swfw_options['swfw_enable_css']) && $swfw_options['swfw_enable_css'] == 1) {
			// If CSS customization is enabled, proceed to generate dynamic CSS style

			// Generate the dynamic CSS style
			$swfw_dynamic_css = "
	    .swfw-button {
	        border-radius: {$swfw_button_radius}px;
	        font-style: {$swfw_button_text_style};
	        " . ($swfw_button_color_enable == 1 ? "background-color: {$swfw_button_color};" : "") . "
	        " . ($swfw_text_color_enable == 1 ? "color: {$swfw_text_color_css};" : "") . "
	        " . ($swfw_button_text_size ? "font-size: {$swfw_button_text_size}px;" : "") . " }	";

			// Register the dynamic CSS as an external stylesheet
			//This function registers a new stylesheet named 'swfw-dynamic-style'
			wp_register_style('swfw-dynamic-style', false);
			//This function adds the registered 'swfw-dynamic-style' stylesheet to the list of stylesheets to be loaded on the page.
			wp_enqueue_style('swfw-dynamic-style');
			//This function adds the generated $swfw_dynamic_css as inline styles to the 'swfw-dynamic-style' stylesheet. 
			wp_add_inline_style('swfw-dynamic-style', $swfw_dynamic_css);
			// The dynamic CSS will be added to the 'swfw-dynamic-style' stylesheet and loaded on the frontend.
		}

	}

	/**
	 * Callback function for removing multiple products from the wishlist via AJAX.
	 *
	 * This function handles the removal of multiple products from the user's wishlist
	 * when an AJAX request is made. It verifies the nonce for security, checks if the user
	 * is logged in and has the necessary capabilities to perform the action, validates the
	 * received data, and updates the user's wishlist by removing the selected products.
	 * It returns a JSON response indicating the success or failure of the operation.
	 *
	 * @since 1.0.0
	 */
	function swfw_remove_multiple_products_from_wishlist()
	{
		// Verify the nonce
		if (!isset($_POST['swfwmultiremovenonce']) || !wp_verify_nonce($_POST['swfwmultiremovenonce'], 'swfw_wishlist_nonce')) {
			// Nonce verification failed, handle the error
			wp_send_json_error(esc_html__('Invalid nonce.', 'swfw'));
		}

		// Check if the user is logged in or has necessary capabilities to perform this action
		if (!is_user_logged_in() || !current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('You are not allowed to perform this action.', 'swfw'));
		}

		// Check if the required data is received
		if (!isset($_POST['productIDs']) || !is_array($_POST['productIDs'])) {
			wp_send_json_error(esc_html__('Invalid data.', 'swfw'));
		}

		$swfw_productIDs = array_map('intval', $_POST['productIDs']);

		// Get the user ID
		$swfw_user_id = get_current_user_id();

		// Get the existing wishlist array from user meta
		$swfw_wishlist = get_user_meta($swfw_user_id, 'swfw_smart_wishlist_meta', true);

		if ($swfw_wishlist && is_array($swfw_wishlist) && !empty($swfw_wishlist)) {
			// Remove the selected product IDs from the wishlist array
			$swfw_updated_wishlist = array();
			foreach ($swfw_wishlist as $swfw_wishlist_key => $swfw_wishlist_data) {
				if (isset($swfw_wishlist_data['product_ids']) && is_array($swfw_wishlist_data['product_ids'])) {
					$swfw_updated_wishlist[$swfw_wishlist_key] = array(
						'product_ids' => array_diff($swfw_wishlist_data['product_ids'], $swfw_productIDs),
					);
				}
			}

			// Update the wishlist in user meta
			update_user_meta($swfw_user_id, 'swfw_smart_wishlist_meta', $swfw_updated_wishlist);

			// Send a success response back to the AJAX request
			wp_send_json_success(esc_html__('Products removed successfully.', 'swfw'));
		} else {
			// Send an error response if the wishlist is empty or doesn't exist
			wp_send_json_error(esc_html__('Empty wishlist.', 'swfw'));
		}
	}

	/**
	 * Callback function for adding multiple products to the cart via AJAX.
	 *
	 * This function handles the addition of multiple products to the cart when an AJAX request is made.
	 * It verifies the nonce for security, checks if the user is logged in and has the necessary capabilities
	 * to perform the action, validates the received data, and adds each selected product to the cart.
	 * After successfully adding products to the cart, it returns a JSON response with the updated cart count.
	 *
	 * @since 1.0.0
	 */
	function swfw_add_multiple_to_cart()
	{
		// Verify the nonce
		if (!isset($_POST['swfwmultinoncecart']) || !wp_verify_nonce($_POST['swfwmultinoncecart'], 'swfw_wishlist_nonce')) {
			// Nonce verification failed, handle the error
			wp_send_json_error(esc_html__('Invalid nonce.', 'swfw'));
		}

		// Check if the user is logged in or has necessary capabilities to perform this action
		if (!is_user_logged_in() || !current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('You are not allowed to perform this action.', 'swfw'));
		}

		// Get the user ID
		$swfw_user_id = get_current_user_id();

		// Get the selected product IDs from the AJAX request
		$swfw_productIDs = isset($_POST['productIDs']) ? $_POST['productIDs'] : array();

		// Validate the product IDs
		$swfw_validatedProductIDs = array_map('intval', $swfw_productIDs);
		$swfw_validatedProductIDs = array_filter($swfw_validatedProductIDs, function ($id) {
			return $id > 0;
		});

		if (empty($swfw_validatedProductIDs)) {
			wp_send_json_error(esc_html__('No valid product IDs provided.', 'swfw'));
		}

		// Loop through the validated product IDs and add each product to the cart
		foreach ($swfw_validatedProductIDs as $swfw_productID) {
			WC()->cart->add_to_cart($swfw_productID);
		}

		// Return the updated cart data
		$swfw_cart_count = WC()->cart->get_cart_contents_count();

		// Prepare the response
		$swfw_response = array(
			'cart_count' => $swfw_cart_count,
			'message' => esc_html__('Products added to cart successfully.', 'swfw'),
		);

		// Send the success response back to the AJAX request
		wp_send_json_success($swfw_response);
	}

	/**
	 * Shortcode callback function for displaying the wishlist product table.
	 * 
	 * This function retrieves the product IDs stored in the cookie and generates the HTML markup for the wishlist table.
	 * It loops through each product ID, retrieves the product details, and adds a row to the table with the product name, price, and "Add to Cart" button.
	 * If there are no product IDs in the cookie, it displays a message indicating an empty wishlist.
	 * 
	 * Note: This function should be used as a shortcode callback and placed in the appropriate shortcode handler.
	 * @since    1.0.0
	 */
	function swfw_wishlist_product_shortcode()
	{
		// Get the user ID
		$swfw_user_id = get_current_user_id();
		$swfw_wishlists = get_user_meta($swfw_user_id, 'swfw_smart_wishlist_meta', true);

		$swfw_wishlist_table_columns = get_option('swfw_wishlist_page_options_fields');
		$swfw_social_setting = get_option('swfw_social_networks_settings_fields');

		$swfw_wishlist_table_columns_settings = isset($swfw_wishlist_table_columns['swfw_wishlist_table_columns']) ? $swfw_wishlist_table_columns['swfw_wishlist_table_columns'] : array();
		$swfw_social_networks = isset($swfw_social_setting['swfw_social_networks']) ? $swfw_social_setting['swfw_social_networks'] : array();
		$swfw_social_networks_show_icon = isset($swfw_social_setting['swfw_social_networks_show_icon']) ? $swfw_social_setting['swfw_social_networks_show_icon'] : 0;
		$swfw_enable_social_sharing = isset($swfw_social_setting['swfw_enable_social_sharing']) ? $swfw_social_setting['swfw_enable_social_sharing'] : 0;
		
		// Check if the wishlist exists and is not empty
		if ($swfw_wishlists['MyWishlist']['product_ids'] && !empty($swfw_wishlists['MyWishlist']['product_ids'])) {
			$swfw_html = '';

			// Generate the HTML markup for the wishlist table using the product IDs
			$swfw_html .= '<table id="swfw-wishlist-table">
				<tr>
					<th>' . esc_html__('Select', 'swfw') . '</th>
					<th>' . esc_html__('Product Image', 'swfw') . '</th>
					<th>' . esc_html__('Product Name', 'swfw') . '</th>
					<th>' . esc_html__('Price', 'swfw') . '</th>';

			// Check if the 'product_variations' column is enabled in admin
			if (is_array($swfw_wishlist_table_columns_settings) && in_array('product_variations', $swfw_wishlist_table_columns_settings)) {
				$swfw_html .= '<th>' . esc_html__('Product Variations', 'swfw') . '</th>';
			}

			// Check if the 'product_stock' column is enabled in admin
			if (is_array($swfw_wishlist_table_columns_settings) && in_array('product_stock', $swfw_wishlist_table_columns_settings)) {
				$swfw_html .= '<th>' . esc_html__('Product Stock', 'swfw') . '</th>';
			}

			// Check if the 'date_added' column is enabled in admin
			if (is_array($swfw_wishlist_table_columns_settings) && in_array('date_added', $swfw_wishlist_table_columns_settings)) {
				$swfw_html .= '<th>' . esc_html__('Date Added', 'swfw') . '</th>';
			}

			$swfw_html .= '<th>' . esc_html__('Product Add To Cart', 'swfw') . '</th>
					<th>' . esc_html__('Action', 'swfw') . '</th>
				</tr>';

			// Get the product IDs from the user meta (default wishlist or "My Wishlist")
			if ($swfw_wishlists && isset($swfw_wishlists['MyWishlist']) && is_array($swfw_wishlists['MyWishlist']['product_ids'])) {
				foreach ($swfw_wishlists['MyWishlist']['product_ids'] as $swfw_product_id) {
					// Get the product details based on the product ID
					$swfw_product = wc_get_product($swfw_product_id);

					// Check if the product exists
					if (!$swfw_product) {
						continue; // Skip this product if it doesn't exist
					}
					$swfw_product_image = $swfw_product->get_image();

					// Get the product variations
					$swfw_product_variations = '';
					if (is_array($swfw_wishlist_table_columns_settings) && in_array('product_variations', $swfw_wishlist_table_columns_settings)) {
						$swfw_product_variations = $this->swfw_get_product_variations($swfw_product);
					}

					// Get the product stock
					$swfw_product_stock = '';
					if (is_array($swfw_wishlist_table_columns_settings) && in_array('product_stock', $swfw_wishlist_table_columns_settings)) {
						$swfw_product_stock = $this->swfw_get_product_stock($swfw_product);
					}

					// Get the date added
					$swfw_date_added = '';
					if (is_array($swfw_wishlist_table_columns_settings) && in_array('date_added', $swfw_wishlist_table_columns_settings)) {
						$swfw_date_added = $this->swfw_get_product_date_added($swfw_product_id);
					}

					// Build the HTML table row for each product
					$swfw_html .= '<tr>
							<td><input type="checkbox" class="swfw-product-checkbox" data-product-id="' . $swfw_product_id . '"></td>
							<td class="swfw-img"><a href="' . esc_url(get_permalink($swfw_product->get_id())) . '">' . $swfw_product_image . '</a></td>
							<td>' . esc_html($swfw_product->get_name()) . '</td>
							<td>' . $swfw_product->get_price_html() . '<span class="swfw-get-price-html" >' . $swfw_product->get_price() . '</span></td>';

					// Add the product variations column if enabled
					if (is_array($swfw_wishlist_table_columns_settings) && in_array('product_variations', $swfw_wishlist_table_columns_settings)) {
						$swfw_html .= '<td>' . esc_html(sanitize_text_field($swfw_product_variations)) . '</td>';
					}

					// Add the product stock column if enabled
					if (is_array($swfw_wishlist_table_columns_settings) && in_array('product_stock', $swfw_wishlist_table_columns_settings)) {
						$swfw_html .= '<td>' . esc_html(sanitize_text_field($swfw_product_stock)) . '</td>';
					}

					// Add the date added column if enabled
					if (is_array($swfw_wishlist_table_columns_settings) && in_array('date_added', $swfw_wishlist_table_columns_settings)) {
						$swfw_html .= '<td>' . esc_html(sanitize_text_field($swfw_date_added)) . '</td>';
					}

					// Modify the "Add to Cart" button to include data-product-ids attribute
					$swfw_html .= '<td><a href="' . esc_url($swfw_product->add_to_cart_url()) . '" class="swfw-button-cart button swfw-button">' . esc_html__('Add to Cart', 'swfw') . '</a></td>';

					$swfw_html .= '<td><button class="swfw-remove-product swfw-button" data-product-id="' . $swfw_product_id . '">' . esc_html__('Remove', 'swfw') . '</button></td>
						</tr>';
				}
			}
			$swfw_html .= '</table>';
			
			$swfw_html .= '<div class="swfw-action-buttons">
			<select class="swfw-action-select swfw-button">
				<option value="">' . esc_html__('Action', 'swfw') . '</option>
				<option value="swfw-multiple-add-to-cart">' . esc_html__('Add to Cart', 'swfw') . '</option>
				<option value="remove">' . esc_html__('Remove', 'swfw') . '</option>
			</select>
			<button id="swfw-apply-button" class="swfw-button">' . esc_html__('Apply Action', 'swfw') . '</button>
			</div>';
		}else {
				// If the wishlist is empty, do not display the table heading and apply action button
				$swfw_html = '<div class="swfw-wishlist-empty-message">' . esc_html__('Your Wishlist is Empty!', 'swfw') . '</div><br>';
				$swfw_html .= '<div class="swfw-return-to-shop-button">';
				$swfw_html .= '<a href="' . esc_url(get_permalink(wc_get_page_id('shop'))) . '" class="swfw-return-button button swfw-button">' . esc_html__('Return to Shop', 'swfw') . '</a>';
				$swfw_html .= '</div>';
			}
			
			// Check if the "Enable Social Sharing" option is enabled
			if ($swfw_enable_social_sharing) {
				// Add the heading for social networks
				$swfw_html .= '<h2>' . esc_html__('Wishlists Share on Social Networks', 'swfw') . '</h2>';

				// Add the share buttons for social networks
				$swfw_html .= '<div class="swfw-social-share-buttons " id="swfw-social-share-buttons">';
				if (in_array('whatsapp', $swfw_social_networks)) {
					// Get the wishlist page URL dynamically using get_permalink()
					$swfw_wishlist_link = get_permalink(); // Assuming the current page is the wishlist page

					// Create the WhatsApp share message with the wishlist link
					$swfw_whatsapp_message = esc_html__('Check out my wishlist: ','swfw') . $swfw_wishlist_link;

					// Add the WhatsApp share button
					$swfw_whatsapp_share_url = 'https://web.whatsapp.com/send?text=' . urlencode($swfw_whatsapp_message);
					$swfw_html .= '<a href="' . esc_url($swfw_whatsapp_share_url) . '" target="_blank" class="swfw-social-share-button swfw-whatsapp-share ">';
					if ($swfw_social_networks_show_icon) {
						$swfw_html .= '<i class="fab fa-whatsapp"></i>';
					}
					$swfw_html .= esc_html__('WhatsApp', 'swfw') . '</a>';
				}
				// Assuming $swfw_social_networks contains the list of enabled social networks
				if (in_array('gmail', $swfw_social_networks)) {
					// Get the wishlist page URL dynamically using get_permalink()
					$swfw_wishlist_link = get_permalink();

					// Generate the Gmail share URL with the wishlist link pre-filled in the body of the email
					$swfw_gmail_share_url = 'mailto:?body=' . urlencode($swfw_wishlist_link);

					// Add the Gmail share link with the icon
					$swfw_html .= '<a href="' . esc_url($swfw_gmail_share_url) . '" target="_blank" class="swfw-social-share-button swfw-gmail-share">';
					if ($swfw_social_networks_show_icon) {
						$swfw_html .= '<i class="fa fa-envelope"></i>'; // Using FontAwesome icon class
					}
					$swfw_html .= esc_html__('Gmail', 'swfw') . '</a>';
				}

				if (in_array('pinterest', $swfw_social_networks)) {
					$swfw_html .= '<a href="https://pinterest.com/pin/create/button/?url=' . get_permalink() . '" target="_blank" class="swfw-social-share-button swfw-pinterest-share">';
					if ($swfw_social_networks_show_icon) {
						$swfw_html .= '<i class="fab fa-pinterest"></i>';
					}
					$swfw_html .= esc_html__('Pinterest', 'swfw') . '</a>';
				}

				if (in_array('twitter', $swfw_social_networks)) {
					$swfw_html .= '<a href="https://twitter.com/intent/tweet?url=' . get_permalink() . '" target="_blank" class="swfw-social-share-button swfw-twitter-share">';
					if ($swfw_social_networks_show_icon) {
						$swfw_html .= '<i class="fab fa-twitter"></i>';
					}
					$swfw_html .= esc_html__('Twitter', 'swfw') . '</a>';
				}

				if (in_array('copy_link', $swfw_social_networks)) {
					$swfw_wishlist_link = esc_url(get_permalink());
					$swfw_html .= '<a href="#" class="swfw-social-share-button swfw-copy-link-share" data-wishlist-link="' . $swfw_wishlist_link . '">';
					if ($swfw_social_networks_show_icon) {
						$swfw_html .= '<i class="fa fa-copy"></i>';
					}
					$swfw_html .= esc_html__('Copy Link', 'swfw') . '</a>';
				}
				$swfw_html .= '<p id="swfw-copy-message">' . esc_html__('Wishlist link copied to clipboard!', 'swfw') . '</p>';

				$swfw_html .= '</div>';
			}
			return $swfw_html;

	}

	/**
	 * Retrieves the variations of a product.
	 * 
	 * This function is used to get the variations of a product.
	 * It takes a product object as input and returns an array of variations.
	 * 
	 * @since 1.0.0
	 */
	function swfw_get_product_variations($swfw_product)
	{
		// Retrieve the variations of a variable product
		if ($swfw_product->is_type('variable')) {
			$swfw_variations = $swfw_product->get_available_variations();
			if (!empty($swfw_variations)) {
				$swfw_variation_names = array();
				foreach ($swfw_variations as $swfw_variation) {
					$swfw_variation_names[] = implode(', ', $swfw_variation['attributes']);
				}
				return implode('<br>', $swfw_variation_names);
			}
		}
		return '-';
	}
	/**
	 * Retrieves the stock status of a product.
	 * 
	 * This function is used to get the stock status of a product.
	 * It takes a product object as input and returns the stock information.
	 * 
	 * @since 1.0.0
	 */
	function swfw_get_product_stock($swfw_product)
	{
		// Retrieve the product stock status
		$swfw_stock_status = $swfw_product->get_stock_status();
		if ($swfw_stock_status === 'instock') {

			return esc_html__('In Stock', 'swfw');

		} elseif ($swfw_stock_status === 'outofstock') {

			return esc_html__('Out of Stock', 'swfw');
		}
		return '-';
	}
	/**
	 * Retrieves the date added of a product.
	 * 
	 * This function is used to get the date when a product was added.
	 * It takes a product ID as input and returns the date added.
	 * 
	 * @since 1.0.0
	 */
	function swfw_get_product_date_added($swfw_product_id)
	{
		// Get the user ID
		$swfw_user_id = get_current_user_id();

		// Get the existing wishlist array from user meta
		$swfw_wishlist = get_user_meta($swfw_user_id, 'swfw_smart_wishlist_meta', true);

		// Check if the wishlist exists and is not empty
		if ($swfw_wishlist && !empty($swfw_wishlist)) {
			// Check if the product ID exists in the wishlist
			if (in_array($swfw_product_id, $swfw_wishlist)) {
				// Get the index of the product ID in the wishlist array
				$swfw_index = array_search($swfw_product_id, $swfw_wishlist);

				// Get the corresponding date added from the user meta
				$swfw_wishlist_dates = get_user_meta($swfw_user_id, 'swfw_wishlist_date_added', true);

				if ($swfw_wishlist_dates && isset($swfw_wishlist_dates[$swfw_index])) {
					return $swfw_wishlist_dates[$swfw_index];
				}
			}
		}

		// Default case: return the current date if the date added is not found
		$swfw_current_date = date('Y-m-d'); // Get the current date
		return $swfw_current_date;
	}
}
