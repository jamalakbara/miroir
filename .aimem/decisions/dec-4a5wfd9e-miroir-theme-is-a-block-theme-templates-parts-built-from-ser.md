---
aimem: 1
id: dec-4a5wfd9e
type: decision
title: "miroir-theme is a block theme: templates/parts built from server-rendered miroir/* blocks"
summary: User chose (2026-11-01) Shopify-style editing in Appearance > Editor; sections are dynamic miroir blocks printing the lofi markup, pixel-identical; Styles + slim Customizer
status: proposed
trust: agent
roles: [all]
tags: [theme, blocks, site-editor]
paths: [wp-content/themes/miroir-theme/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T23:26:13Z
updated: 2026-10-31T23:26:13Z
links: [dec-54vkenha, dec-w31np48s, mem-nsd6azpt]
---

User asked (2026-11-01) for WordPress's equivalent of Shopify's theme editor, so miroir-theme became a block theme (templates/*.html, parts/header|footer.html, theme.json). This replaces the 'rejected: block theme' part of dec-54vkenha; the rest of it (WC overrides for cart/checkout/account, WhatsApp pending orders, phases) stands.
Choices with the user: (1) pixel-identical to the classic theme with no admin changes (tools/visual-diff/parity.js, 39 screens x 3 widths); (2) granularity = sections: product page = product-info / product-buy / product-pager / product-gallery / similar-products inside miroir/product > product-main > product-details; listing = shop-filters / listing-bar / product-grid (search: search-bar / search-results) inside miroir/listing; header = announcement, site-nav > logo, nav-group(primary|utility) > menu / nav-item; footer = footer-text, menu x3, copyright; (3) settings: colours + font families in Site Editor > Styles (theme.json palette/fontFamilies; tokens.css reads --wp--preset-- vars; Text font -> --font-sans, Captions font -> --font-mono), header/footer texts as block attributes, slim Customizer keeps Text & labels, per-breakpoint spacing/columns, type scale, products per page, photos, linked pages, Instagram URL, maintenance; (4) menus stay in Appearance > Menus (miroir/menu picks a location).
Rejected: core blocks (group/navigation/post-content/post-title) and WooCommerce product blocks - their wrappers, layout classes and CSS break the 1:1 markup and the miroir-whatsapp-order hooks; full Site Editor-only settings (no per-breakpoint values in Styles).
Cart, checkout, account and order-received keep their shortcodes and the theme's woocommerce/*.php overrides, rendered through miroir/page-content. The header has no core/navigation, so the miroir-currency and WooCommerce Block Hooks never insert blocks; the theme's own currency modal stays.
