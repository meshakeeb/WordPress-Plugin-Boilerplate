<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 * @author     Your Name <email@example.com>
 */
class Plugin_Name_Installer {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function install() {

		//add_option( 'plugin_name_options', serialize( get_settings_defaults() ) );
		//add_option( 'plugin_name_setup', 'pending' );
		add_option( 'plugin_name_redirect_about', true );
		add_option( 'plugin_name_db_version', plugin_name()->db_version );
		add_option( 'plugin_name_version', plugin_name()->version );
	}
}
