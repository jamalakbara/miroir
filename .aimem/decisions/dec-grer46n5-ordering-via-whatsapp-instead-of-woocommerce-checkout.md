---
aimem: 1
id: dec-grer46n5
type: decision
title: Ordering via WhatsApp instead of WooCommerce checkout
summary: "Per quotation QUO-2026-003: no payment gateway or shipping calc; PDP and bag hand the order to WhatsApp (wa.me prefilled message)"
status: proposed
trust: agent
roles: [all]
tags: [whatsapp, checkout, woocommerce, design]
paths: [docs/design/miroir.pen, docs/Quotation Webdev Miroir.pdf]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-30T21:29:20Z
updated: 2026-10-30T21:29:20Z
links: [dec-bf8ede4p]
---

Source: docs/Quotation Webdev Miroir.pdf (QUO-2026-003, 30 Oct 2026). It says WooCommerce in catalogue mode, ordering from product pages to the client's WhatsApp number, no checkout, no payment gateway, no automatic shipping. A payment gateway and shipping calculation are explicitly excluded (separate quote). Standard WhatsApp Business app, no API.

Lofi updated (2026-10-31) in docs/design/miroir.pen:
- PDP: 'Order via WhatsApp' button under 'Add to bag' (single product, selected size, price).
- Bag: coupon removed, shipping = 'Confirmed in WhatsApp', 'Estimated total', CTA 'Checkout via WhatsApp'.
- 06 Checkout becomes a short review page: name, country, city, notes, live preview of the message, then 'Continue to WhatsApp'.
- 07 becomes 'Order sent' handoff page (retry WhatsApp link, bank transfer after confirmation).
- 'WhatsApp Float' component on every page (quote: floating WhatsApp button).

Planned implementation: own plugin wp-content/plugins/miroir-whatsapp-order (conventions: own code in miroir-*). Button after add-to-cart reads the chosen variation in JS and opens https://wa.me/<number>?text=<urlencoded>. Replace woocommerce_button_proceed_to_checkout. The review step builds the message server-side from WC()->cart, with prices in the currency the multi-currency plugin has selected. Turn off payment gateways (the local seed enabled COD + bank transfer). Point the checkout URL at the review page so it keeps resolving.

Open: the quote only promises product-page ordering; the bag-to-WhatsApp review step goes a little beyond it, so check with the client. Optionally create a pending WC order before the redirect for records/stock (not in the quote). My account / login pages have little purpose without checkout.
