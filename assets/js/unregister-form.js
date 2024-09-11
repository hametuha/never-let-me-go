/*!
 * Unregister form of Never Let Me Go helper.
 *
 * @handle nlmg-form
 * @deps jquery
 */

( function( $ ) {
	'use strict';

	$( '#nlmg-acceptance' ).on( 'click', function() {
		if ( this.checked ) {
			$( '#nlmg-resign-button' ).attr( 'disabled', null );
		} else {
			$( '#nlmg-resign-button' ).attr( 'disabled', true );
		}
	} );
}( jQuery ) );
