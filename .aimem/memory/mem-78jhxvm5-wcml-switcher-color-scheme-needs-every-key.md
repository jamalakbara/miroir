---
aimem: 1
id: mem-78jhxvm5
type: memory
title: WCML switcher color_scheme needs every key
summary: currency_switchers[*][color_scheme] missing keys -> PHP warnings in class-wcml-currency-switcher-templates.php:396; fill all 9 keys with ''
status: active
trust: agent
roles: [all]
tags: [currency]
paths: [wp-content/plugins/miroir-currency/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-30T22:16:26Z
updated: 2026-10-30T22:16:26Z
kind: gotcha
---

When writing _wcml_settings directly, currency_switchers[<id>][color_scheme] must contain all of font_current_normal/hover, background_current_normal/hover, font_other_normal/hover, background_other_normal/hover, border_normal (empty string = theme default). WCML reads them unguarded. Also: page caching (LiteSpeed on hosting) must be checked with currency switching; WCML has a 'cache_enabled' mode that loads prices via AJAX.
