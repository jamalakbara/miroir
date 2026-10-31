---
aimem: 1
id: mem-jc3esnfa
type: memory
title: Theme setup command and demo data changes for the lofi
summary: "wp miroir theme setup: cart/checkout/account pages -> shortcodes, FAQ page, menus. Demo seed now: pa_color families, women > garment cats, prices IDR 279,000"
status: active
trust: agent
roles: [all]
tags: [theme, setup, wp-cli, demo]
paths: [wp-content/themes/miroir-theme/inc/cli.php, wp-content/plugins/miroir-demo/miroir-demo.php]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T14:05:17Z
updated: 2026-10-31T14:05:17Z
links: [mem-cr4vvbhh]
kind: fact
---

wp miroir theme setup (theme inc/cli.php, safe to re-run): sets cart/checkout/my-account page content to [woocommerce_cart]/[woocommerce_checkout]/[woocommerce_my_account] (the blocks versions ignore the theme's PHP templates), creates the FAQ page (core Details blocks, placeholder copy), creates menus Header, shop women, featured, Footer: contact; footer account/policy menus wait until their pages exist (theme falls back to the same links in code). miroir-demo seed changes 2026-10-31: global attribute Colour (pa_color) with the 10 lofi colour families; categories accessories/bottoms/dress/outerwear/tops under women, assigned by garment; thousand separator ',' / decimal '.'. miroir-currency maps IDR symbol to 'IDR'. Demo was reset+reseeded (product ids changed). Local test customer 'shopper' exists for account captures (see tools/visual-diff/README.md).
