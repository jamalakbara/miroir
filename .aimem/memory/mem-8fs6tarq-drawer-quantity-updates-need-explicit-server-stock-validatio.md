---
aimem: 1
id: mem-8fs6tarq
type: memory
title: Drawer quantity updates need explicit server stock validation
summary: WooCommerce native update_cart accepts excessive quantities until later checks; drawer adds woocommerce_update_cart_validation to retain the saved quantity on failure.
status: active
trust: verified
roles: [all]
author: human:akbar
created: 2026-11-01T18:44:58Z
updated: 2026-11-01T18:44:58Z
verified: 2026-11-01
kind: gotcha
---

Verified in WooCommerce shipped handler: update_cart calls woocommerce_update_cart_validation but does not check available stock before set_quantity. Existing full-bag stock-state template then clears notices. Mini-bag requests carry miroir_mini_bag=1 and a normal WooCommerce cart nonce; inc/states.php filter rejects unavailable quantities and aggregates posted quantities sharing get_stock_managed_by_id. Do not rely only on input max or success notices when confirming drawer quantity saves.
