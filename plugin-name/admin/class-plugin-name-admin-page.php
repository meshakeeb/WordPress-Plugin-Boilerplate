<?php
/**
 * The admin-page functionality.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Plugin_Name_Admin_Page extends Plugin_Name_Base {

	/**
	 * Unique ID used for menu_slug.
	 * @var string
	 */
	public $id = null;

	/**
	 * The text to be displayed in the title tags of the page.
	 * @var string
	 */
	public $title = null;

	/**
	 * The slug name for the parent menu.
	 * @var string
	 */
	public $parent = null;

	/**
	 * The The on-screen name text for the menu.
	 * @var string
	 */
	public $menu_title = null;

	/**
	 * The capability required for this menu to be displayed to the user.
	 * @var string
	 */
	public $capability = 'manage_options';

	/**
	 * The icon for this menu.
	 * @var string
	 */
	public $icon = 'dashicons-art';

	/**
	 * The position in the menu order this menu should appear.
	 * @var int
	 */
	public $position = -1;

	/**
	 * The function/file that displays the page content for the menu page.
	 * @var string|function
	 */
	public $render = null;

	/**
	 * The function that run on page POST to save data.
	 * @var fucntion
	 */
	public $onsave = null;

	/**
	 * The Constructor
	 *
	 * @param  string      $id
	 * @param  string      $title
	 * @param  array       $config
	 */
	public function __construct( $id, $title, $config = array() ) {

		// Check
		if ( ! $id ) {
			wp_die( esc_html__( '$id variable required', 'plugin-name' ), esc_html__( 'Variable Required', 'plugin-name' ) );
		}

		if ( ! $title ) {
			wp_die( esc_html__( '$title variable required', 'plugin-name' ), esc_html__( 'Variable Required', 'plugin-name' ) );
		}

		$this->id    = $id;
		$this->title = $title;
		$this->config( $config );

		if ( ! $this->menu_title ) {
			$this->menu_title = $title;
		}

		$this->add_action( 'init', 'init' );
	}

	/**
	 * Init admin page when WordPress Initialises.
	 * @return void
	 */
	public function init() {

		$priority = $this->parent ? intval( $this->position ) : -1;
		$this->add_action( 'admin_menu', 'register_menu', $priority );

		// If not the page is not this page stop here
		if ( ! $this->is_current_page() ) {
			return;
		}

		$this->add_action( 'admin_body_class', 'body_class' );

		if ( ! is_null( $this->onsave ) && is_callable( $this->onsave ) ) {
			$this->add_action( 'admin_init', 'save' );
		}
	}

	/**
	 * Register Admin Menu.
	 */
	public function register_menu() {

		// Parent Page
		if ( ! $this->parent ) {
			add_menu_page(
				$this->title, $this->menu_title, $this->capability, $this->id,
				array( $this, 'display' ), $this->icon, $this->position
			);

		// Child Page
		} else {
			add_submenu_page(
				$this->parent, $this->title, $this->menu_title, $this->capability,
				$this->id, array( $this, 'display' )
			);
		}
	}

	/**
	 * Render admin page content using render function you passed in config.
	 */
	public function display() {

		plugin_slug_action( 'before_admin_page', $this );
		plugin_slug_action( 'before_admin_page_' . $this->id, $this );

		if ( ! is_null( $this->render ) ) {

			if ( is_callable( $this->render ) ) {
				call_user_func( $this->onrender, $this );
			} else if ( is_string( $this->render ) ) {
				include_once $this->render;
			}
		}

		plugin_slug_action( 'admin_page_' . $this->id, $this );
		plugin_slug_action( 'admin_page', $this );
	}

	/**
	 * Add classes to <body> of wordpress admin.
	 * @param  string $classes
	 * @return string
	 */
	public function body_class( $classes = '' ) {
		return $classes . ' plugin-name-page';
	}

	/**
	 * Save anything you want using onsave function.
	 * @return void
	 */
	public function save() {
		call_user_func( $this->onsave, $this );
	}

	/**
	 * Is the page is currrent page
	 * @return boolean
	 */
	protected function is_current_page() {

		$page = isset( $_GET['page'] ) && ! empty( $_GET['page'] ) ? $_GET['page'] : false;
        return $page === $this->id;
	}
}
