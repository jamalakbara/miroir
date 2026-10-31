---
aimem: 1
id: mem-614w22w1
type: memory
title: wp-admin forms.css .card leaks into block editor previews of product cards
summary: Editor iframe loads wp-admin forms.css whose .card adds border/padding/max-width 520px; editor.css resets .miroir-block-preview .card
status: active
trust: agent
roles: [all]
tags: [blocks, editor, css]
paths: [wp-content/themes/miroir-theme/assets/css/editor.css]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T22:24:11Z
updated: 2026-10-31T22:24:11Z
kind: gotcha
---

The block editor canvas iframe loads wp-admin/css/forms.min.css, which styles .card (border, 8px radius, 16px 24px padding, min/max width). The theme's product card uses class .card, so ServerSideRender previews of miroir/product-strip showed boxed cards. assets/css/editor.css resets those properties under .miroir-block-preview .card. The storefront is not affected.
