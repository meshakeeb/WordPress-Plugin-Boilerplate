<?php
/**
 * The settings functionality of the plugin.
 *
 * This class defines all code necessary to have setting pages and manager.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 * @author     Your Name <email@example.com>
 */
class Plugin_Name_Settings extends Plugin_Name_Base {

	/**
	 * Option key.
	 * @var string
	 */
	private $key = null;

	/**
	 * Options holder.
	 * @var array
	 */
	private $options = null;

	/**
	 * CMB2 option page id.
	 * @var string
	 */
	private $cmb_id = null;

	/**
	 * CMB2 metabox
	 * @var CMB2
	 */
	private $cmb2 = null;

	/**
	 * The Constructor
	 */
	public function __construct() {

		$this->key = plugin_name()->setting_key;
		$this->cmb_id = $this->key . '_options';

		$this->add_action( 'admin_init', 'init' );
		$this->add_action( 'cmb2_admin_init', 'add_options' );

		add_action( 'admin_enqueue_scripts', array( 'CMB2_hookup', 'enqueue_cmb_css' ), 25 );
	}

	/**
	 * Initialize.
	 * @return void
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	public function display() {

		$active = ! empty( $_GET['plugin-name-tab'] ) ? $_GET['plugin-name-tab'] : 'general';

		echo '<div class="plugin-name-tab-wrapper wp-clearfix">';

		foreach ( $this->get_tabs() as $id => $title ) {
			$class = $id === $active ? 'active' : '';
			printf(
				'<a href="%1$s" class="%3$s">%2$s</a>',
				admin_url( 'admin.php?page=plugin-name-settings&plugin-name-tab=' . $id ),
				$title,
				$class
			);
		}

		echo '<input type="submit" id="plugin-name-submit-cmb" value="Save Changes" class="button-primary">';

		echo '</div>';

		$cmb = $this->cmb2;
		include_once( plugin_name()->admin_dir() . 'settings/' . $active . '.php' );

		cmb2_metabox_form( $this->cmb_id, $this->key, array(
			'save_button' => esc_html__( 'Save Changes', '_plugin_name' ),
		) );
	}

	/**
	 * Create option object and add settings
	 */
	function add_options() {

		// hook in our save notices
		add_action( 'cmb2_save_options-page_fields_' . $this->cmb_id, array( $this, 'settings_notices' ), 10, 2 );

		$this->cmb2 = new_cmb2_box( array(
			'id'         => $this->cmb_id,
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key ),
			),
		) );
	}

	/**
	 * Add notices
	 */
	public function settings_notices( $object_id, $updated ) {

		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}

		add_settings_error( $this->key . '-notices', '', esc_html__( 'Settings updated.', '_plugin_name' ), 'updated' );
		settings_errors( $this->key . '-notices' );
	}

	/**
	 * Get setting tabs
	 * @return array
	 */
	private function get_tabs() {

		return _plugin_name_filter( 'admin_setting_tabs',
			array(
				'general' => '<span class="dashicons dashicons-image-filter"></span>' . esc_html__( 'General', '_plugin_name' ),
			)
		);
	}

	// Getter ----------------------------------------------------

	/**
	 * Get Setting
	 *
	 * @param  string	$field_id
	 * @param  mixed	$default
	 *
	 * @return mixed
	 */
	public function get( $field_id = '', $default = false ) {

		$opts = $this->get_options();

		if ( isset( $opts[ $field_id ] ) ) {
			return false !== $opts[ $field_id ] ? $opts[ $field_id ] : $default;
		}

		return $default;
	}

	/**
	 * Get all settings
	 *
	 * @return array
	 */
	public function all() {
		return $this->get_options();
	}

	/**
	 * Get options once for use throughout the plugin cycle
	 *
	 * @return void
	 */
	public function get_options() {

		if ( is_null( $this->options ) && ! empty( $this->key ) ) {

			$this->options = get_option( $this->key, array() );
		}

		foreach ( $this->options as $key => $value ) {

			$this->options[ $key ] = $this->normalize_data( $value );
		}

		return (array) $this->options;
	}

	/**
	 * Normalize option data
	 *
	 * @param  mixed $value
	 * @return mixed
	 */
	private function normalize_data( $value ) {

		if ( 'true' === $value || 'on' === $value ) {
			$value = true;
		} elseif ( 'false' === $value || 'off' === $value ) {
			$value = false;
		} elseif ( '0' === $value || '1' === $value ) {
			$value = intval( $value );
		}

		return $value;
	}
}

// Init the setting manager.
plugin_name()->settings = new Plugin_Name_Settings;
