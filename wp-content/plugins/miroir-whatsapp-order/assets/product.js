/* PDP: keep the "Order via WhatsApp" link in step with the chosen size and price. */
( function ( $, data ) {
	'use strict';

	var $button = $( '[data-wa-product]' );
	if ( ! $button.length || ! data.number ) {
		return;
	}

	function link( size, price ) {
		var text = data.greeting + '\n\n1. ' + data.name + ' — ' + ( size || data.oneSize ) + ' × 1 — ' + price + '\n\n' + data.url;
		return 'https://wa.me/' + data.number + '?text=' + encodeURIComponent( text );
	}

	var initial = $button.attr( 'href' );

	function price( amount ) {
		var c = data.currency;
		var parts = Number( amount ).toFixed( c.decimals ).split( '.' );
		var number = parts[ 0 ].replace( /\B(?=(\d{3})+(?!\d))/g, c.thousands ) + ( parts[ 1 ] ? c.decimal + parts[ 1 ] : '' );
		return c.format.replace( '%1$s', c.symbol ).replace( '%2$s', number ).replace( / /g, ' ' );
	}

	$( '.variations_form' )
		.on( 'found_variation', function ( event, variation ) {
			var size = $( this ).find( 'select[name^="attribute_"]' ).map( function () {
				var label = $( this ).closest( '.field' ).find( 'label' ).text().trim();
				return ( label ? label + ' ' : '' ) + $( this ).find( 'option:selected' ).text();
			} ).get().join( ', ' );
			$button.attr( 'href', link( size, price( variation.display_price ) ) );
		} )
		.on( 'reset_data', function () {
			$button.attr( 'href', initial );
		} );
} )( jQuery, window.miroirWaProduct || {} );
