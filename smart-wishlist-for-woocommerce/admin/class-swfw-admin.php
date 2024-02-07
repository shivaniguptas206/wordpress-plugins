<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://example.com/
 * @since      1.0.0
 *
 * @package    Swfw
 * @subpackage Swfw/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Swfw
 * @subpackage Swfw/admin
 * @author     Example <admin@example.com>
 */
class Swfw_Admin
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
	 * @param      string    $swfw_plugin_name       The name of this plugin.
	 * @param      string    $swfw_version    The version of this plugin.
	 */
	public function __construct($swfw_plugin_name, $swfw_version)
	{

		$this->swfw_plugin_name = $swfw_plugin_name;
		$this->swfw_version = $swfw_version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function swfw_enqueue_styles()
	{
		$swfw_current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
		// Check if we are on the plugin's settings page
		if ($swfw_current_page === 'swfw-wishlist-plugin') {
			// Load the JavaScript only when on the plugin's settings page
			wp_enqueue_style($this->swfw_plugin_name, plugin_dir_url(__FILE__) . 'css/swfw-admin.css', array(), $this->swfw_version, 'all');
		}
	}

	/**
	 * Register the scripts for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function swfw_enqueue_scripts()
	{
		$swfw_current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
		// Check if we are on the plugin's settings page
		if ($swfw_current_page === 'swfw-wishlist-plugin') {
			// Load the JavaScript only when on the plugin's settings page
			wp_enqueue_script('swfw-admin', plugin_dir_url(__FILE__) . 'js/swfw-admin.js', array('jquery'), '1.0.0', false);
		}
	}

	/**
	 * Callback function for adding a menu page for the plugin.
	 *
	 * This function adds a menu page for the plugin in the WordPress admin menu.
	 * It defines the menu title, label, capability required to access the page,
	 * the unique slug for the page, the callback function to display the page,
	 * the icon for the menu item, and the position of the menu item in the admin menu.
	 * @since    1.0.0
	 */
	function swfw_wishlist_plugin_add_menu_page()
	{
		add_menu_page(
			esc_html__('Wishlist Plugin', 'swfw'),
			// The title of the menu page
			esc_html__('Smart Wishlist', 'swfw'),
			// The label displayed in the admin menu with orange color and bold font
			'manage_options',
			// The capability required to access the menu page
			'swfw-wishlist-plugin',
			// The unique slug for the menu page
			array($this, 'swfw_wishlist_plugin_settings_page'),
			// The callback function to display the settings page
			'dashicons-heart',
			// The icon for the menu page (make sure Dashicons library is properly loaded)
			20, // The position of the menu page in the admin menu
		);
	}
	/**
	 * Adds a custom menu class to the Wishlist Plugin menu item in the WordPress admin menu.
	 *
	 * @since 1.0.0
	 *
	 * This function iterates over the menu items and checks for the Wishlist Plugin menu item.
	 * Once found, it adds the 'swfw-plugin-menu' class to the menu item's existing class attribute.
	 * This allows for targeted styling or functionality for the Wishlist Plugin menu item.
	 */
	function swfw_add_custom_menu_class()
	{
		global $menu;

		foreach ($menu as $swfw_key => $swfw_item) {
			if ($swfw_item[2] === 'swfw-wishlist-plugin') {
				$menu[$swfw_key][4] .= ' swfw-plugin-menu';
				break;
			}
		}
	}

	/**
	 * Callback function for registering plugin settings.
	 *
	 * This function registers the settings and adds sections and fields for various options.
	 * It is responsible for defining the structure and functionality of the plugin's settings page.
	 * Each section represents a specific group of settings, and each field represents an individual setting option.
	 * The settings are registered using the WordPress settings API functions such as register_setting(),
	 * add_settings_section(), and add_settings_field().
	 * @since    1.0.0
	 */
	function swfw_register_settings()
	{
		// Register settings for General Settings
		register_setting('swfw_general_settings', 'swfw_general_settings_fields');
		add_settings_section('swfw_general_settings_section', '', array($this, 'swfw_general_settings_section_callback'), 'swfw_general_settings');
		
		add_settings_field('swfw_enable_wishlist_button', esc_html__('Enable Wishlist Button', 'swfw'), array($this, 'swfw_enable_wishlist_button_callback'), 'swfw_general_settings', 'swfw_general_settings_section');
		add_settings_field('swfw_wishlist_product_page', esc_html__('Display "Add to Wishlist" Button on Product Page', 'swfw'), array($this, 'swfw_wishlist_product_page_callback'), 'swfw_general_settings', 'swfw_general_settings_section');
		add_settings_field('swfw_wishlist_button_icon', esc_html__('Display Style for "Add to Wishlist" Button', 'swfw'), array($this, 'swfw_wishlist_options_callback'), 'swfw_general_settings', 'swfw_general_settings_section');
		add_settings_field('swfw_default_wishlist_name', esc_html__('Customize "Add To Wishlist" Text', 'swfw'), array($this, 'swfw_default_wishlist_name_callback'), 'swfw_general_settings', 'swfw_general_settings_section');
		add_settings_field('swfw_wishlist_page_filter', esc_html__('Select Wishlist Page', 'swfw'), array($this, 'swfw_wishlist_page_filter_callback'), 'swfw_general_settings', 'swfw_general_settings_section');

		// Register settings for Add To Wishlist Options Section

		register_setting('swfw_add_to_wishlist_options', 'swfw_add_to_wishlist_options_fields');
		add_settings_section('swfw_add_to_wishlist_options_section', '', array($this, 'swfw_add_to_wishlist_options_section_callback'), 'swfw_add_to_wishlist_options');
		add_settings_field('swfw_view_button_type_options', esc_html__('View button type options', 'swfw'), array($this, 'swfw_view_button_type_options_callback'), 'swfw_add_to_wishlist_options', 'swfw_add_to_wishlist_options_section');
		add_settings_field('swfw_view_wishlist_text', esc_html__('Customise "View wishlist" Text', 'swfw'), array($this, 'swfw_view_wishlist_text_callback'), 'swfw_add_to_wishlist_options', 'swfw_add_to_wishlist_options_section');
		add_settings_field('swfw_add_to_wishlist_icon_style', esc_html__('Customise "Add to Wishlist" Icon', 'swfw'), array($this, 'swfw_add_to_wishlist_icon_style_callback'), 'swfw_add_to_wishlist_options', 'swfw_add_to_wishlist_options_section');

		// Register settings for Wishlist Page Options Section

		register_setting('swfw_wishlist_page_options', 'swfw_wishlist_page_options_fields');
		add_settings_section('swfw_wishlist_page_options_section', '', array($this, 'swfw_wishlist_page_options_section_callback'), 'swfw_wishlist_page_options');
		add_settings_field('swfw_wishlist_table_columns', esc_html__('Wishlist Table Columns', 'swfw'), array($this, 'swfw_wishlist_table_columns_callback'), 'swfw_wishlist_page_options', 'swfw_wishlist_page_options_section');

		// Register settings for Social Networks Section

		register_setting('swfw_social_networks_settings', 'swfw_social_networks_settings_fields');
		add_settings_section('swfw_social_networks_settings_section', '', array($this, 'swfw_social_networks_settings_section_callback'), 'swfw_social_networks_settings');
		add_settings_field('swfw_enable_social_sharing_field', esc_html__('Enable Social Sharing', 'swfw'), array($this, 'swfw_enable_social_sharing_field_callback'), 'swfw_social_networks_settings', 'swfw_social_networks_settings_section');
		add_settings_field('swfw_social_networks_field', esc_html__('Select Social Networks', 'swfw'), array($this, 'swfw_social_networks_field'), 'swfw_social_networks_settings', 'swfw_social_networks_settings_section');
		add_settings_field('swfw_social_networks_show_icon', esc_html__('Show Social Network Icons', 'swfw'), array($this, 'swfw_social_networks_icon'), 'swfw_social_networks_settings', 'swfw_social_networks_settings_section');

		// Register settings for Style Section
		register_setting('swfw_style_settings', 'swfw_style_options');
		add_settings_section('swfw_style_settings_section', '', array($this, 'swfw_style_settings_section_callback'), 'swfw_style_settings');
		add_settings_field('swfw_enable_css_style_field', esc_html__('Enable Custom CSS', 'swfw'), array($this, 'swfw_enable_css_style_field_callback'), 'swfw_style_settings', 'swfw_style_settings_section');
		add_settings_field('swfw_button_radius_options', esc_html__('Change Button Radius', 'swfw'), array($this, 'swfw_button_radius_css_callback'), 'swfw_style_settings', 'swfw_style_settings_section');
		add_settings_field('swfw_button_color_options', esc_html__('Change Button Color', 'swfw'), array($this, 'swfw_button_color_css_callback'), 'swfw_style_settings', 'swfw_style_settings_section');
		add_settings_field('swfw_text_color_options', esc_html__('Change Font Color', 'swfw'), array($this, 'swfw_text_color_css_callback'), 'swfw_style_settings', 'swfw_style_settings_section');
		add_settings_field('swfw_button_text_size_options', esc_html__('Change Button Font Size', 'swfw'), array($this, 'swfw_button_text_size_css_callback'), 'swfw_style_settings', 'swfw_style_settings_section');
		add_settings_field('swfw_button_text_style_options', esc_html__('Change Button Font Style', 'swfw'), array($this, 'swfw_button_text_style_css_callback'), 'swfw_style_settings', 'swfw_style_settings_section');
		
	}

	/**
	 * Callback function for the "General Settings" section.
	 *
	 * This function displays the description for the General Settings section.
	 * You can add your custom description or content for the General Settings section here.
	 * @since    1.0.0
	 */
	function swfw_general_settings_section_callback()
	{
		// Add your General Settings description or content here.
		return;
	}

	/**
	 * Callback function for enabling the wishlist button option.
	 *
	 * This function is responsible for rendering a toggle switch input on a settings page. Users can use this toggle switch to enable or disable a specific feature, such as the wishlist button.
	 * It retrieves the current value of the "swfw_enable_wishlist_button" option from the plugin's settings and sets the initial state of the toggle switch based on that value.
	 * @since    1.0.0
	 */
	function swfw_enable_wishlist_button_callback(){
		$swfw_settings = get_option('swfw_general_settings_fields');
		$swfw_enable_wishlist_button = isset($swfw_settings['swfw_enable_wishlist_button']) ? sanitize_text_field($swfw_settings['swfw_enable_wishlist_button']) : '0';
		?>
		<div class="swfw-toggle-switch">
			<input type="checkbox" id="swfw_enable_wishlist_button"
				name="swfw_general_settings_fields[swfw_enable_wishlist_button]" value="1" <?php checked('1', $swfw_enable_wishlist_button); ?>>
			<label for="swfw_enable_wishlist_button" class="swfw-toggle-slider"></label>
		</div>
		<?php
	}

	/**
	 * Callback function for the "Wishlist Product Page" field.
	 *
	 * This function displays the HTML code for the checkbox option to enable/disable the wishlist product page.
	 * It retrieves the current value of the "wishlist_product_page" option from the plugin's settings.
	 * The checkbox is checked or unchecked based on the saved option value.
	 * @since    1.0.0
	 */
	function swfw_wishlist_product_page_callback()
	{
		$swfw_general_settings = get_option('swfw_general_settings_fields');
		$swfw_wishlist_product_page = isset($swfw_general_settings['swfw_wishlist_product_page']) ? sanitize_text_field($swfw_general_settings['swfw_wishlist_product_page']) : false;
		?>
		<label class="swfw-toggle-switch">
			<input type="checkbox" name="swfw_general_settings_fields[swfw_wishlist_product_page]" value="1" <?php checked(1, $swfw_wishlist_product_page); ?>>
			<span class="swfw-toggle-slider"></span>
		</label>
		<?php
	}

	/**
	 * Callback function for the "Show Wishlist Button Types" field.
	 *
	 * This function displays the input field for the select wishlist name option.
	 * It retrieves the default wishlist name value from the options and displays it in the input field.
	 *
	 * @since 1.0.0
	 */
	function swfw_wishlist_options_callback()
	{

		$swfw_options = get_option('swfw_general_settings_fields');
		$swfw_wishlist_button = isset($swfw_options['swfw_wishlist_button']) ? sanitize_text_field($swfw_options['swfw_wishlist_button']) : '';

		?>
		<div class="swfw-wishlists-buttons-options">
			<label>
				<input type="radio" name="swfw_general_settings_fields[swfw_wishlist_button]" value="swfw_show_wishlist_button"
					<?php checked(empty($swfw_wishlist_button) || $swfw_wishlist_button === 'swfw_show_wishlist_button', true); ?>>
				<?php esc_html_e('Show "Add To Wishlist" Button', 'swfw'); ?>
			</label><br/>
			<label>
				<input type="radio" name="swfw_general_settings_fields[swfw_wishlist_button]" value="swfw_show_wishlist_icon"
					<?php checked($swfw_wishlist_button, 'swfw_show_wishlist_icon'); ?>>
				<?php esc_html_e('Show "Add To Wishlist" Icon', 'swfw'); ?>
			</label><br/>
			<label>
				<input type="radio" name="swfw_general_settings_fields[swfw_wishlist_button]" value="swfw_show_wishlist_both"
					<?php checked($swfw_wishlist_button, 'swfw_show_wishlist_both'); ?>>
				<?php esc_html_e('Show "Add To Wishlist" Icon and Text Both', 'swfw'); ?>
			</label>
		</div>

		<?php
	}

	/**
	 * Callback function for the "Default Wishlist Name" field.
	 *
	 * This function displays the input field for the default wishlist name option.
	 * It retrieves the default wishlist name value from the options and displays it in the input field.
	 * @since    1.0.0
	 */
	function swfw_default_wishlist_name_callback()
	{
		$swfw_default_name = get_option('swfw_general_settings_fields');
		$swfw_wishlist_name = isset($swfw_default_name['swfw_name_string']) ? $swfw_default_name['swfw_name_string'] : '';

		// Sanitize the input value
		$swfw_wishlist_name = sanitize_text_field($swfw_wishlist_name);
		?>
		<input class="swfw-input-field" id="swfw_default_wishlist_name" name="swfw_general_settings_fields[swfw_name_string]"
			type="text" value="<?php echo esc_attr__($swfw_wishlist_name, 'swfw'); ?>" />
		<?php
	}

	/**
	 * Callback function for the "Select Wishlist Page" field.
	 *
	 * This function displays the select field for choosing a page to show the wishlist on.
	 * It retrieves the selected page from the options and displays a list of pages in the select field.
	 * It also provides a link to create a new page and add the shortcode [swfw_smart_wishlist].
	 * @since    1.0.0
	 */
	function swfw_wishlist_page_filter_callback()
	{
		$swfw_selected_page = get_option('swfw_general_settings_fields');
		$swfw_wishlist_page = isset($swfw_selected_page['swfw_page_show']) ? sanitize_text_field($swfw_selected_page['swfw_page_show']) : '';
		$swfw_previous_wishlist_page = isset($swfw_selected_page['swfw_previous_page_show']) ? sanitize_text_field($swfw_selected_page['swfw_previous_page_show']) : '';

		// Sanitize the input values
		$swfw_wishlist_page = sanitize_text_field($swfw_wishlist_page);

		// Store the previous page ID
		$swfw_selected_page['swfw_previous_page_show'] = $swfw_wishlist_page;

		$swfw_pages = get_pages();

		if (!empty($swfw_pages)) {
			?>
			<select id="swfw_wishlist_page_filter" name="swfw_general_settings_fields[swfw_page_show]" class="swfw-input-field">
				<?php
				// Check if wishlist page exists
				$swfw_wishlist_page_exists = false;
				foreach ($swfw_pages as $swfw_page) {
					$swfw_selected = ($swfw_wishlist_page == $swfw_page->ID) ? 'selected="selected"' : '';

					if ($swfw_page->post_name === 'wishlist') {
						$swfw_wishlist_page_exists = true;
						?>
						<option value="<?php echo esc_attr($swfw_page->ID); ?>" <?php echo esc_attr($swfw_selected); ?>><?php echo esc_html__($swfw_page->post_title, 'swfw'); ?></option>
						<?php
					}
				}
				// If wishlist page doesn't exist, add it as the first option
				if (!$swfw_wishlist_page_exists) {
					$swfw_wishlist_page = get_page_by_path('wishlist');
					if ($swfw_wishlist_page) {
						?>
						<option value="<?php echo esc_attr($swfw_wishlist_page->ID); ?>" selected="selected"><?php echo esc_html__($swfw_wishlist_page->post_title, 'swfw'); ?></option>
						<?php
					}
				}

				// Show the rest of the pages
				foreach ($swfw_pages as $swfw_page) {
					if ($swfw_page->post_name !== 'wishlist') {
						$swfw_selected = ($swfw_wishlist_page == $swfw_page->ID) ? 'selected="selected"' : '';
						?>
						<option value="<?php echo esc_attr($swfw_page->ID); ?>" <?php echo esc_attr($swfw_selected); ?>><?php echo esc_html__($swfw_page->post_title, 'swfw'); ?></option>
						<?php
					}
				}
				?>
			</select>
			<?php
		} else {
			?>
			<p>
				<?php echo esc_html__('No pages found.', 'swfw'); ?>
			</p>
			<?php
		}
		?>
		<p class="swfw-description">
			<?php echo sprintf(
				esc_html__('If you want to create a new page, %s and add the Shortcode [swfw_smart_wishlist].', 'swfw'),
				'<a href="' . esc_url(admin_url('post-new.php?post_type=page')) . '">' . esc_html__('Click Here', 'swfw') . '</a>'
			); ?>
		</p>
		<?php
		// Check if the wishlist page is active by default and show it first
		if (empty($swfw_wishlist_page)) {
			$swfw_pages = get_pages();

			if (!empty($swfw_pages)) {
				$swfw_default_page = $swfw_pages[0];
				$swfw_default_page_id = $swfw_default_page->ID;
				$swfw_selected_page['swfw_page_show'] = $swfw_default_page_id;
			}
		}
		// Update the option outside the function
		update_option('swfw_general_settings_fields', $swfw_selected_page);
	}

	/**
	 * Callback function for the "Add to Wishlist Options" section.
	 *
	 * This function displays the HTML code for the swfw_add_to_wishlist_options section.
	 * Add your swfw_add_to_wishlist_options section HTML code here.
	 * @since    1.0.0
	 */
	function swfw_add_to_wishlist_options_section_callback()
	{
		return;
	}
	/**
	 * Callback function for the "After Product is Added to Wishlist" setting.
	 *
	 * This function displays the options for what to show after a product is added to the wishlist.
	 * @since    1.0.0
	 *
	 * 
	 */
	function swfw_view_button_type_options_callback()
	{
		$swfw_options = get_option('swfw_add_to_wishlist_options_fields');
		$swfw_selected_option = isset($swfw_options['swfw_view_button_type_options']) ? $swfw_options['swfw_view_button_type_options'] : 'swfw_show_view_wishlist';

		?>
		<form>
			<label><input type="radio" name="swfw_add_to_wishlist_options_fields[swfw_view_button_type_options]"
					value="swfw_show_view_wishlist" <?php checked($swfw_selected_option, 'swfw_show_view_wishlist'); ?>> <?php echo esc_html__('Show "View Wishlist" Button', 'swfw'); ?></label><br>
			<label><input type="radio" name="swfw_add_to_wishlist_options_fields[swfw_view_button_type_options]"
					value="swfw_show_view_wishlist_icon" <?php checked($swfw_selected_option, 'swfw_show_view_wishlist_icon'); ?>> <?php echo esc_html__('Show "View Wishlist" Icon', 'swfw'); ?></label>
		</form>
		<?php
	}
	/**
	 * Callback function for the "View Wishlist Text" setting.
	 *
	 * This function displays an input field to Customise the text displayed for browsing the wishlist.
	 * @since    1.0.0
	 */
	function swfw_view_wishlist_text_callback()
	{
		$swfw_options = get_option('swfw_add_to_wishlist_options_fields');
		$swfw_view_wishlist_text = isset($swfw_options['swfw_view_wishlist_text']) ? sanitize_text_field($swfw_options['swfw_view_wishlist_text']) : '';
		?>
		<input type="text" class="swfw-input-field" name="swfw_add_to_wishlist_options_fields[swfw_view_wishlist_text]"
			value="<?php esc_attr_e($swfw_view_wishlist_text, 'swfw'); ?>">
		<?php
	}

	/**
	 * Callback function for the "Add to Wishlist Icon" setting.
	 *
	 * This function displays an input field to enter the icon class or URL for the "Add to Wishlist" button.
	 */
	function swfw_add_to_wishlist_icon_style_callback()
	{
		$swfw_options = get_option('swfw_add_to_wishlist_options_fields');
		$swfw_add_to_wishlist_icon_style = isset($swfw_options['swfw_add_to_wishlist_icon_style']) ? sanitize_text_field($swfw_options['swfw_add_to_wishlist_icon_style']) : '';
		?>
		<input type="text" class="swfw-input-field" name="swfw_add_to_wishlist_options_fields[swfw_add_to_wishlist_icon_style]"
			value="<?php echo esc_attr($swfw_add_to_wishlist_icon_style); ?>"
			placeholder="<?php esc_attr_e('E.g., fa fa-heart', 'swfw'); ?>" />
		<p class="swfw-description">
			<?php echo esc_html__('Enter the Font Awesome class for the icon to be displayed on the "Add to Wishlist" button. Only Font Awesome 4 & 5 classes are allowed (e.g., fa fa-heart, fas fa-heart).', 'swfw'); ?>
		</p>
		<?php
	}

	/**
	 * Callback function for the "Wishlist Page Options" section.
	 *
	 * This function displays the HTML code for the Wishlist Page Options section.
	 *
	 * 
	 */
	function swfw_wishlist_page_options_section_callback()
	{
		
		return;
	}
	/**
	 * Callback function for the "Wishlist Table Columns" field.
	 *
	 * This function displays the HTML code for the Wishlist Table Columns field, 
	 * allowing the user to select which columns to display in the wishlist table.
	 * @since    1.0.0
	 */
	function swfw_wishlist_table_columns_callback()
	{
		$swfw_options = get_option('swfw_wishlist_page_options_fields');
		$swfw_wishlist_columns = isset($swfw_options['swfw_wishlist_table_columns']) ? array_map('sanitize_text_field', (array) $swfw_options['swfw_wishlist_table_columns']) : array();

		$swfw_columns = array(
			'product_variations' => esc_html__('Product Variations', 'swfw'),
			'product_stock' => esc_html__('Product Stock', 'swfw'),
			'date_added' => esc_html__('Date Added', 'swfw'),
		);
		?>
		<fieldset>
			<?php foreach ($swfw_columns as $swfw_column => $swfw_label) {
				$swfw_checked = in_array($swfw_column, $swfw_wishlist_columns) ? 'checked="checked"' : '';
				?>
				<label>
					<input type="checkbox" name="swfw_wishlist_page_options_fields[swfw_wishlist_table_columns][]"
						value="<?php echo esc_attr($swfw_column); ?>" <?php echo esc_html($swfw_checked); ?>>
					<?php echo esc_html__($swfw_label, 'swfw'); ?>
				</label><br>
			<?php } ?>
		</fieldset>
		<?php
	}

	/**
	 * Callback function for the Social Networks Settings section.
	 *
	 * This function displays the description or additional content for the Social Networks Settings section in the WordPress admin panel.
	 * @since    1.0.0
	 *
	 */
	function swfw_social_networks_settings_section_callback()
	{
		
		return;
	}
	/**
	 * Callback function for the "Share Wishlist" field.
	 *
	 * This function displays the HTML code for the Share Wishlist field, allowing the user to enable or disable the sharing of the wishlist through social media platforms.
	 * @since    1.0.0
	 *
	 * 
	 */
	function swfw_enable_social_sharing_field_callback()
	{
		$swfw_settings = get_option('swfw_social_networks_settings_fields');
		$swfw_enable_social_sharing = isset($swfw_settings['swfw_enable_social_sharing']) ? sanitize_text_field($swfw_settings['swfw_enable_social_sharing']) : '0';
		?>
		<div class="swfw-toggle-switch">
			<input type="checkbox" id="swfw_enable_social_sharing"
				name="swfw_social_networks_settings_fields[swfw_enable_social_sharing]" value="1" <?php checked('1', $swfw_enable_social_sharing); ?>>
			<label for="swfw_enable_social_sharing" class="swfw-toggle-slider"></label>
		</div>
		<?php
	}

	/**
	 * Callback function for the "Share on Social Media" field.
	 *
	 * This function displays the HTML code for the Share on Social Media field, allowing the user to select which social media platforms to include in the wishlist sharing options.
	 * @since    1.0.0
	 *
	 * 
	 */
	function swfw_social_networks_field()
	{
		$swfw_settings = get_option('swfw_social_networks_settings_fields');
		$swfw_social_networks = isset($swfw_settings['swfw_social_networks']) ? array_map('sanitize_text_field', $swfw_settings['swfw_social_networks']) : array();

		$swfw_social_networks_list = array(
			'whatsapp' => esc_html__('WhatsApp', 'swfw'),
			'gmail' => esc_html__('Gmail', 'swfw'),
			'pinterest' => esc_html__('Pinterest', 'swfw'),
			'twitter' => esc_html__('Twitter', 'swfw'),
			'copy_link' => esc_html__('Copy Link', 'swfw'),
		);

		foreach ($swfw_social_networks_list as $swfw_key => $swfw_label) {
			$swfw_checked = in_array($swfw_key, $swfw_social_networks) ? 'checked="checked"' : '';
			?>
			<input type="checkbox" id="<?php echo esc_attr($swfw_key); ?>"
				name="swfw_social_networks_settings_fields[swfw_social_networks][]" value="<?php echo esc_attr($swfw_key); ?>" <?php echo esc_attr($swfw_checked); ?> />
			<label for="<?php echo esc_attr($swfw_key); ?>"><?php echo esc_html__($swfw_label, 'swfw'); ?></label><br>
			<?php
		}
	}

	/**
	 * Callback function for the "Social Networks Icon" field.
	 *
	 * This function displays the HTML code for the Social Networks Icon field, allowing the user to enable or disable the display of icons for social networks.
	 *
	 * @since 1.0.0
	 */
	function swfw_social_networks_icon()
	{
		$swfw_settings = get_option('swfw_social_networks_settings_fields');
		$swfw_social_networks_show_icon = isset($swfw_settings['swfw_social_networks_show_icon']) ? sanitize_text_field($swfw_settings['swfw_social_networks_show_icon']) : 0;
		?>
		<div class="swfw-toggle-switch">
			<input type="checkbox" id="swfw_social_networks_show_icon"
				name="swfw_social_networks_settings_fields[swfw_social_networks_show_icon]" value="1" <?php checked(1, $swfw_social_networks_show_icon); ?>>
			<label for="swfw_social_networks_show_icon" class="swfw-toggle-slider"></label>
		</div>
		<?php
	}

	/**
	 * Callback function for the "Style Settings" section.
	 *
	 * This function displays the Style Settings section in the plugin's settings page.
	 *
	 * @since 1.0.0
	 */
	function swfw_style_settings_section_callback()
	{
		
		return;
	}

	/**
	 * Callback function for the "Enable CSS Styles" field.
	 *
	 * This function displays a checkbox field allowing the user to enable or disable CSS styles.
	 *
	 * @since 1.0.0
	 */
	function swfw_enable_css_style_field_callback()
	{
		$swfw_options = get_option('swfw_style_options');
		$swfw_enable_css = isset($swfw_options['swfw_enable_css']) ? sanitize_text_field($swfw_options['swfw_enable_css']) : '';
		?>
		<div class="swfw-toggle-switch">
			<input type="checkbox" id="swfw_enable_css" name="swfw_style_options[swfw_enable_css]" value="1" <?php echo checked(1, $swfw_enable_css, false); ?>>
			<label for="swfw_enable_css" class="swfw-toggle-slider"></label>
		</div>
		<?php
	}

	/**
	 * Callback function for the "Button Radius" field.
	 *
	 * This function displays the HTML code for the Button Radius field, allowing the user to select the desired button radius.
	 *
	 * @since 1.0.0
	 */
	function swfw_button_radius_css_callback()
	{
		// Retrieve the saved values from the database
		$swfw_options = get_option('swfw_style_options');

		// Sanitize the saved value
		$swfw_button_radius = isset($swfw_options['swfw_button_radius']) ? sanitize_text_field($swfw_options['swfw_button_radius']) : '';

		// Input Text Field for Button Radius
		?>
		<input type="text" class="swfw-input-field" id="swfw_button_radius" name="swfw_style_options[swfw_button_radius]"
			value="<?php esc_attr_e($swfw_button_radius, 'swfw'); ?>" />
		<?php
	}

	/**
	 * Callback function for the "Button Color" field.
	 *
	 * This function displays the HTML code for the Button Color field, allowing the user to select the desired button color.
	 *
	 * @since 1.0.0
	 */
	function swfw_button_color_css_callback()
	{
		// Retrieve the saved values from the database
		$swfw_options = get_option('swfw_style_options');

		// Sanitize the saved value
		$swfw_button_color = isset($swfw_options['swfw_button_color']) ? sanitize_hex_color($swfw_options['swfw_button_color']) : '';

		// Checkbox value for disabling button color change
		$swfw_button_color_enable = isset($swfw_options['swfw_button_color_enable']) ? $swfw_options['swfw_button_color_enable'] : '';

		// Input Field with CSS Color Picker for Button Color
		?>
		<label for="swfw_button_color_enable">
			<input type="checkbox" id="swfw_button_color_enable" name="swfw_style_options[swfw_button_color_enable]" value="1"
				<?php checked(1, $swfw_button_color_enable); ?> />
			<?php esc_html_e('Enable Button Color Change', 'swfw'); ?>
		</label>
		<br />
		<br />
		<input type="color" class="swfw-input-field" id="swfw_button_color" name="swfw_style_options[swfw_button_color]"
			value="<?php echo esc_attr($swfw_button_color); ?>" <?php echo ($swfw_button_color_enable == 1) ? '' : ' '; ?> />
		<?php
	}

	/**
	 * Callback function for the "Text Color" CSS field.
	 *
	 * This function displays the HTML code for the Text Color CSS input field.
	 *
	 * @since    1.0.0
	 */
	function swfw_text_color_css_callback()
	{
		$swfw_options = get_option('swfw_style_options');
		$swfw_text_color_css = isset($swfw_options['swfw_text_color_css']) ? $swfw_options['swfw_text_color_css'] : '';
		$swfw_text_color_enable = isset($swfw_options['swfw_text_color_enable']) ? $swfw_options['swfw_text_color_enable'] : '';

		?>
		<label for="swfw_text_color_enable">
			<input type="checkbox" id="swfw_text_color_enable" name="swfw_style_options[swfw_text_color_enable]" value="1" <?php checked(1, $swfw_text_color_enable); ?> />
			<?php esc_html_e('Enable Text Color Change', 'swfw'); ?>
		</label>
		<br/>
		<br/>
		<input type="color" class="swfw-input-field" id="swfw_text_color_css" name="swfw_style_options[swfw_text_color_css]"
			value="<?php echo esc_attr($swfw_text_color_css); ?>" <?php echo ($swfw_text_color_enable == 1) ? '' : ''; ?> />
		<?php
	}

	/**
	 * Callback function for the "Button Text Size" field.
	 *
	 * This function displays the HTML code for the Button Text Size field, allowing the user to select the desired button text size.
	 *
	 * @since 1.0.0
	 */
	function swfw_button_text_size_css_callback()
	{
		// Retrieve the saved values from the database
		$swfw_options = get_option('swfw_style_options');

		// Sanitize the saved value
		$swfw_button_text_size = isset($swfw_options['swfw_button_text_size']) ? sanitize_text_field($swfw_options['swfw_button_text_size']) : '';

		// Input Text Field for Button Text Size
		?>
		<input type="text" class="swfw-input-field" id="swfw_button_text_size" name="swfw_style_options[swfw_button_text_size]"
			value="<?php esc_attr_e($swfw_button_text_size, 'swfw'); ?>" />
		<?php
	}

	/**
	 * Callback function for the "Button Text Style" field.
	 *
	 * This function displays the HTML code for the Button Text Style field, allowing the user to select the desired font style for the button text.
	 *
	 * @since 1.0.0
	 */
	function swfw_button_text_style_css_callback()
	{
		// Retrieve the saved values from the database
		$swfw_options = get_option('swfw_style_options');

		// Define the font style options
		$swfw_font_style_options = array(
			'normal' => esc_html__('Normal', 'swfw'),
			'italic' => esc_html__('Italic', 'swfw'),
			'oblique' => esc_html__('Oblique', 'swfw'),
		);

		// Sanitize and validate the saved option value
		$swfw_button_text_style = isset($swfw_options['swfw_button_text_style']) ? sanitize_text_field($swfw_options['swfw_button_text_style']) : '';

		// Select Field for Font Style
		?>
		<select class="swfw-input-field" id="swfw_button_text_style" name="swfw_style_options[swfw_button_text_style]">
			<?php foreach ($swfw_font_style_options as $swfw_value => $swfw_label): ?>
				<?php $swfw_selected = ($swfw_button_text_style == $swfw_value) ? 'selected="selected"' : ''; ?>
				<option value="<?php echo esc_attr($swfw_value); ?>" <?php echo esc_attr($swfw_selected); ?>><?php echo esc_html__($swfw_label, 'swfw'); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}
	
	/**
	 * Displays the plugin settings page.
	 *
	 * This function generates the HTML markup for the plugin settings page.
	 * It includes a welcome message, navigation tabs, and forms for each
	 * settings section based on the selected tab.
	 *
	 * @since 1.0.0
	 */
	function swfw_wishlist_plugin_settings_page()
	{
		if (!current_user_can('manage_options')) {
			return;
		}
		
		// Determine the active tab based on the URL parameter
		$swfw_active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'swfw-general';
		?>
		<div id="swfw-plugin-container">
			<div class="swfw-mode">
				<div class="swfw-container">
					<div class="swfw-welcome-container">
						<h1 class="swfw-welcome-title">
							<span class="swfw-animated-text ">
								<?php echo esc_html__('Welcome to Smart Wishlist for WooCommerce', 'swfw'); ?>
							</span>
						</h1>
						<p class="swfw-welcome-message">
							<?php echo esc_html__('Thank you for using the Smart Wishlist for WooCommerce plugin. This is the plugin settings page.', 'swfw'); ?>
						</p>
						<div class="swfw-wrap">
							<h1 class="swfw-wishlist-h1 swfw-neon-text">
								<?php echo esc_html__('Smart Wishlist Settings', 'swfw'); ?>
							</h1>
						</div>
					</div>
				</div>
				<br>
				<div class="swfw-toggle-description">
					<div class="swfw-toggle-switch-mode">
						<input id="swfw-toggle" class="swfw-toggle-input" type="checkbox">
						<label for="swfw-toggle" class="swfw-toggle-label"></label>
					</div>
					<h4 class="swfw-toggle">
						<?php esc_html_e('Dark Mode', 'swfw'); ?>
					</h4>
				</div>
				<?php
				// Display a success message if the settings were updated
				if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
					?>
					<div class="swfw-notice notice swfw-notice-success notice-success swfw-is-dismissible is-dismissible">
						<p>
							<?php echo esc_html__('Your changes have been saved.', 'swfw'); ?>
						</p>
					</div>
					<?php
				}
				?>
				<div class="swfw-nav-wrapper nav-tab-wrapper">
					<!-- General Settings tab -->
					<a href="?page=swfw-wishlist-plugin&tab=swfw-general"
						class="swfw-nav-tab nav-tab <?php echo esc_attr(($swfw_active_tab === 'swfw-general' || !isset($_GET['tab'])) ? 'swfw-nav-tab nav-tab-active' : ''); ?>">
						<?php echo esc_html__('General Settings', 'swfw'); ?></a>

					<!-- Add to Wishlist Options tab -->
					<a href="?page=swfw-wishlist-plugin&tab=swfw-add-options"
						class="swfw-nav-tab nav-tab <?php echo esc_attr(($swfw_active_tab === 'swfw-add-options') ? 'swfw-nav-tab nav-tab-active' : ''); ?>">
						<?php echo esc_html__('Add to Wishlist Options', 'swfw'); ?></a>

					<!-- Wishlist Page Options tab -->
					<a href="?page=swfw-wishlist-plugin&tab=swfw-wishlist-page"
						class="swfw-nav-tab nav-tab <?php echo esc_attr(($swfw_active_tab === 'swfw-wishlist-page') ? 'swfw-nav-tab nav-tab-active' : ''); ?>">
						<?php echo esc_html__('Wishlist Page Options', 'swfw'); ?></a>

					<!-- Social Networks tab -->
					<a href="?page=swfw-wishlist-plugin&tab=swfw-social-page"
						class="swfw-nav-tab nav-tab <?php echo esc_attr(($swfw_active_tab === 'swfw-social-page') ? 'swfw-nav-tab nav-tab-active' : ''); ?>">
						<?php echo esc_html__('Social Networks', 'swfw'); ?></a>

					<!-- CSS Style Features tab -->
					<a href="?page=swfw-wishlist-plugin&tab=swfw-css-style-page"
						class="swfw-nav-tab nav-tab <?php echo esc_attr(($swfw_active_tab === 'swfw-css-style-page') ? 'swfw-nav-tab nav-tab-active' : ''); ?>">
						<?php echo esc_html__('CSS Style Options', 'swfw'); ?></a>				
				</div>
				<?php
				// Display the appropriate form based on the active tab
				switch ($swfw_active_tab) {
					case 'swfw-general':
						?>
						<!-- General Settings form -->
						<form method="post" action="options.php" id="swfw-form-css">
							<?php
							settings_fields('swfw_general_settings');
							do_settings_sections('swfw_general_settings');
							wp_nonce_field('swfw_general_settings_nonce', 'swfw_general_settings_nonce');
							submit_button(esc_html__('Save Changes', 'swfw'), 'swfw-submit-button',  false);
							?>
						</form>
						<?php
						break;
					case 'swfw-add-options':
						?>
						<!-- Add to Wishlist Options form -->
						<form method="post" action="options.php" id="swfw-form-css">
							<?php
							settings_fields('swfw_add_to_wishlist_options');
							do_settings_sections('swfw_add_to_wishlist_options');
							wp_nonce_field('swfw_add_to_wishlist_options_nonce', 'swfw_add_to_wishlist_options_nonce');
							submit_button(esc_html__('Save Changes', 'swfw'), 'swfw-submit-button',  false);
							?>
						</form>
						<?php
						break;
					case 'swfw-wishlist-page':
						?>
						<!-- Wishlist Page Options form -->
						<form method="post" action="options.php" id="swfw-form-css">
							<?php
							settings_fields('swfw_wishlist_page_options');
							do_settings_sections('swfw_wishlist_page_options');
							wp_nonce_field('swfw_wishlist_page_options_nonce', 'swfw_wishlist_page_options_nonce');
							submit_button(esc_html__('Save Changes', 'swfw'), 'swfw-submit-button',  false);
							?>
						</form>
						<?php
						break;
					case 'swfw-social-page':
						?>
						<!-- Social Networks form -->
						<form method="post" action="options.php" id="swfw-form-css">
							<?php
							settings_fields('swfw_social_networks_settings');
							do_settings_sections('swfw_social_networks_settings');
							wp_nonce_field('swfw_social_networks_settings_nonce', 'swfw_social_networks_settings_wpnonce');
							submit_button(esc_html__('Save Changes', 'swfw'), 'swfw-submit-button',  false);
							?>
						</form>
						<?php
						break;
					case 'swfw-css-style-page':
						?>
						<!-- CSS Style Features form -->
						<form method="post" action="options.php" id="swfw-form-css">
							<?php
							settings_fields('swfw_style_settings');
							do_settings_sections('swfw_style_settings');
							wp_nonce_field('swfw_style_settings_nonce', 'swfw_style_settings_nonce');
							submit_button(esc_html__('Save Changes', 'swfw'), 'swfw-submit-button', false);
							?>
						</form>
						<?php
						break;
					default:
						// Default behavior when the tab is not recognized
						echo esc_html__('Invalid tab selection.', 'swfw');
						break;
				}
				?>
			</div>
		</div>
		<?php
	}
}