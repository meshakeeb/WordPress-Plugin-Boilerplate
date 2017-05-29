<?php
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 * @author     Your Name <email@example.com>
 */
class Plugin_Name_Frontend extends Plugin_Name_Base {

	/**
	 * The Constructor
	 */
	public function __construct() {

		$this->includes();
		$this->hooks();

		// For developers to hook
		_plugin_name_action( 'frontend_loaded' );
	}

	/**
	 * Include required files.
	 * @return void
	 */
	private function includes() {

	}

	/**
	 * Setup hooks.
	 * @return void
	 */
	private function hooks() {

		$this->add_action( 'init', 'init' );
		$this->add_action( 'wp_enqueue_scripts', 'enqueue' );
	}

	/**
	 * Initialize.
	 * @return void
	 */
	public function init() {
	}

	/**
	 * Enqueue Styles and Scripts required by plugin
	 * @return void
	 */
	public function enqueue() {

		// Styles
		wp_enqueue_style( 'plugin-name', plugin_name()->public_url() . 'assets/css/plugin-name.css', null, null );

		// Scripts
		wp_enqueue_script( 'plugin-name', plugin_name()->public_url() . 'assets/js/plugin-name.js', null, null, true );
	}

}

// Init the plugin public.
new Plugin_Name_Frontend;
