<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Plugin_Name_Admin extends Plugin_Name_Base {

	/**
	 * The Constructor
	 */
	public function __construct() {

		$this->includes();
		$this->hooks();

		// For developers to hook
		_plugin_name_action( 'admin_loaded' );
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

		$this->add_action( 'init', 'init', 1 );
		$this->add_action( 'admin_enqueue_scripts', 'enqueue', 11 );
	}

	/**
	 * Initialize.
	 * @return void
	 */
	public function init() {

		$this->register_pages();
	}

	/**
	 * Enqueue Styles and Scripts required by plugin
	 * @return void
	 */
	public function enqueue() {

		$screen = get_current_screen();
		$pages = array(
			'toplevel_page_plugin-name',
			'plugin-name_page_plugin-name-settings',
		);

		if ( ! in_array( $screen->id, $pages ) ) {
			return;
		}

		// Styles
		wp_enqueue_style( 'plugin-name-admin', plugin_name()->admin_url() . 'assets/css/plugin-name-admin.css', null, null );

		// Scripts
		wp_enqueue_script( 'plugin-name-admin', plugin_name()->admin_url() . 'assets/js/plugin-name-admin.js', null, null, true );
	}

	/**
	 * Register admin pages for plugin.
	 * @return void
	 */
	private function register_pages() {

		// Welcome / About
		new Plugin_Name_Admin_Page( 'plugin-name', esc_html__( 'Plugin Name', '_plugin_name' ), array(
			'position'	=> 30,
			'render'	=> plugin_name()->admin_dir() . 'views/welcome.php',
		));

		// Setting
		new Plugin_Name_Admin_Page( 'plugin-name-settings', esc_html__( 'Settings', '_plugin_name' ), array(
			'parent'	=> 'plugin-name',
			'position'	=> 30,
			'render'	=> plugin_name()->admin_dir() . 'views/settings.php',
		));
	}
}

// Init the plugin admin.
new Plugin_Name_Admin;
