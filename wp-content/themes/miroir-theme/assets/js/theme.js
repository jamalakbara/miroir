/* Miroir theme behaviour. Vanilla JS, no dependencies. */
( function () {
	'use strict';

	/* Real photos get a skeleton; static design placeholders stay unchanged. */
	var watchedImages = new WeakSet();
	function watchImages( root ) {
		var images = root.querySelectorAll( '.ph--filled img' );
		images.forEach( function ( image ) {
			if ( watchedImages.has( image ) ) {
				return;
			}
			watchedImages.add( image );
			var media = image.closest( '.ph--filled' );
			var primary = ! image.classList.contains( 'ph__hover' );
			function settle() {
				var failed = image.naturalWidth === 0;
				image.classList.toggle( 'has-image-error', failed );
				if ( primary ) {
					media.classList.remove( 'is-image-loading' );
					media.classList.toggle( 'has-image-error', failed );
					media.removeAttribute( 'aria-busy' );
					if ( failed ) {
						media.setAttribute( 'role', 'img' );
						media.setAttribute( 'aria-label', image.alt );
					} else {
						media.removeAttribute( 'role' );
						media.removeAttribute( 'aria-label' );
					}
				}
			}
			image.addEventListener( 'load', settle );
			image.addEventListener( 'error', settle );
			if ( image.complete ) {
				settle();
			} else if ( primary ) {
				media.classList.add( 'is-image-loading' );
				media.setAttribute( 'aria-busy', 'true' );
			}
		} );
	}
	watchImages( document );
	// Load-more cards and WooCommerce cart updates can insert photos after startup.
	new MutationObserver( function ( records ) {
		records.forEach( function ( record ) {
			record.addedNodes.forEach( function ( node ) {
				if ( node.nodeType === 1 ) {
					watchImages( node.parentElement || node );
				}
			} );
		} );
	} ).observe( document.body, { childList: true, subtree: true } );

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
	 * Menu, search and currency use native modal dialogs; filters replace
	 * page content. Links fall back to ?panel=… reloads without JS.
	 */
	var main = document.getElementById( 'main' );
	var activeModal = null;
	var modalOpener = null;
	var modalScroll = null;
	var drawerPlaceholder = null;
	var drawerCloseTimer = null;
	var modalCloseTimer = null;

	function resetModalClosing( dialog ) {
		window.clearTimeout( modalCloseTimer );
		dialog.classList.remove( 'is-closing' );
	}

	function restoreDrawerNavbar( dialog ) {
		if ( dialog.classList.contains( 'panel--drawer' ) && drawerPlaceholder ) {
			drawerPlaceholder.replaceWith( dialog.querySelector( '.site-nav' ) );
			drawerPlaceholder = null;
			window.clearTimeout( drawerCloseTimer );
			dialog.classList.remove( 'is-closing' );
		}
	}

	function updateDrawerViewport() {
		if ( ! activeModal || ( ! activeModal.classList.contains( 'panel--drawer' ) && ! activeModal.classList.contains( 'panel--bag' ) ) ) {
			return;
		}
		// Mobile WordPress toolbars scroll away; reserve only their visible portion.
		var toolbar = document.getElementById( 'wpadminbar' );
		var rect = toolbar && toolbar.getBoundingClientRect();
		var offset = rect && rect.width && rect.height ? Math.max( 0, Math.min( rect.bottom, window.innerHeight ) ) : 0;
		activeModal.style.setProperty( '--panel-toolbar-offset', offset + 'px' );
	}
	window.addEventListener( 'resize', updateDrawerViewport );

	// Keep the native scrollbar in place while the background stays still.
	function preventBackgroundScroll( event ) {
		if ( ! activeModal ) {
			return;
		}
		var point = event.touches ? event.touches[0] : event;
		var rect = activeModal.getBoundingClientRect();
		var inside = point && point.clientX >= rect.left && point.clientX <= rect.right && point.clientY >= rect.top && point.clientY <= rect.bottom;
		if ( ! inside || ! activeModal.contains( event.target ) ) {
			event.preventDefault();
		}
	}
	document.addEventListener( 'wheel', preventBackgroundScroll, { passive: false } );
	document.addEventListener( 'touchmove', preventBackgroundScroll, { passive: false } );
	window.addEventListener( 'scroll', function () {
		// Also hold the position for keyboard scrolling and scrollbar dragging.
		if ( activeModal && modalScroll && ( window.scrollX !== modalScroll.x || window.scrollY !== modalScroll.y ) ) {
			window.scrollTo( { left: modalScroll.x, top: modalScroll.y, behavior: 'instant' } );
		}
	} );

	document.querySelectorAll( 'dialog[data-panel]' ).forEach( function ( dialog ) {
		// Keep dialogs outside page panels so the menu cannot hide them.
		document.body.appendChild( dialog );
		dialog.addEventListener( 'close', function () {
			restoreDrawerNavbar( dialog );
			// A replaced dialog's queued close event must not cancel the new one.
			if ( ! dialog.open ) {
				dialog.classList.remove( 'is-closing' );
			}
			if ( activeModal !== dialog ) {
				return;
			}
			resetModalClosing( dialog );
			activeModal = null;
			modalScroll = null;
			var visiblePanel = main && main.querySelector( '[data-panel]:not([hidden])' );
			setExpanded( visiblePanel ? visiblePanel.dataset.panel : '' );
			var url = new URL( window.location.href );
			if ( url.searchParams.get( 'panel' ) === dialog.dataset.panel ) {
				url.searchParams.delete( 'panel' );
				window.history.replaceState( null, '', url );
			}
			if ( dialog.dataset.panel === 'added' && url.searchParams.has( 'added' ) ) {
				url.searchParams.delete( 'added' );
				window.history.replaceState( null, '', url );
			}
			if ( modalOpener ) {
				if ( ! modalOpener.isConnected && modalOpener.matches( 'a.site-nav__bag' ) ) {
					modalOpener = document.querySelector( 'a.site-nav__bag' );
				}
				modalOpener.focus( { preventScroll: true } );
			}
		} );
		dialog.addEventListener( 'cancel', function ( event ) {
			event.preventDefault();
			closePanels();
		} );
		var backdropPress = false;
		function outside( event ) {
			var rect = dialog.getBoundingClientRect();
			return event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom;
		}
		dialog.addEventListener( 'pointerdown', function ( event ) { backdropPress = outside( event ); } );
		dialog.addEventListener( 'click', function ( event ) {
			if ( event.target === dialog && backdropPress && outside( event ) ) {
				closePanels();
			}
		} );
	} );
	document.querySelectorAll( '[data-panel-open="nav"], [data-panel-open="search"], [data-panel-open="currency"]' ).forEach( function ( toggle ) {
		toggle.setAttribute( 'aria-haspopup', 'dialog' );
	} );

	function setExpanded( name ) {
		document.querySelectorAll( '[data-panel-open]' ).forEach( function ( toggle ) {
			toggle.setAttribute( 'aria-expanded', toggle.dataset.panelOpen === name ? 'true' : 'false' );
			if ( toggle.dataset.panelOpen === 'nav' ) {
				toggle.setAttribute( 'aria-label', name === 'nav' ? toggle.dataset.closeLabel : toggle.dataset.menuLabel );
			}
		} );
	}

	function openPanel( name ) {
		var panel = document.querySelector( '[data-panel="' + name + '"]' );
		if ( ! panel ) {
			return false;
		}
		if ( panel.tagName === 'DIALOG' ) {
			if ( activeModal === panel ) {
				if ( name === 'nav' ) {
					if ( panel.classList.contains( 'is-closing' ) ) {
						window.clearTimeout( drawerCloseTimer );
						panel.classList.remove( 'is-closing' );
						setExpanded( name );
					} else {
						closePanels();
					}
				}
				return true;
			}
			if ( activeModal ) {
				// Replace the drawer with search/currency and keep its original opener.
				var modalUrl = new URL( window.location.href );
				if ( modalUrl.searchParams.get( 'panel' ) === activeModal.dataset.panel ) {
					modalUrl.searchParams.delete( 'panel' );
					window.history.replaceState( null, '', modalUrl );
				}
				resetModalClosing( activeModal );
				restoreDrawerNavbar( activeModal );
				activeModal.close();
			} else {
				modalOpener = document.activeElement;
				modalScroll = { x: window.scrollX, y: window.scrollY };
			}
			activeModal = panel;
			updateDrawerViewport();
			if ( name === 'nav' ) {
				var navbar = document.querySelector( '.site-nav' );
				if ( navbar ) {
					// Keep the same live navbar and toggle inside the modal focus scope.
					drawerPlaceholder = document.createElement( 'div' );
					drawerPlaceholder.style.height = navbar.getBoundingClientRect().height + 'px';
					navbar.before( drawerPlaceholder );
					panel.prepend( navbar );
					modalOpener = navbar.querySelector( '.site-nav__toggle' );
				}
				panel.querySelector( '.flow__col' ).scrollTop = 0;
			}
			panel.removeAttribute( 'open' );
			panel.showModal();
			if ( name === 'added' ) {
				panel.querySelector( '#added-panel-title' ).focus( { preventScroll: true } );
			}
			// Resolve the resting lines before starting their morph animation.
			if ( name === 'nav' ) {
				panel.querySelector( '.site-nav__menu-mark' ).getBoundingClientRect();
			}
			setExpanded( name );
			if ( name === 'nav' ) {
				panel.querySelector( '.site-nav__toggle' ).focus( { preventScroll: true } );
			}
			var field = name !== 'added' && panel.querySelector( 'input:not([type="hidden"]):not([type="radio"]), input:checked' );
			if ( field ) {
				field.focus( { preventScroll: true } );
			}
			return true;
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
		if ( activeModal ) {
			if ( activeModal.classList.contains( 'panel--drawer' ) && ! window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
				if ( activeModal.classList.contains( 'is-closing' ) ) {
					return;
				}
				var drawer = activeModal;
				drawer.classList.add( 'is-closing' );
				setExpanded( '' );
				drawerCloseTimer = window.setTimeout( function () { drawer.close(); }, 220 );
				return;
			}
			if ( ! activeModal.classList.contains( 'panel--drawer' ) ) {
				if ( activeModal.classList.contains( 'is-closing' ) ) {
					return;
				}
				var modal = activeModal;
				modal.classList.add( 'is-closing' );
				var duration = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ? 100 : 180;
				modalCloseTimer = window.setTimeout( function () { modal.close(); }, duration );
				return;
			}
			activeModal.close();
			return;
		}
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

	var miniBagRequest = 0;
	var bagMutationPending = false;

	function updateBagMarkup( doc ) {
		var panel = document.getElementById( 'added-panel' );
		var result = doc.querySelector( '#added-panel' );
		if ( ! panel || ! result ) { throw new Error( 'Missing bag' ); }
		panel.removeAttribute( 'aria-busy' );
		panel.innerHTML = result.innerHTML;
		var bag = doc.querySelector( 'a.site-nav__bag' );
		if ( bag ) {
			document.querySelectorAll( 'a.site-nav__bag' ).forEach( function ( current ) { current.replaceWith( document.importNode( bag, true ) ); } );
		}
		if ( window.jQuery ) { window.jQuery( document.body ).trigger( 'wc_fragment_refresh' ); }
		return panel;
	}

	function bagFailure( feedback ) {
		var panel = document.getElementById( 'added-panel' );
		feedback.textContent = panel.dataset.error;
		var link = document.createElement( 'a' );
		link.href = panel.dataset.bagUrl;
		link.textContent = panel.dataset.bagLabel;
		feedback.append( ' ', link );
	}

	function bagDocument( response ) {
		if ( ! response.ok ) { throw new Error( 'Unable to load bag' ); }
		return response.text().then( function ( html ) { return new DOMParser().parseFromString( html, 'text/html' ); } );
	}

	function openBag( opener ) {
		var panel = document.getElementById( 'added-panel' );
		if ( ! panel || typeof panel.showModal !== 'function' ) { return false; }
		if ( bagMutationPending || panel.getAttribute( 'aria-busy' ) === 'true' ) { return true; }
		var request = ++miniBagRequest;
		panel.querySelector( '#added-panel-title' ).textContent = panel.dataset.title;
		openPanel( 'added' );
		modalOpener = opener;
		panel.setAttribute( 'aria-busy', 'true' );
		panel.querySelectorAll( 'input, button' ).forEach( function ( control ) { control.disabled = true; } );
		fetch( panel.dataset.bagUrl, { credentials: 'same-origin', cache: 'no-store' } )
			.then( bagDocument )
			.then( function ( doc ) {
				if ( request !== miniBagRequest ) { return; }
				updateBagMarkup( doc );
				if ( activeModal === panel ) { panel.querySelector( '#added-panel-title' ).focus( { preventScroll: true } ); }
			} )
			.catch( function () {
				if ( request === miniBagRequest ) { bagFailure( panel.querySelector( '.mini-bag__feedback' ) ); }
			} )
			.finally( function () {
				if ( request === miniBagRequest ) { panel.removeAttribute( 'aria-busy' ); }
			} );
		return true;
	}

	document.addEventListener( 'submit', function ( event ) {
		var form = event.target;
		if ( ! form.matches( '[data-mini-bag-form]' ) ) { return; }
		event.preventDefault();
		var panel = form.closest( 'dialog' );
		if ( bagMutationPending || panel.getAttribute( 'aria-busy' ) === 'true' || ! form.reportValidity() ) { return; }
		var data = new FormData( form );
		var focus = document.activeElement;
		var item = focus.closest( '[data-mini-bag-item]' );
		var key = item && item.dataset.miniBagItem;
		var step = focus.dataset.qtyStep;
		var title = panel.querySelector( '#added-panel-title' ).textContent;
		var request = ++miniBagRequest;
		bagMutationPending = true;
		form.setAttribute( 'aria-busy', 'true' );
		form.querySelectorAll( 'input, button' ).forEach( function ( control ) { control.disabled = true; } );
		fetch( form.action, { method: 'POST', body: data, credentials: 'same-origin', cache: 'no-store' } )
			.then( bagDocument )
			.then( function ( doc ) {
				if ( request !== miniBagRequest ) { return; }
				updateBagMarkup( doc );
				panel.querySelector( '#added-panel-title' ).textContent = title;
				var feedback = panel.querySelector( '.mini-bag__feedback' );
				var errors = doc.querySelectorAll( '.woocommerce-error' );
				errors.forEach( function ( error ) { feedback.appendChild( document.importNode( error, true ) ); } );
				if ( ! errors.length ) { feedback.textContent = panel.dataset.updated; }
				var cart = document.querySelector( '.cart-wrap, .cart-empty' );
				var nextCart = doc.querySelector( '.cart-wrap, .cart-empty' );
				if ( cart && nextCart ) { cart.replaceWith( document.importNode( nextCart, true ) ); }
				if ( activeModal === panel ) {
					var row = key && panel.querySelector( '[data-mini-bag-item="' + key + '"]' );
					var nextFocus = row && row.querySelector( step ? '[data-qty-step="' + step + '"]:not(:disabled)' : '.qty__input' );
					( nextFocus || panel.querySelector( '#added-panel-title' ) ).focus( { preventScroll: true } );
				}
			} )
			.catch( function () { bagFailure( panel.querySelector( '.mini-bag__feedback' ) ); } )
			.finally( function () { bagMutationPending = false; form.removeAttribute( 'aria-busy' ); } );
	} );

	document.addEventListener( 'click', function ( event ) {
		var bag = event.target.closest( 'a.site-nav__bag' );
		if ( bag && event.button === 0 && ! event.metaKey && ! event.ctrlKey && ! event.shiftKey && ! event.altKey && openBag( bag ) ) {
			event.preventDefault();
			return;
		}
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
		if ( activeModal && event.key === 'Tab' ) {
			var stops = Array.prototype.filter.call(
				activeModal.querySelectorAll( 'a[href], button, input, select, textarea, [tabindex]' ),
				function ( element ) {
					return ! element.disabled && element.tabIndex >= 0 && element.getClientRects().length &&
						( element.type !== 'radio' || element.checked );
				}
			);
			var first = stops[0];
			var last = stops[stops.length - 1];
			if ( first && ( ! activeModal.contains( document.activeElement ) ||
				( event.shiftKey && document.activeElement === first ) ||
				( ! event.shiftKey && document.activeElement === last ) ) ) {
				event.preventDefault();
				( event.shiftKey ? last : first ).focus();
			}
		}
		if ( 'Escape' === event.key && main && main.classList.contains( 'has-panel' ) ) {
			closePanels();
		}
	} );

	var initialModal = document.querySelector( 'dialog[data-panel][open]' );
	if ( initialModal ) {
		openPanel( initialModal.dataset.panel );
	}

	/* Keep WooCommerce's native validation and POST handling, then show its result in place. */
	document.addEventListener( 'submit', function ( event ) {
		var form = event.target;
		var button = form.matches( 'form.cart' ) && form.querySelector( '.single_add_to_cart_button' );
		var panel = document.getElementById( 'added-panel' );
		if ( ! button || ! panel || typeof panel.showModal !== 'function' ) {
			return;
		}
		event.preventDefault();
		if ( form.getAttribute( 'aria-busy' ) === 'true' || button.classList.contains( 'disabled' ) ) {
			return;
		}
		var data = new FormData( form );
		if ( button.name ) {
			data.set( button.name, button.value );
		}
		var target = new URL( form.action || window.location.href );
		[ 'added', 'choose-size', 'add-to-cart' ].forEach( function ( key ) { target.searchParams.delete( key ); } );
		var feedback = form.querySelector( '[data-add-feedback]' );
		if ( ! feedback ) {
			feedback = document.createElement( 'div' );
			feedback.dataset.addFeedback = '';
			feedback.setAttribute( 'role', 'alert' );
			button.closest( '.woocommerce-variation-add-to-cart' ) ? button.closest( '.woocommerce-variation-add-to-cart' ).after( feedback ) : form.appendChild( feedback );
		}
		feedback.replaceChildren();
		form.setAttribute( 'aria-busy', 'true' );
		button.disabled = true;
		++miniBagRequest;
		bagMutationPending = true;
		fetch( target, { method: 'POST', body: data, credentials: 'same-origin', cache: 'no-store' } )
			.then( function ( response ) {
				if ( ! response.ok ) { throw new Error( 'Unable to confirm' ); }
				return response.text();
			} )
			.then( function ( html ) {
				var doc = new DOMParser().parseFromString( html, 'text/html' );
				var result = doc.querySelector( '#added-panel[data-added-item]' );
				if ( ! result ) {
					var errors = doc.querySelectorAll( '.woocommerce-error' );
					if ( ! errors.length ) { throw new Error( 'Missing confirmation' ); }
					errors.forEach( function ( error ) { feedback.appendChild( document.importNode( error, true ) ); } );
					return;
				}
				updateBagMarkup( doc );
				openPanel( 'added' );
				modalOpener = button;
			} )
			.catch( function () {
				// The POST may already have succeeded. Never automatically submit it twice.
				feedback.textContent = panel.dataset.error;
				var link = document.createElement( 'a' );
				link.href = panel.dataset.bagUrl;
				link.textContent = panel.dataset.bagLabel;
				feedback.append( ' ', link );
			} )
			.finally( function () {
				bagMutationPending = false;
				form.removeAttribute( 'aria-busy' );
				button.disabled = false;
			} );
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
		var mini = input.closest( '[data-mini-bag-form]' );
		if ( mini ) { mini.requestSubmit(); return; }
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
