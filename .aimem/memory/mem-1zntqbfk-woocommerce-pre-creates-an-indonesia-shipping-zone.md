---
aimem: 1
id: mem-1zntqbfk
type: memory
title: WooCommerce pre-creates an Indonesia shipping zone
summary: Onboarding auto-made zone 'Indonesia' with unconditional free shipping; seed scripts must reuse it, not skip or duplicate
status: active
trust: agent
roles: [all]
tags: [woocommerce, shipping]
paths: [wp-content/plugins/miroir-demo/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-30T20:44:54Z
updated: 2026-10-30T20:44:54Z
kind: gotcha
---

On this install WooCommerce created shipping zone #1 'Indonesia' with a free_shipping method (requires: none) before any setup script ran, so every order shipped free. Configure methods inside the existing zone (find by name, add missing methods, update woocommerce_<method>_<instance>_settings) instead of returning early when the zone exists. Bump the shipping transient (WC_Cache_Helper::get_transient_version('shipping', true)) after editing settings directly.
