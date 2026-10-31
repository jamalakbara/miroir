---
aimem: 1
id: mem-h2ggxvpy
type: memory
title: "Block theme: core/global styles get enqueued while content renders; dequeue again before printing"
summary: global-styles, wp-block-library, block-style-variation-styles, woocommerce-blocktheme, wc-blocks-style appear late; theme dequeues on wp_print_styles and wp_print_footer_scripts (prio 1)
status: active
trust: agent
roles: [all]
tags: [css, blocks, woocommerce]
paths: [wp-content/themes/miroir-theme/inc/setup.php, wp-content/themes/miroir-theme/inc/woocommerce.php]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T23:26:29Z
updated: 2026-10-31T23:26:29Z
links: [dec-4a5wfd9e]
kind: gotcha
---

After the switch to a block theme the size guide differed from the classic theme although the markup was identical: a block style variation in its content made WordPress enqueue global-styles, wp-block-library and block-style-variation-styles during rendering, after the wp_enqueue_scripts dequeue. WooCommerce also enqueues woocommerce-blocktheme on enqueue_block_assets and re-adds wc-blocks-style. miroir_dequeue_core_styles() (setup.php) and the WC handles (woocommerce.php) are dequeued again on wp_print_styles and wp_print_footer_scripts at priority 1. Styles still reach the CSS through miroir_global_styles_css(): preset variables, font roles and Additional CSS only. Per-block core CSS stays on for core blocks the theme does not style (users adding e.g. an image or columns block).
