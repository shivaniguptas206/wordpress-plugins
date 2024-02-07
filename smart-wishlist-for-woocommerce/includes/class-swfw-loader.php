<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://example.com/
 * @since      1.0.0
 *
 * @package    Swfw
 * @subpackage Swfw/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Swfw
 * @subpackage Swfw/includes
 * @author     Example <admin@example.com>
 */
class Swfw_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $swfw_actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $swfw_actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $swfw_filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $swfw_filters;

	/**
	 * The array of shortcode registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $swfw_shortcodes    The shortcode registered with WordPress to fire when the plugin loads.
	 */
	protected $swfw_shortcodes;

	/**
	 * Initialize the collections used to maintain the actions, filters and shortcode.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->swfw_actions = array();
		$this->swfw_filters = array();
		$this->swfw_shortcodes =array();

	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $swfw_hook             The name of the WordPress action that is being registered.
	 * @param    object               $swfw_component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $swfw_callback         The name of the function definition on the $component.
	 * @param    int                  $swfw_priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $swfw_accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function swfw_add_action( $swfw_hook, $swfw_component, $swfw_callback, $swfw_priority = 10, $swfw_accepted_args = 1 ) {
		$this->swfw_actions = $this->swfw_add( $this->swfw_actions, $swfw_hook, $swfw_component, $swfw_callback, $swfw_priority, $swfw_accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $swfw_hook             The name of the WordPress filter that is being registered.
	 * @param    object               $swfw_component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $swfw_callback         The name of the function definition on the $component.
	 * @param    int                  $swfw_priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $swfw_accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function swfw_add_filter( $swfw_hook, $swfw_component, $swfw_callback, $swfw_priority = 10, $swfw_accepted_args = 1 ) {
		$this->swfw_filters = $this->swfw_add( $this->swfw_filters, $swfw_hook, $swfw_component, $swfw_callback, $swfw_priority, $swfw_accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $swfw_hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $swfw_hook             The name of the WordPress filter that is being registered.
	 * @param    object               $swfw_component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $swfw_callback         The name of the function definition on the $component.
	 * @param    int                  $swfw_priority         The priority at which the function should be fired.
	 * @param    int                  $swfw_accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function swfw_add( $swfw_hooks, $swfw_hook, $swfw_component, $swfw_callback, $swfw_priority, $swfw_accepted_args ) {

		$swfw_hooks[] = array(
			'hook'          => $swfw_hook,
			'component'     => $swfw_component,
			'callback'      => $swfw_callback,
			'priority'      => $swfw_priority,
			'accepted_args' => $swfw_accepted_args
		);

		return $swfw_hooks;
	}

	/**
	* Add a new shortcode to the collection to be registered with WordPress.
	*
	* @since    1.0.0
	* @param    string   $swfw_shortcode   The name of the shortcode being registered.
	* @param    object   $swfw_component   A reference to the instance of the object on which the shortcode is defined.
	* @param    string   $swfw_callback    The name of the function definition on the $swfw_component.
	*/
	public function swfw_add_shortcode( $swfw_shortcode, $swfw_component, $swfw_callback ) {
		$this->swfw_shortcodes = $this->swfw_add_shortcode_internal( $this->swfw_shortcodes, $swfw_shortcode, $swfw_component, $swfw_callback );
	}
	
	/**
	 * A utility function that is used to register shortcodes into a single collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $swfw_shortcodes       The collection of shortcodes that is being registered.
	 * @param    string               $swfw_shortcode        The name of the shortcode being registered.
	 * @param    object               $swfw_component        A reference to the instance of the object on which the shortcode is defined.
	 * @param    string               $swfw_callback         The name of the function definition on the $component.
	 * @return   array                                           The collection of shortcodes registered.
	 */
	private function swfw_add_shortcode_internal( $swfw_shortcodes, $swfw_shortcode, $swfw_component, $swfw_callback ) {
		$swfw_shortcodes[] = array(
			'shortcode'   => $swfw_shortcode,
			'component'   => $swfw_component,
			'callback'    => $swfw_callback,
		);
	
		return $swfw_shortcodes;
	}
	/**
	 * Register the filters, actions and shortcode with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function swfw_run() {

		foreach ( $this->swfw_filters as $swfw_hook ) {
			add_filter( $swfw_hook['hook'], array( $swfw_hook['component'], $swfw_hook['callback'] ), $swfw_hook['priority'], $swfw_hook['accepted_args'] );
		}

		foreach ( $this->swfw_actions as $swfw_hook ) {
			add_action( $swfw_hook['hook'], array( $swfw_hook['component'], $swfw_hook['callback'] ), $swfw_hook['priority'], $swfw_hook['accepted_args'] );
		}
		foreach ( $this->swfw_shortcodes as $swfw_shortcode ) {
			add_shortcode( $swfw_shortcode['shortcode'], array( $swfw_shortcode['component'], $swfw_shortcode['callback'] ) );
		}
	}

}
