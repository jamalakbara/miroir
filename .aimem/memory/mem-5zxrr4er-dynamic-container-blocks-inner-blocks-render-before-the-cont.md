---
aimem: 1
id: mem-5zxrr4er
type: memory
title: "Dynamic container blocks: inner blocks render before the container; replacing screens must short-circuit"
summary: miroir/page (WC pages), miroir/product (states) and miroir/listing (empty) decide in pre_render_block; else WC shortcodes/notices run twice and notices print in the discarded copy
status: active
trust: agent
roles: [all]
tags: [blocks, woocommerce, notices]
paths: [wp-content/themes/miroir-theme/inc/block-helpers.php]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T23:43:30Z
updated: 2026-10-31T23:43:30Z
links: [dec-4a5wfd9e, mem-kgnqxac3]
kind: gotcha
---

WP_Block::render() renders inner blocks into $content before the container's render callback runs. A container that ignores $content and prints something else (bag/checkout/account shortcode, product state screen, empty results) still pays for the inner render: the account shortcode ran twice, the first run printed and cleared the WooCommerce notices, so the visible copy had an empty notices wrapper (failed login showed no error). Fix: the pre_render_block filter in inc/block-helpers.php returns the replacement HTML for miroir/page (is_cart/is_checkout/is_account_page), miroir/product (miroir_product_state) and miroir/listing (no posts) before any inner block renders. Any new container that can replace its sections must be added there.
