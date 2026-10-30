---
aimem: 1
id: mem-nsd6azpt
type: memory
title: Header currency switcher is block miroir/currency-switcher
summary: miroir-currency registers dynamic block, Block Hooks insert it after core/navigation in header parts/patterns (prio 8, before WC account/cart); PDP switcher off
status: active
trust: agent
roles: [all]
tags: [currency, theme]
paths: [wp-content/plugins/miroir-currency/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-30T22:23:55Z
updated: 2026-10-30T22:23:55Z
links: [dec-nepgetmt, mem-78jhxvm5]
kind: howto
---

Header switcher (added 2026-10-31):
- Block `miroir/currency-switcher` (registered in miroir-currency, server-rendered via do_action('wcml_currency_switcher') with switcher_style wcml-dropdown-click, format %code%). Click-to-open so it works on touch screens.
- Block Hooks: filter hooked_block_types at priority 8 adds it `after core/navigation` when context is a header template part or header pattern. WooCommerce adds customer-account + mini-cart at the same anchor at priority 9, so order is menu, currency, account, cart. Skipped when the header already contains the block.
- The filter's $context can be WP_Block_Template, a pattern array, or WP_Post (navigation fallback); treating WP_Post as array was a fatal.
- When the miroir theme is built, place the block in parts/header.html directly; the hook then stands down.
- WCML product-page switcher turned off (currency_switcher_product_visibility=0) to avoid two switchers on PDP; its 'product' settings stay because the WCML admin screen expects them.
- Currency switch = admin-ajax + full reload; locally 5-10 s because the whole Docker-on-Windows stack is slow, not the switcher.
