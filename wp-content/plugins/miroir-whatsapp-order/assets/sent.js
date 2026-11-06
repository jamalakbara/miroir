/*
 * "Order sent" page (screen 07): open WhatsApp once, in the tab checkout.js opened
 * on submit; the button retries. Without that tab, redirect here instead; if this page
 * is still showing a few seconds later (WhatsApp blocked or not installed), switch to
 * the fallback (screen 43) with a copyable message.
 */
( function ( timing ) {
	'use strict';

	var link = document.querySelector( '[data-wa-open]' );
	var sent = document.querySelector( '[data-wa-sent]' );
	var fallback = document.querySelector( '[data-wa-fallback]' );

	function showFallback() {
		if ( sent && fallback && document.visibilityState === 'visible' ) {
			sent.hidden = true;
			fallback.hidden = false;
		}
	}

	var copy = document.querySelector( '[data-copy-message]' );
	if ( copy ) {
		copy.addEventListener( 'click', function () {
			var text = document.querySelector( '[data-wa-message]' ).textContent;
			var done = function () {
				document.querySelector( '[data-copied]' ).classList.add( 'is-visible' );
			};
			if ( navigator.clipboard ) {
				navigator.clipboard.writeText( text ).then( done );
			} else {
				var area = document.createElement( 'textarea' );
				area.value = text;
				document.body.appendChild( area );
				area.select();
				document.execCommand( 'copy' );
				area.remove();
				done();
			}
		} );
	}

	if ( ! link ) {
		return;
	}
	var key = 'miroir-wa-opened:' + window.location.pathname + window.location.search;
	try {
		window.sessionStorage.removeItem( 'miroir-wa-tab' );
		if ( window.sessionStorage.getItem( key ) ) {
			return;
		}
		window.sessionStorage.setItem( key, '1' );
	} catch ( e ) {
		return;
	}
	window.setTimeout( function () {
		// Without a waiting tab the browser may block this; stay with the same-tab redirect then.
		var tab = window.open( link.href, timing.windowName );
		if ( tab ) {
			// WhatsApp has its own tab, so this page staying visible says nothing about it opening.
			tab.focus();
			return;
		}
		window.location.href = link.href;
		window.setTimeout( showFallback, timing.fallbackDelay || 4000 );
	}, timing.openDelay === undefined ? 800 : timing.openDelay );
} )( window.miroirWaSent || {} );
