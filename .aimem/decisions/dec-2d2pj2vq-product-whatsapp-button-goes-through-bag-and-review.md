---
aimem: 1
id: dec-2d2pj2vq
type: decision
title: Product WhatsApp button goes through bag and review
summary: Order via WhatsApp adds the product to the bag and opens review, so every order is saved in WooCommerce. Direct wa.me stays only for non-orderable product enquiries.
status: proposed
trust: verified
roles: [all]
paths: [wp-content/themes/miroir-theme/template-parts/product-buy.php, wp-content/plugins/miroir-whatsapp-order/miroir-whatsapp-order.php, wp-content/themes/miroir-theme/assets/js/theme.js]
author: human:akbar
created: 2026-11-05T22:54:05Z
updated: 2026-11-05T22:54:05Z
verified: 2026-11-05
supersedes: dec-8vfz3jd3
---

User reported direct PDP links created no WooCommerce order. Review stays because an order needs name and delivery city. Button is a submit for the buy form with formaction ?wa-order=1; plugin redirect runs after the theme added-to-bag redirect. product.js was removed. Existing bag items stay in the same order. Rest of dec-8vfz3jd3 still applies: review saves on-hold order, sent page opens WhatsApp.
