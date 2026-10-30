---
aimem: 1
id: mem-6gpn6a3h
type: memory
title: Project overview
summary: Rebuild of miroir.id store on WordPress + WooCommerce; keep every legacy URL working via redirects
status: active
trust: agent
roles: [all]
tags: [pinned]
paths: [docs/migration/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-30T20:25:17Z
updated: 2026-10-30T20:25:17Z
kind: fact
---

Miroir is a rebuild of the miroir.id online shop on WordPress + WooCommerce, developed locally first.

- Old site: https://miroir.id (WooCommerce store; product URLs like /product/<slug>/).
- Goal: new store live with no lost SEO traffic: every legacy URL either resolves on the new site or 301-redirects to its best match.
- Legacy URL inventory: docs/migration/legacy-urls.csv (194 URLs: 133 product, 34 pagination, 13 category, 4 tag, 4 page, 4 product-junk, home, shop). The xlsx next to it is the same list for editing in Excel.
- The new_url_or_action column is still empty for every row: redirect mapping is not done yet.
