---
aimem: 1
id: dec-4fden8b5
type: decision
title: "State screens 31-44: real states with customer-friendly titles"
summary: "User chose (2026-10-31): friendly titles instead of designer state labels; PDP sold-out/choose-size use the single-column lofi layout; panels render in place of page content"
status: proposed
trust: agent
roles: [all]
tags: [design, theme, states]
paths: [wp-content/themes/miroir-theme/inc/states.php, wp-content/themes/miroir-theme/template-parts/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T15:27:56Z
updated: 2026-10-31T15:27:56Z
links: [dec-54vkenha]
---

Titles: Menu, Search, Filter & sort, Currency, No results, No matches, Size guide, Sold out, Added to bag, Check your bag, Orders, Open WhatsApp, Choose a size (design text was 'Navigation open', 'Product — unavailable', etc.). Wiring: panels 31-34 are in every page's <main> (template-parts/panels.php), opened by theme.js in place of the content or by ?panel=… without JS; filter panel posts filter_color[] (joined for WooCommerce in inc/states.php); currency panel sends ?miroir_currency=XXX, handled in miroir-currency (sets WCML client currency + starts the guest WC session). 35/36 replace empty listings; 37 is the size-guide page (Guide template, Table block); 38 sold-out products and 44 (?choose-size, reached when Add to bag is pressed without a size) use template-parts/product-state.php; 39 = woocommerce_add_to_cart_redirect to ?added=<cart key> (this also applies to ?add-to-cart= links); 40 replaces the bag while a line exceeds stock; 42 empty orders; 43 is the order-sent page's fallback (sent.js shows it if the page is still visible 4s after opening WhatsApp, or ?wa-fallback=1). 41 = WooCommerce notices restyled (woocommerce/notices/*) and printed inside each screen's column; known messages get the drawn titles (miroir_notice_title). Designer annotations (41 'do not show all three', 43 'After copying: …') are not rendered as such. Size filtering needs a global pa_size attribute; demo sizes are still a local attribute, so the panel shows colour only.
