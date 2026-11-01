---
aimem: 1
id: mem-ngmszjee
type: memory
title: WooCommerce variation script only syncs qty limits into '.quantity input.qty'; its jQuery triggers skip native listeners
summary: Theme .qty is the stepper wrapper, so WC never sees our input; apply variation min/max ourselves via jQuery found_variation. jQuery .trigger('change') does not fire addEventListener handlers.
status: active
trust: agent
roles: [all]
tags: [woocommerce, jquery, quantity]
paths: [wp-content/themes/miroir-theme/assets/js/theme.js]
author: agent:claude-code:claude-opus-5-5
created: 2026-11-01T18:58:56Z
updated: 2026-11-01T18:58:56Z
kind: gotcha
---

add-to-cart-variation.js onFoundVariation looks up form.$singleVariationWrap.find('.quantity input.qty[name=quantity]'). Giving our input class qty would pick up the .qty wrapper styles (outline, inline-flex), so theme.js binds window.jQuery('.variations_form').on('found_variation reset_data') instead (theme.js is deferred; footer jQuery is loaded by then and WC init runs on DOM ready afterwards). Native listeners also never see jQuery-triggered events, and the WhatsApp plugin's jQuery change handler on the input runs before the theme's document listener, so the theme re-dispatches a native change after clamping.
