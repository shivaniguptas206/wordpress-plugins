<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://example.com/
 * @since      1.0.0
 *
 * @package    Swfw
 * @subpackage Swfw/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Swfw
 * @subpackage Swfw/includes
 * @author     Example <admin@example.com>
 */
class Swfw
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Swfw_Loader    $swfw_loader    Maintains and registers all hooks for the plugin.
	 */
	protected $swfw_loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $swfw_plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $swfw_plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $swfw_version    The current version of the plugin.
	 */
	protected $swfw_version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('SWFW_VERSION')) {
			$this->swfw_version = SWFW_VERSION;
		} else {
			$this->swfw_version = '1.0.0';
		}
		$this->swfw_plugin_name = 'swfw';

		$this->swfw_load_dependencies();
		$this->swfw_set_locale();
		$this->swfw_define_admin_hooks();
		$this->swfw_define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Swfw_Loader. Orchestrates the hooks of the plugin.
	 * - Swfw_i18n. Defines internationalization functionality.
	 * - Swfw_Admin. Defines all hooks for the admin area.
	 * - Swfw_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function swfw_load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-swfw-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-swfw-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-swfw-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-swfw-public.php';

		$this->swfw_loader = new Swfw_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Swfw_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function swfw_set_locale()
	{

		$swfw_plugin_i18n = new Swfw_i18n();

		$this->swfw_loader->swfw_add_action('plugins_loaded', $swfw_plugin_i18n, 'swfw_load_plugin_textdomain');

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function swfw_define_admin_hooks()
	{
		$swfw_plugin_admin = new Swfw_Admin($this->swfw_get_plugin_name(), $this->swfw_get_version());

		$this->swfw_loader->swfw_add_action('admin_enqueue_scripts', $swfw_plugin_admin, 'swfw_enqueue_styles');
		$this->swfw_loader->swfw_add_action('admin_enqueue_scripts', $swfw_plugin_admin, 'swfw_enqueue_scripts');
		$this->swfw_loader->swfw_add_action('admin_menu', $swfw_plugin_admin, 'swfw_wishlist_plugin_add_menu_page');
		$this->swfw_loader->swfw_add_action('admin_init', $swfw_plugin_admin, 'swfw_register_settings');
		$this->swfw_loader->swfw_add_action('admin_menu', $swfw_plugin_admin, 'swfw_add_custom_menu_class');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function swfw_define_public_hooks()
	{
		$swfw_plugin_public = new Swfw_Public($this->swfw_get_plugin_name(), $this->swfw_get_version());

		$this->swfw_loader->swfw_add_action('wp_enqueue_scripts', $swfw_plugin_public, 'swfw_enqueue_styles');
		$this->swfw_loader->swfw_add_action('wp_enqueue_scripts', $swfw_plugin_public, 'swfw_enqueue_scripts');
		$this->swfw_loader->swfw_add_action('wp_enqueue_scripts', $swfw_plugin_public, 'swfw_change_button_css_style');
		$this->swfw_loader->swfw_add_action('woocommerce_after_shop_loop_item', $swfw_plugin_public, 'swfw_wishlistify_add_wishlist_button', 15);
		$this->swfw_loader->swfw_add_action('woocommerce_after_add_to_cart_button', $swfw_plugin_public, 'swfw_wishlistify_add_wishlist_button');
		$this->swfw_loader->swfw_add_action('wp_ajax_swfw_add_to_wishlist', $swfw_plugin_public, 'swfw_add_to_wishlist');
		$this->swfw_loader->swfw_add_action('wp_ajax_nopriv_swfw_add_to_wishlist', $swfw_plugin_public, 'swfw_add_to_wishlist');
		$this->swfw_loader->swfw_add_action('wp_ajax_swfw_remove_product_from_wishlist', $swfw_plugin_public, 'swfw_remove_product_from_wishlist');
		$this->swfw_loader->swfw_add_action('wp_ajax_nopriv_swfw_remove_product_from_wishlist', $swfw_plugin_public, 'swfw_remove_product_from_wishlist');
		$this->swfw_loader->swfw_add_action('template_redirect', $swfw_plugin_public, 'swfw_add_remove_shortcode');
		$this->swfw_loader->swfw_add_action('wp_ajax_swfw_add_multiple_to_cart', $swfw_plugin_public, 'swfw_add_multiple_to_cart');
		$this->swfw_loader->swfw_add_action('wp_ajax_nopriv_swfw_add_multiple_to_cart', $swfw_plugin_public, 'swfw_add_multiple_to_cart');
		$this->swfw_loader->swfw_add_action('wp_ajax_swfw_remove_multiple_products_from_wishlist', $swfw_plugin_public, 'swfw_remove_multiple_products_from_wishlist');
		$this->swfw_loader->swfw_add_action('wp_ajax_nopriv_swfw_remove_multiple_products_from_wishlist', $swfw_plugin_public, 'swfw_remove_multiple_products_from_wishlist');
		$this->swfw_loader->swfw_add_shortcode('swfw_smart_wishlist',$swfw_plugin_public, 'swfw_wishlist_product_shortcode');

	}
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function swfw_run()
	{
		$this->swfw_loader->swfw_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function swfw_get_plugin_name()
	{
		return $this->swfw_plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Swfw_Loader    Orchestrates the hooks of the plugin.
	 */
	public function swfw_get_loader()
	{
		return $this->swfw_loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function swfw_get_version()
	{
		return $this->swfw_version;
	}

}