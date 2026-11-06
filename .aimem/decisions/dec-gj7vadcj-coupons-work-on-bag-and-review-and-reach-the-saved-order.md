---
aimem: 1
id: dec-gj7vadcj
type: decision
title: Coupons work on bag and review, and reach the saved order
summary: Coupon field on bag and review; review posts whole form so typed details stay. Discount carries to WooCommerce order, sent page, account and WhatsApp message with Total line.
status: proposed
trust: verified
roles: [all]
paths: [wp-content/plugins/miroir-whatsapp-order/**, wp-content/themes/miroir-theme/woocommerce/**]
author: human:akbar
created: 2026-11-06T09:01:47Z
updated: 2026-11-06T09:01:47Z
verified: 2026-11-06
---

User asked 2026-11-06. PDP Order via WhatsApp skips the bag, so review needs the field too. Review buttons are type=button and call form.submit() so Enter in other fields never applies a coupon and the WhatsApp tab handler does not fire. WooCommerce applies them on wp_loaded for any URL; handle_checkout returns early on apply_coupon or remove_coupon. Order uses WC_Order::apply_coupon so usage limits count. Estimated total excludes shipping, confirmed in chat. WCML fixed-amount coupons in other currencies not tested.
