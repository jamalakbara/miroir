---
aimem: 1
id: mem-cr4vvbhh
type: memory
title: Demo store seeder (miroir-demo plugin)
summary: wp miroir demo seed builds settings, legacy taxonomy/pages, 28 template products; reset removes demo products
status: active
trust: agent
roles: [all]
tags: [demo, seed]
paths: [wp-content/plugins/miroir-demo/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-30T20:44:53Z
updated: 2026-10-30T20:44:53Z
kind: howto
---

Plugin wp-content/plugins/miroir-demo adds WP-CLI commands (plugin must be active):
- docker compose run --rm wpcli miroir demo seed  -> idempotent (matches by slug). Sets IDR/Rp 389.000 format, country ID:JK, COD + bank transfer, Indonesia shipping zone (flat Rp 20.000, free >= Rp 500.000), turns off WooCommerce coming-soon mode, 4:5 catalog crop. Creates the 13 legacy product categories (shop > shop-by-collection > textura etc.) and 4 tags with legacy slugs, pages about/contact/delivery-and-returns/terms-and-conditions, a Home front page (+ title-less front-page wp_template), a wp_navigation menu, and 28 template products using real legacy product slugs (dress/pants/skirt/set = variable S/M/L, tops/outer = simple All Size).
- docker compose run --rm wpcli miroir demo reset -> deletes only products/images with meta _miroir_demo; taxonomy, pages, settings stay.
Product names/prices/descriptions/images are placeholders (GD-drawn silhouettes), not legacy data. Replace with a real product import later.
