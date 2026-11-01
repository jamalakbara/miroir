---
aimem: 1
id: mem-p1qfqy1x
type: memory
title: WooCommerce form internals that break 1:1 layouts
summary: <wc-order-attribution-inputs> becomes a flex item (+gap); notices only print via woocommerce_before_customer_login_form; strength meter widget is dequeued
status: active
trust: agent
roles: [all]
tags: [theme, woocommerce, css, forms]
paths: [wp-content/themes/miroir-theme/assets/css/flow.css, wp-content/themes/miroir-theme/woocommerce/myaccount/form-login.php]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T14:25:08Z
updated: 2026-10-31T14:25:08Z
kind: gotcha
---

WC 11 order attribution injects a custom element <wc-order-attribution-inputs> into register/checkout forms; inside a flex/display:contents form it takes a gap (24px on screen 12) -> hidden in flow.css. On the logged-out account page WooCommerce prints notices through the woocommerce_before_customer_login_form action, so a template that branches (register / password updated) must fire it before branching or validation errors vanish. The theme dequeues wc-password-strength-meter (its inline widget is not in the lofi) and instead enforces the lofi rule 'at least 12 characters' on register and password reset (MIROIR_MIN_PASSWORD in inc/woocommerce.php). Password-updated screen (16) clears WC's duplicate success notice.
