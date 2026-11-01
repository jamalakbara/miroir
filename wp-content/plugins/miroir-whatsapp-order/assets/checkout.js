/* Review page (screen 06): live WhatsApp message preview from the form fields. */
( function ( data ) {
	'use strict';

	var form = document.querySelector( '[data-wa-form]' );
	var preview = document.querySelector( '[data-wa-preview]' );
	if ( ! form || ! preview || ! data.lines ) {
		return;
	}

	function value( name ) {
		var field = form.elements[ name ];
		if ( ! field ) {
			return '';
		}
		if ( field.tagName === 'SELECT' ) {
			return field.options[ field.selectedIndex ].text.trim();
		}
		return field.value.trim();
	}

	/* Same {placeholder} templates as Miroir_WA_Order::fill() (WooCommerce > Settings > WhatsApp ordering). */
	function fill( template, vars ) {
		return template.replace( /\{(\w+)\}/g, function ( match, key ) {
			return Object.prototype.hasOwnProperty.call( vars, key ) ? vars[ key ] : match;
		} );
	}

	function render() {
		var lines = [ data.greeting, '' ].concat( data.lines, [
			'',
			data.subtotal,
			'',
			fill( data.labels.name, { name: value( 'wa_name' ) || data.placeholder.name } ),
			fill( data.labels.deliver, { city: value( 'wa_city' ) || data.placeholder.city, country: value( 'wa_country' ) } ),
			fill( data.labels.notes, { notes: value( 'wa_notes' ) || '—' } ),
		] );
		preview.textContent = lines.join( '\n' );
	}

	form.addEventListener( 'input', render );
	form.addEventListener( 'change', render );
} )( window.miroirWaCheckout || {} );
