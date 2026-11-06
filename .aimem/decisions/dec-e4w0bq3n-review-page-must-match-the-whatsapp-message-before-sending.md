---
aimem: 1
id: dec-e4w0bq3n
type: decision
title: Review page must match the WhatsApp message before sending
summary: "Preview shows Order ref: #… until the order exists; bag_hash stops a submit when products or subtotal changed after the page loaded."
status: proposed
trust: verified
roles: [all]
paths: [wp-content/plugins/miroir-whatsapp-order/miroir-whatsapp-order.php, wp-content/plugins/miroir-whatsapp-order/assets/checkout.js]
author: human:akbar
created: 2026-11-05T22:41:16Z
updated: 2026-11-05T22:41:16Z
verified: 2026-11-05
---

Bag drawer can change the bag on checkout without refreshing preview, and other tabs can too. Submit re-renders with typed values and a notice instead of creating an order. Hash covers displayed lines and subtotal, not WC cart hash, to avoid false mismatches from totals state.
