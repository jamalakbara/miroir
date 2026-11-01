/* PDP: keep the "Order via WhatsApp" link in step with the chosen size, quantity and price. */
( function ( $, data ) {
	'use strict';

	var $button = $( '[data-wa-product]' );
	if ( ! $button.length || ! data.number ) {
		return;
	}

	/* Product line template from WooCommerce > Settings > WhatsApp ordering, as in Miroir_WA_Order::line(). */
	function line( vars ) {
		return data.line.replace( /\{(\w+)\}/g, function ( match, key ) {
			return Object.prototype.hasOwnProperty.call( vars, key ) ? vars[ key ] : match;
		} );
	}

	var size = data.variant;
	var unit = data.price;
	var $qty = $( '.buy-form input[name="quantity"]' );
	var initial = $button.attr( 'href' );

	function update() {
		var qty = Math.max( 1, parseInt( $qty.val(), 10 ) || 1 );
		if ( qty === 1 && size === data.variant && unit === data.price ) {
			$button.attr( 'href', initial );
			return;
		}
		var text = data.greeting + '\n\n' + line( { n: 1, name: data.name, variant: size || data.oneSize, qty: qty, price: price( unit * qty ) } ) + '\n\n' + data.url;
		$button.attr( 'href', 'https://wa.me/' + data.number + '?text=' + encodeURIComponent( text ) );
	}

	function price( amount ) {
		var c = data.currency;
		var parts = Number( amount ).toFixed( c.decimals ).split( '.' );
		var number = parts[ 0 ].replace( /\B(?=(\d{3})+(?!\d))/g, c.thousands ) + ( parts[ 1 ] ? c.decimal + parts[ 1 ] : '' );
		return c.format.replace( '%1$s', c.symbol ).replace( '%2$s', number ).replace( / /g, ' ' );
	}

	$( '.variations_form' )
		.on( 'found_variation', function ( event, variation ) {
			size = $( this ).find( 'select[name^="attribute_"]' ).map( function () {
				var label = $( this ).closest( '.field' ).find( 'label' ).text().trim();
				return ( label ? label + ' ' : '' ) + $( this ).find( 'option:selected' ).text();
			} ).get().join( ', ' );
			unit = variation.display_price;
			update();
		} )
		.on( 'reset_data', function () {
			size = data.variant;
			unit = data.price;
			update();
		} );

	$qty.on( 'change', update );
} )( jQuery, window.miroirWaProduct || {} );
