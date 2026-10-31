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

	function format( template, args ) {
		var i = 0;
		return template.replace( /%(\d)\$s|%s/g, function ( match, n ) {
			return args[ n ? n - 1 : i++ ];
		} );
	}

	function render() {
		var lines = [ data.greeting, '' ].concat( data.lines, [
			'',
			data.subtotal,
			'',
			format( data.labels.name, [ value( 'wa_name' ) || data.placeholder.name ] ),
			format( data.labels.deliver, [ value( 'wa_city' ) || data.placeholder.city, value( 'wa_country' ) ] ),
			format( data.labels.notes, [ value( 'wa_notes' ) || '—' ] ),
		] );
		preview.textContent = lines.join( '\n' );
	}

	form.addEventListener( 'input', render );
	form.addEventListener( 'change', render );
} )( window.miroirWaCheckout || {} );
