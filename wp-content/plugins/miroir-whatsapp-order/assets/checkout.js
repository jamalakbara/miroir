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
			var option = field.options[ field.selectedIndex ];
			return option && option.value ? option.text.trim() : '';
		}
		return field.value.trim();
	}

	/* Same {placeholder} templates as Miroir_WA_Order::fill() (WooCommerce > Settings > WhatsApp ordering). */
	function fill( template, vars ) {
		return template.replace( /\{(\w+)\}/g, function ( match, key ) {
			return Object.prototype.hasOwnProperty.call( vars, key ) ? vars[ key ] : match;
		} );
	}

	/* Same order as Miroir_WA_Order::full_address() callers. */
	function address() {
		return [
			value( 'wa_address' ) || data.placeholder.address,
			value( 'wa_address_2' ),
			value( 'wa_city' ) || data.placeholder.city,
			value( 'wa_state' ),
			value( 'wa_postcode' ),
			value( 'wa_country' ),
		].filter( Boolean ).join( ', ' );
	}

	/* Province list follows the country; countries without one hide the field. */
	function syncStates() {
		var select = form.elements.wa_state;
		if ( ! select ) {
			return;
		}
		var states = ( data.states || {} )[ form.elements.wa_country.value ] || {};
		var codes = Object.keys( states );
		var chosen = select.value;
		select.length = 1;
		codes.forEach( function ( code ) {
			select.add( new Option( states[ code ], code ) );
		} );
		select.value = states[ chosen ] ? chosen : '';
		select.disabled = ! codes.length;
		select.required = !! codes.length && !! data.requireState;
		select.closest( '.field' ).hidden = ! codes.length;
	}

	function render() {
		var lines = [ data.greeting, '' ].concat( data.lines, [
			'',
			data.subtotal,
		], data.discounts || [], [
			'',
			fill( data.labels.name, { name: value( 'wa_name' ) || data.placeholder.name } ),
			fill( data.labels.phone, { phone: value( 'wa_phone' ) || data.placeholder.phone } ),
			fill( data.labels.deliver, { address: address(), city: value( 'wa_city' ) || data.placeholder.city, country: value( 'wa_country' ) } ),
			fill( data.labels.notes, { notes: value( 'wa_notes' ) || '—' } ),
			fill( data.labels.ref, { ref: data.placeholder.ref } ),
		] );
		preview.textContent = lines.join( '\n' );
	}

	syncStates();
	form.elements.wa_country.addEventListener( 'change', syncStates );
	form.addEventListener( 'input', render );
	form.addEventListener( 'change', render );

	/* Coupons post the whole form, so typed details stay; submit() skips the WhatsApp tab handler below. */
	function applyCoupon() {
		var code = form.elements.coupon_code;
		if ( ! code.value.trim() ) {
			code.focus();
			return;
		}
		var flag = document.createElement( 'input' );
		flag.type = 'hidden';
		flag.name = 'apply_coupon';
		flag.value = '1';
		form.appendChild( flag );
		form.submit();
	}

	form.addEventListener( 'click', function ( event ) {
		if ( event.target.closest( '[data-wa-coupon-apply]' ) ) {
			applyCoupon();
			return;
		}
		var remove = event.target.closest( '[data-wa-coupon-remove]' );
		if ( remove ) {
			var url = new URL( form.getAttribute( 'action' ), window.location.href );
			url.searchParams.set( 'remove_coupon', remove.dataset.waCouponRemove );
			form.setAttribute( 'action', url.toString() );
			form.submit();
		}
	} );

	form.addEventListener( 'keydown', function ( event ) {
		if ( event.key === 'Enter' && event.target.name === 'coupon_code' ) {
			event.preventDefault();
			applyCoupon();
		}
	} );

	/*
	 * Browsers only allow a new tab during a click, and the WhatsApp link exists
	 * after the order is saved. Open the tab on submit; sent.js loads WhatsApp in it.
	 */
	var pendingTab = 'miroir-wa-tab';

	function remember( pending ) {
		try {
			if ( pending ) {
				window.sessionStorage.setItem( pendingTab, '1' );
			} else {
				window.sessionStorage.removeItem( pendingTab );
			}
		} catch ( e ) {}
	}

	function tabPending() {
		try {
			return !! window.sessionStorage.getItem( pendingTab );
		} catch ( e ) {
			return false;
		}
	}

	/* Back on this page after submitting means the order was refused: close the waiting tab. */
	if ( tabPending() ) {
		remember( false );
		var waiting = window.open( '', data.windowName );
		if ( waiting ) {
			waiting.close();
		}
	}

	form.addEventListener( 'submit', function () {
		if ( ! form.checkValidity() ) {
			return;
		}
		var tab = window.open( '', data.windowName );
		if ( tab ) {
			tab.document.title = data.opening;
			tab.document.body.textContent = data.opening;
			remember( true );
		}
	} );
} )( window.miroirWaCheckout || {} );
