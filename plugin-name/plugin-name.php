<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Plugin_Name
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress Plugin Boilerplate
 * Plugin URI:        http://example.com/plugin-name-uri/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Your Name or Your Company
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       plugin-name
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Include Base Class.
 * From which all other classes are derived.
 */
include_once dirname( __FILE__ ) . '/includes/class-plugin-slug-base.php';

final class Plugin_Name extends Plugin_Name_Base {

	/**
	 * Plugin_Name version.
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * Plugin_Name database version.
	 * @return string
	 */
	public $db_version = '1';

	/**
	 * The single instance of the class.
	 * @var Plugin_Name
	 */
	protected static $_instance = null;

	/**
	 * Possible error message.
	 * @var null|WP_Error
	 */
	protected $error = null;

	/**
	 * Halt plugin loading.
	 * @var boolean
	 */
	private $is_critical = false;

	/**
	 * Minimum version of WordPress required to run the plugin.
	 * @var string
	 */
	public $wordpress_version = '3.8';

	/**
	 * Minimum version of PHP required to run the plugin.
	 * @var string
	 */
	public $php_version = '5.6';

	/**
	 * Plugin url.
	 * @var string
	 */
	private $plugin_url = null;

	/**
	 * Plugin path.
	 * @var string
	 */
	private $plugin_dir = null;

	/**
	 * Setting manager.
	 * @var WPIS_Settings
	 */
	public $settings = null;

	/**
	 * Setting option key.
	 * @var string
	 */
	public $setting_key = 'plugin_name_options';

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'plugin-name' ), $this->version );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'plugin-name' ), $this->version );
	}

	/**
	 * Main Plugin_Name instance.
	 *
	 * Ensure only one instance is loaded or can be loaded.
	 *
	 * @return Plugin_Name
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) && ! ( self::$_instance instanceof Plugin_Name ) ) {
			self::$_instance = new Plugin_Name();
			self::$_instance->setup();
		}

		return self::$_instance;
	}

	/**
	 * Plugin_Name constructor.
	 */
	private function __construct() {
	}

	/**
	 * Instantiate the plugin.
	 * @return void
	 */
	private function setup() {

		// Make sure the WordPress version is recent enough
		$this->is_wp_enough();

		// Make sure the PHP version is recent enough
		$this->is_php_enough();

		// If we have any error
		if ( ! is_null( $this->error ) && $this->error instanceof WP_Error ) {
			add_action( 'admin_notices', array( $this, 'display_error' ), 10 );
		}

		// If we have critical issue don't load the plugin
		if ( $this->is_critical ) {
			return;
		}

		$this->autoloader();
		$this->includes();
		$this->hooks();

		// For developers to hook
		plugin_slug_action( 'loaded' );
	}

	/**
	 * Register file autoloading mechanism.
	 * @return void
	 */
	private function autoloader() {
		if ( function_exists( '__autoload' ) ) {
			spl_autoload_register( '__autoload' );
		}
        spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 * @return void
	 */
	private function includes() {

		// Core
		include_once $this->plugin_dir() . 'includes/functions-plugin-name-helpers.php';
		include_once $this->plugin_dir() . 'includes/libs/cmb2/init.php';
		include_once $this->plugin_dir() . 'includes/class-plugin-name-settings.php';

		// Admin Only
		if ( is_admin() ) {
			include_once $this->plugin_dir() . 'admin/class-plugin-name-admin.php';
		}

		// Frontend Only
		else {
			include_once $this->plugin_dir() . 'public/class-plugin-name-frontend.php';
		}

		// AJAX Only
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

		}
	}

	/**
	 * Add hooks to begin.
	 * @return void
	 */
	private function hooks() {

		register_activation_hook( __FILE__, array( 'Plugin_Name_Installer', 'install' ) );

		if ( is_admin() ) {

			/**
			 * Redirect to about page.
			 *
			 * We don't use the 'was_setup' option for the redirection as
			 * if the install fails the first time this will create a redirect loop
			 * on the about page.
			 */
			if ( true === boolval( get_option( 'plugin_name_redirect_about', false ) ) ) {
				$this->add_action( 'init', 'redirect_to_welcome' );
			}
		}

		$this->add_action( 'plugins_loaded', 'load_plugin_textdomain' );
	}

	/**
	 * Autoload Strategy
	 * @param  string $class
	 * @return void
	 */
	public function autoload( $class ) {

		if ( ! plugin_name_str_start_with( 'Plugin_Name_', $class ) ) {
			return;
		}

		$path = $this->plugin_dir() . 'includes/';
		if ( plugin_name_str_start_with( 'Plugin_Name_Admin', $class ) ) {
			$path = $this->admin_dir();
		}

		$class = strtolower( $class );
		$file = 'class-' . str_replace( '_', '-', strtolower( $class ) ) . '.php';

		// Load File
		$load = $path . $file;
        if ( $load && is_readable( $load ) ) {
			include_once $load;
		}
	}

	/**
	 * Load the plugin text domain for translation.
	 * @return void
	 */
	public function load_plugin_textdomain() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'plugin-name' );

		load_textdomain(
			'plugin-name',
			WP_LANG_DIR . '/plugin-name/plugin-name-' . $locale . '.mo'
		);

		load_plugin_textdomain(
			'plugin-name',
			false,
			$this->plugin_dir() . '/languages/'
		);
	}

	// Helpers -----------------------------------------------------------

	/**
	 * Redirect to welcome page.
	 *
	 * Redirect the user to the welcome page after plugin activation.
	 * @return void
	 */
	public function redirect_to_welcome() {

		delete_option( 'plugin_name_redirect_about' );
		wp_redirect( admin_url( 'admin.php?page=plugin-name-about' ) );
		exit;
	}

	/**
	 * Check if WordPress version is enough to run this plugin.
	 * @return boolean
	 */
	public function is_wp_enough() {

		if ( version_compare( get_bloginfo( 'version' ), $this->wordpress_version, '<' ) ) {
			$this->add_error(
				sprintf( esc_html__( 'Plugin Name requires WordPress version %s or above. Please update WordPress to run this plugin.', 'plugin-name' ), $this->wordpress_version )
			);
			$this->is_critical = true;
		}
	}

	/**
	 * Check if PHP version is enough to run this plugin.
	 * @return boolean
	 */
	public function is_php_enough() {

		if ( version_compare( phpversion(), $this->php_version, '<' ) ) {
			$this->add_error(
				sprintf( esc_html__( 'Plugin Name requires PHP version %s or above. Please update PHP to run this plugin.', 'plugin-name' ), $this->php_version )
			);
			$this->is_critical = true;
		}
	}

	/**
	 * Add error.
	 *
	 * Add a new error to the WP_Error object
	 * and create object if it doesn't exists.
	 *
	 * @param string $message
	 * @param string $code
	 */
	public function add_error( $message, $code = 'error' ) {

		if ( is_null( $this->error ) && ! ( $this->error instanceof WP_Error ) ) {
			$this->error = new WP_Error();
		}

		$this->error->add( $code, $message );
	}

	/**
	 * Display error.
	 *
	 * Get all the error messages and display them in the admin notice.
	 * @return void
	 */
	public function display_error() {

		if ( is_null( $this->error ) || ! ( $this->error instanceof WP_Error ) ) {
			return;
		}

		$messages = $this->error->get_error_messages(); ?>

		<div class="error">
			<p>
				<?php
				if ( count( $messages ) > 1 ) {
					echo '<ul>';
					foreach ( $messages as $message ) {
						echo "<li>$message</li>";
					}
					echo '</li>';
				} else {
					echo $messages[0];
				}
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Get plugin directory.
	 * @return string
	 */
	public function plugin_dir() {

		if ( is_null( $this->plugin_dir ) ) {
			$this->plugin_dir = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/';
		}

		return $this->plugin_dir;
	}

	/**
	 * Get plugin uri.
	 * @return string
	 */
	public function plugin_url() {

		if ( is_null( $this->plugin_url ) ) {
			$this->plugin_url = untrailingslashit( plugin_dir_url( __FILE__ ) ) . '/';
		}

		return $this->plugin_url;
	}

	/**
	 * Get plugin admin directory.
	 * @return string
	 */
	public function admin_dir() {
		return $this->plugin_dir() . 'admin/';
	}

	/**
	 * Get plugin admin uri.
	 * @return string
	 */
	public function admin_url() {
		return $this->plugin_url() . 'admin/';
	}

	/**
	 * Get plugin public directory.
	 * @return string
	 */
	public function public_dir() {
		return $this->plugin_dir() . 'public/';
	}

	/**
	 * Get plugin public uri.
	 * @return string
	 */
	public function public_url() {
		return $this->plugin_url() . 'public/';
	}

	/**
	 * Get plugin version
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}
}

/**
 * Main instance of Plugin_Name.
 *
 * Returns the main instance of Plugin_Name to prevent the need to use globals.
 *
 * @return Plugin_Name
 */
function plugin_name() {
	return Plugin_Name::instance();
}

// Init the plugin.
plugin_name();
