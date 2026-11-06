---
aimem: 1
id: mem-qm1f4x74
type: memory
title: Empty children of the PDP buy form still add a 20px flex gap
summary: theme.js appends an empty [data-add-feedback] to .buy-form on Add to bag; flex gap counts 0px children, so it needs display:none while empty.
status: active
trust: verified
roles: [all]
paths: [wp-content/themes/miroir-theme/assets/css/flow.css, wp-content/themes/miroir-theme/assets/js/theme.js, wp-content/themes/miroir-theme/assets/css/product.css]
author: human:akbar
created: 2026-11-06T09:59:23Z
updated: 2026-11-06T09:59:23Z
verified: 2026-11-06
kind: gotcha
---

Simple products append feedback directly to the flex .buy-form, doubling the gap from Add to bag to Order via WhatsApp (measured 20px to 40px). Variable products put it inside block .single_variation_wrap, so they never showed it. All 18 current products are simple. Hiding :empty fixes it; do not rely on 0 height.
