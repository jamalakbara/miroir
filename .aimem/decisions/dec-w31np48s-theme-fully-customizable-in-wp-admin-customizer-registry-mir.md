---
aimem: 1
id: dec-w31np48s
type: decision
title: "Theme fully customizable in wp-admin: Customizer registry, Miroir home blocks, real images"
summary: "User chose (2026-11-01): Customizer for design/copy (defaults = lofi), home as miroir/* blocks, real WC images with grey placeholder fallback; no third-party deps"
status: proposed
trust: agent
roles: [all]
tags: [customizer, blocks, images, admin]
paths: [wp-content/themes/miroir-theme/inc/customizer/**, wp-content/themes/miroir-theme/inc/blocks.php, wp-content/themes/miroir-theme/blocks/**, wp-content/plugins/miroir-whatsapp-order/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T18:24:22Z
updated: 2026-10-31T18:24:22Z
links: [dec-54vkenha]
---

User asked (2026-11-01) for everything to be customizable from wp-admin "like a theme should have". Choices made with the user:
- Home page: custom dynamic blocks miroir/editorial, miroir/banner, miroir/product-strip (plain JS editor UI, no build step, server render via the same template parts as the PHP fallback). Rejected: Customizer slots (clunky reorder, fixed count), ACF/SCF (third-party dependency).
- Customizer panel "Miroir theme" for colours, logo (custom-logo), fonts (bundled / system / uploaded woff2), type scale, layout spacing per breakpoint, header/footer, search shortcuts, linked pages, maintenance; plus "Text & labels" (~209 strings). Every default equals the lofi value and no override CSS is printed while nothing is changed.
- Images: real WooCommerce product/gallery/variation images and block images render inside the .ph box (cover-cropped); grey placeholder only when no image. This replaces the "grey placeholders instead of photos" part of dec-54vkenha; the rest of that decision stands.
- WhatsApp plugin settings live in WooCommerce > Settings > WhatsApp ordering (number, message templates with {placeholders}, order status, payment title, timing), shared by PHP and JS.
Admin-set values intentionally diverge from miroir.pen; the pen keeps documenting the defaults.
