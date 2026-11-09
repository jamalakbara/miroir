---
aimem: 1
id: mem-00jfjmpv
type: memory
title: Sale category is automatic
summary: "'sale' product_cat follows is_on_sale() on every product save; manual ticks overridden; backfill: wp miroir theme sync-sale"
status: active
trust: verified
roles: [all]
author: human:akbar
created: 2026-11-09T05:46:42Z
updated: 2026-11-09T05:46:42Z
verified: 2026-11-09
---

The 'sale' product category is managed by miroir_sync_sale_category (inc/woocommerce.php), hooked on woocommerce_after_product_object_save. Variation saves sync the parent. WooCommerce's scheduled-sales cron saves products when dated sales start/end, so those are covered. Ticking 'sale' by hand on a product with no sale price is undone on its next save. Products saved before this shipped need: wp miroir theme sync-sale.
