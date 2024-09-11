/*!
 * Helper script for Resign Button
 *
 * @handle nlmg-resign-block-helper
 * @deps jquery, wp-api-fetch
 */

/* global NlmgRedirect: false */

const $ = jQuery;
const { apiFetch } = wp;

$( document ).ready( function() {

	$( '.wp-block-nlmg-resign-button' ).each( function( i, div ) {
		const $button = $( div ).find( '.wp-block-button__link' );

		// Add disabled class.
		$button.addClass( 'disabled' );

		// Toggle button status with checkbox.
		$( '#nlmg-acceptance' ).on( 'click', function() {
			if ( this.checked ) {
				$button.removeClass( 'disabled' );
			} else {
				$button.addClass( 'disabled' );
			}
		} );

		// Resign button click event.
		$button.on( 'click', function( e ) {
			const $thisButton = $( this );
			e.preventDefault();
			if ( $thisButton.hasClass( 'disabled' ) ) {
				return false;
			}
			$thisButton.addClass( 'disabled' );
			const link = $thisButton.attr( 'href' ) || NlmgRedirect.url;
			apiFetch( {
				path: 'nlmg/v1/resign',
				method: 'POST',
			} ).then( ( res ) => {
				window.location.href = link;
			} ).catch( ( res ) => {
				// Display messages.
				// eslint-disable-next-line no-alert
				alert( res.message );
			} ).finally( () => {
				$thisButton.removeClass( 'disabled' );
			} );
		} );
	} );
} );
