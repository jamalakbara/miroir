---
aimem: 1
id: dec-8vfz3jd3
type: decision
title: "WhatsApp ordering implemented: review form saves an on-hold order, then opens wa.me"
summary: "miroir-whatsapp-order: own POST handler on the checkout page (no WC checkout/gateways); order status on-hold, message in _miroir_wa_message, order-received page auto-opens WhatsApp once"
status: superseded
trust: agent
roles: [all]
tags: [whatsapp, checkout, woocommerce]
paths: [wp-content/plugins/miroir-whatsapp-order/**, wp-content/themes/miroir-theme/woocommerce/checkout/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T14:05:15Z
updated: 2026-11-05T22:54:05Z
links: [dec-54vkenha]
supersedes: dec-grer46n5
superseded_by: dec-2d2pj2vq
---

Built 2026-10-31 (phase 2). Plugin owns behaviour, theme owns markup (woocommerce/checkout/form-checkout.php = screen 06, thankyou.php = 07; product-buy.php button via filter miroir_wa_product_url). Flow: bag -> /checkout/ review (name, country, city, notes, live preview from assets/checkout.js) -> POST handled on template_redirect (nonce miroir_wa_checkout) -> wc_create_order from cart, billing/shipping name+city+country, payment title 'Bank transfer after confirmation', status on-hold (stock reduced, shop notified) -> cart emptied -> order-received URL; sent.js navigates to wa.me once per order (sessionStorage), retry button keeps the link. Message groups separated by blank lines as in the lofi; adds 'Order ref: #N'. Gateways off via filters (woocommerce_available_payment_gateways = [], cart/order needs_payment false). PDP link updates on variation choice from display_price (WC leaves price_html empty when all variations cost the same). Number: option miroir_wa_number (digits, country code first); a placeholder test number is set locally and must be replaced. Bank details on 07 come from the first WooCommerce BACS account (woocommerce_bacs_accounts), else dashes.
