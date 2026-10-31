/*
 * "Order sent" page (screen 07): open WhatsApp once; the button retries. If the
 * page is still showing a few seconds after the attempt (WhatsApp blocked or
 * not installed), switch to the fallback (screen 43) with a copyable message.
 */
( function () {
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
		if ( window.sessionStorage.getItem( key ) ) {
			return;
		}
		window.sessionStorage.setItem( key, '1' );
	} catch ( e ) {
		return;
	}
	window.setTimeout( function () {
		window.location.href = link.href;
		window.setTimeout( showFallback, 4000 );
	}, 800 );
} )();
