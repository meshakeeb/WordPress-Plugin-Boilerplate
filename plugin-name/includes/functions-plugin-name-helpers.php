<?php
/**
 * Helper Functions.
 *
 * This file contains functions need during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 * @author     Your Name <email@example.com>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// 1. STRING ----------------------------------------------------

/**
 * Check if the string begins with the given value
 *
 * @param  string	$needle   The sub-string to search for
 * @param  string	$haystack The string to search
 *
 * @return bool
 */
function _plugin_name_str_start_with( $needle, $haystack ) {
	return substr_compare( $haystack, $needle, 0, strlen( $needle ) ) === 0;
}

/**
 * Check if the string contains the given value
 *
 * @param  string	$needle   The sub-string to search for
 * @param  string	$haystack The string to search
 *
 * @return bool
 */
function _plugin_name_str_contains( $needle, $haystack ) {
	return strpos( $haystack, $needle ) !== false;
}

/**
 * Generate html attribute string for array
 *
 * @param  array 	$attributes
 * @return string
 */
function _plugin_name_attributes( $attributes = array(), $prefix = '' ) {

	// If empty return false
	if ( empty( $attributes ) ) {
		return false;
	}

	$out = '';
	foreach ( $attributes as $key => $value ) {

		$key = $prefix . $key;

		if ( true === $value ) {
			$value = 'true';
		}

		if ( false === $value ) {
			$value = 'false';
		}

		$out .= sprintf( ' %s="%s"', esc_html( $key ), esc_attr( $value ) );
	}

	return $out;
}
