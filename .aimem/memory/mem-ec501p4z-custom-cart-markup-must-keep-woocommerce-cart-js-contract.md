---
aimem: 1
id: mem-ec501p4z
type: memory
title: Custom cart markup must keep WooCommerce cart.js contract
summary: cart.js disables update_cart on load and only AJAX-updates forms holding .woocommerce-cart-form__contents; totals block needs .cart_totals
status: active
trust: agent
roles: [all]
tags: [theme, woocommerce, cart]
paths: [wp-content/themes/miroir-theme/woocommerce/cart/cart.php, wp-content/themes/miroir-theme/assets/js/theme.js]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T14:05:12Z
updated: 2026-10-31T14:05:12Z
kind: gotcha
---

Found 2026-10-31 building the bag (screen 04). WooCommerce cart.js (WC 11.2) disables button[name=update_cart] on init and re-enables it only on change inside .cart_item inputs; cart_submit returns early unless the form contains .woocommerce-cart-form__contents, so the browser posts the form natively WITHOUT update_cart (disabled fields are not sent) and quantities silently do not change. Fix in theme: wrap rows in .woocommerce-cart-form__contents, rows get .cart_item, keep a (visually hidden) submit button name=update_cart; theme.js re-enables it, sets clicked=true and requestSubmit(button) -> cart.js quantity_update AJAX. update_wc_div replaces .woocommerce-cart-form and div.cart_totals, so the totals block carries class cart_totals.
