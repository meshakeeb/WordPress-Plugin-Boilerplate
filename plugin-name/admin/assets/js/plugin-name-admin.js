/*!
* Plugin Name
*
* @version 1.0.0
* @author  Your Name or Your Company
*/
;(function( $ ) {

	'use strict';

	// Document Ready
	$(function() {

		$( '.plugin-name-tab-wrapper > .button-primary' ).on( 'click', function() {

			$( '.cmb-form > .button-primary' ).trigger( 'click' );

			return false;
		});

		$( '.cmb-type-title' ).each( function() {
			$( this ).prev( '.cmb-row' ).addClass( 'cmb-type-title-next' );
		});
	});

})( jQuery );
