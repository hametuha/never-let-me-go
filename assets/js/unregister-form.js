/*!
 * Unregister form of Never Let Me Go helper.
 *
 * @package nlmg
 */

( function ( $ ) {

	'use strict';

	$( '#nlmg-acceptance' ).on( 'click', function() {
		if ( this.checked ) {
			$( '#nlmg-resign-button' ).attr( 'disabled', null );
		} else {
			$( '#nlmg-resign-button' ).attr( 'disabled', true );
		}
	} );


} )( jQuery );
