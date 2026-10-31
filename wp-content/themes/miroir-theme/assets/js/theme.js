/* Miroir theme behaviour. Vanilla JS, no dependencies. */
( function () {
	'use strict';

	/* Product strips keep their card widths and scroll only when they overflow. */
	document.querySelectorAll( '[data-product-carousel]' ).forEach( function ( carousel ) {
		var track = carousel.querySelector( '[data-carousel-track]' );
		var controls = carousel.querySelector( '[data-carousel-controls]' );
		var previous = carousel.querySelector( '[data-carousel-prev]' );
		var next = carousel.querySelector( '[data-carousel-next]' );
		var drag = null;
		var suppressClick = false;

		function update() {
			var end = track.scrollWidth - track.clientWidth;
			controls.hidden = end <= 1;
			track.classList.toggle( 'is-draggable', end > 1 );
			previous.disabled = track.scrollLeft <= 1;
			next.disabled = track.scrollLeft >= end - 1;
		}

		function move( direction ) {
			var card = track.firstElementChild;
			if ( ! card ) {
				return;
			}
			var gap = parseFloat( window.getComputedStyle( track ).columnGap ) || 0;
			var step = card.getBoundingClientRect().width + gap;
			var count = Math.max( 1, Math.round( ( track.clientWidth + gap ) / step ) );
			track.scrollBy( {
				left: direction * step * count,
				behavior: window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ? 'instant' : 'smooth'
			} );
		}

		previous.addEventListener( 'click', function () { move( -1 ); } );
		next.addEventListener( 'click', function () { move( 1 ); } );

		/* Touch keeps native swiping; mouse dragging starts after a small threshold. */
		track.addEventListener( 'pointerdown', function ( event ) {
			if ( event.pointerType !== 'mouse' || event.button !== 0 || track.scrollWidth <= track.clientWidth + 1 ) {
				return;
			}
			suppressClick = false;
			drag = { id: event.pointerId, x: event.clientX, left: track.scrollLeft, active: false };
		} );
		window.addEventListener( 'pointermove', function ( event ) {
			if ( ! drag || event.pointerId !== drag.id ) {
				return;
			}
			var distance = event.clientX - drag.x;
			if ( ! drag.active && Math.abs( distance ) < 6 ) {
				return;
			}
			if ( ! drag.active ) {
				drag.active = true;
				suppressClick = true;
				track.classList.add( 'is-dragging' );
				track.setPointerCapture( drag.id );
			}
			event.preventDefault();
			track.scrollLeft = drag.left - distance;
		} );
		function finishDrag( event ) {
			if ( ! drag || ( event.pointerId !== undefined && event.pointerId !== drag.id ) ) {
				return;
			}
			var id = drag.id;
			drag = null;
			track.classList.remove( 'is-dragging' );
			if ( track.hasPointerCapture( id ) ) {
				track.releasePointerCapture( id );
			}
			// The release click follows pointerup synchronously; later clicks work normally.
			window.setTimeout( function () { suppressClick = false; }, 0 );
		}
		window.addEventListener( 'pointerup', finishDrag );
		window.addEventListener( 'pointercancel', finishDrag );
		window.addEventListener( 'blur', finishDrag );
		track.addEventListener( 'lostpointercapture', finishDrag );
		track.addEventListener( 'dragstart', function ( event ) {
			if ( drag ) {
				event.preventDefault();
			}
		} );
		track.addEventListener( 'click', function ( event ) {
			if ( suppressClick ) {
				event.preventDefault();
				event.stopImmediatePropagation();
			}
		}, true );
		track.addEventListener( 'scroll', update, { passive: true } );
		track.addEventListener( 'keydown', function ( event ) {
			if ( event.target === track && ( event.key === 'ArrowLeft' || event.key === 'ArrowRight' ) ) {
				event.preventDefault();
				move( event.key === 'ArrowLeft' ? -1 : 1 );
			}
		} );
		if ( window.ResizeObserver ) {
			new ResizeObserver( update ).observe( track );
		} else {
			window.addEventListener( 'resize', update );
		}
		update();
	} );

	/*
	 * Header panels (screens 31-34) replace the page content between header and
	 * footer. Links fall back to ?panel=… reloads without JS.
	 */
	var main = document.getElementById( 'main' );

	function setExpanded( name ) {
		document.querySelectorAll( '[data-panel-open]' ).forEach( function ( toggle ) {
			toggle.setAttribute( 'aria-expanded', toggle.dataset.panelOpen === name ? 'true' : 'false' );
		} );
	}

	function openPanel( name ) {
		var panel = main && main.querySelector( '[data-panel="' + name + '"]' );
		if ( ! panel ) {
			return false;
		}
		main.querySelectorAll( '[data-panel]' ).forEach( function ( other ) {
			other.hidden = other !== panel;
		} );
		main.classList.add( 'has-panel' );
		setExpanded( name );
		window.scrollTo( 0, 0 );
		var title = panel.querySelector( '.flow__title' );
		if ( title ) {
			title.setAttribute( 'tabindex', '-1' );
			title.focus( { preventScroll: true } );
		}
		return true;
	}

	function closePanels() {
		if ( new URLSearchParams( window.location.search ).has( 'panel' ) ) {
			var url = new URL( window.location.href );
			url.searchParams.delete( 'panel' );
			window.location.href = url.toString();
			return;
		}
		main.querySelectorAll( '[data-panel]' ).forEach( function ( panel ) {
			panel.hidden = true;
		} );
		main.classList.remove( 'has-panel' );
		setExpanded( '' );
	}

	document.addEventListener( 'click', function ( event ) {
		var opener = event.target.closest( '[data-panel-open]' );
		if ( opener && openPanel( opener.dataset.panelOpen ) ) {
			event.preventDefault();
			return;
		}
		if ( event.target.closest( '[data-panel-close]' ) ) {
			event.preventDefault();
			closePanels();
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' === event.key && main && main.classList.contains( 'has-panel' ) ) {
			closePanels();
		}
	} );

	/*
	 * Add to bag before picking a size: instead of WooCommerce's alert, the PDP
	 * goes to the "choose a size" state (screen 44); on that screen the select gets focus.
	 */
	document.addEventListener(
		'click',
		function ( event ) {
			var button = event.target.closest( '.variations_form .single_add_to_cart_button.disabled' );
			if ( ! button ) {
				return;
			}
			event.preventDefault();
			event.stopImmediatePropagation();
			var form = button.closest( 'form' );
			if ( form.classList.contains( 'state-buy' ) ) {
				var select = form.querySelector( 'select' );
				if ( select ) {
					select.focus();
				}
				return;
			}
			var url = new URL( window.location.href );
			url.searchParams.set( 'choose-size', '1' );
			window.location.href = url.toString();
		},
		true
	);

	/* Links to "#back" (e.g. "Back to product" on the size guide) go back a page. */
	document.addEventListener( 'click', function ( event ) {
		var back = event.target.closest( 'a[href="#back"]' );
		if ( back && window.history.length > 1 ) {
			event.preventDefault();
			window.history.back();
		}
	} );

	/* Password visibility toggle (login). */
	document.addEventListener( 'click', function ( event ) {
		var toggle = event.target.closest( '[data-password-toggle]' );
		if ( ! toggle ) {
			return;
		}
		var input = document.getElementById( toggle.getAttribute( 'aria-controls' ) );
		var show = input.type === 'password';
		input.type = show ? 'text' : 'password';
		toggle.setAttribute( 'aria-pressed', show ? 'true' : 'false' );
	} );

	/*
	 * Qty stepper: −/+ change the number input. Inside the bag form a change
	 * submits "update cart" (the lofi has no update button).
	 */
	function submitCart( input ) {
		var form = input.closest( '[data-cart-form]' );
		if ( ! form ) {
			return;
		}
		// WooCommerce's cart.js disables update_cart until an input changes and
		// routes the submit through its AJAX update when the button is marked clicked.
		var update = form.querySelector( '[name="update_cart"]' );
		update.disabled = false;
		update.setAttribute( 'clicked', 'true' );
		if ( form.requestSubmit ) {
			form.requestSubmit( update );
		} else {
			update.click();
		}
	}

	document.addEventListener( 'click', function ( event ) {
		var step = event.target.closest( '[data-qty-step]' );
		if ( ! step ) {
			return;
		}
		var input = step.closest( '[data-qty]' ).querySelector( 'input' );
		var min = input.min === '' ? 0 : parseInt( input.min, 10 );
		var max = input.max === '' ? Infinity : parseInt( input.max, 10 );
		var value = Math.min( max, Math.max( min, ( parseInt( input.value, 10 ) || 0 ) + parseInt( step.dataset.qtyStep, 10 ) ) );
		if ( String( value ) !== input.value ) {
			input.value = value;
			submitCart( input );
		}
	} );

	document.addEventListener( 'change', function ( event ) {
		if ( event.target.matches( '[data-qty] input' ) ) {
			submitCart( event.target );
		}
	} );

	/*
	 * "Load more" on listings: fetch the next page, append its cards, move the
	 * button to the following page (or drop it). Without JS the button is a
	 * plain link to the next page.
	 */
	document.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '[data-load-more]' );
		if ( ! button || button.getAttribute( 'aria-busy' ) === 'true' ) {
			return;
		}
		var list = document.querySelector( '[data-load-more-list]' );
		if ( ! list ) {
			return;
		}
		event.preventDefault();
		button.setAttribute( 'aria-busy', 'true' );

		fetch( button.href, { credentials: 'same-origin' } )
			.then( function ( response ) {
				return response.text();
			} )
			.then( function ( html ) {
				var doc = new DOMParser().parseFromString( html, 'text/html' );
				var nextList = doc.querySelector( '[data-load-more-list]' );
				var nextButton = doc.querySelector( '[data-load-more]' );
				if ( nextList ) {
					Array.prototype.forEach.call( nextList.children, function ( card ) {
						list.appendChild( document.importNode( card, true ) );
					} );
				}
				if ( nextButton ) {
					button.href = nextButton.href;
					button.removeAttribute( 'aria-busy' );
				} else {
					button.remove();
				}
			} )
			.catch( function () {
				window.location.href = button.href;
			} );
	} );
} )();
