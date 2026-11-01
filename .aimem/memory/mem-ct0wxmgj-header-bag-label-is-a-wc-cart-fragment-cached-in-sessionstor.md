---
aimem: 1
id: mem-ct0wxmgj
type: memory
title: Header bag label is a WC cart fragment cached in sessionStorage
summary: cart-fragments.js swaps a.site-nav__bag with the sessionStorage copy; woocommerce_cart_fragment_name gets an md5 of nav_bag so edits show
status: active
trust: agent
roles: [all]
tags: [woocommerce, customizer, header]
paths: [wp-content/themes/miroir-theme/inc/woocommerce.php]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T22:24:09Z
updated: 2026-10-31T22:24:09Z
links: [mem-9tb3t8xm]
kind: gotcha
---

WooCommerce cart-fragments.js replaces a.site-nav__bag with the HTML cached in sessionStorage (key = fragment_name) until the cart hash changes. A Customizer edit to Text & labels > nav_bag therefore did not show, in preview or for returning visitors. Fix in inc/woocommerce.php: filter woocommerce_cart_fragment_name appends a short md5 of miroir_text('nav_bag', 0), so a new label uses a new cache key. Any other text that goes into a cart fragment needs the same treatment.
