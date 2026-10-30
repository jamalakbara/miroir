---
aimem: 1
id: mem-emqm9rkc
type: memory
title: Legacy URL redirect rules
summary: Pagination 301s to page 1; numeric/trashed product slugs 301 to /shop/; keep slugs identical where possible
status: active
trust: agent
roles: [all]
tags: [migration seo]
paths: [docs/migration/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-30T20:25:49Z
updated: 2026-10-30T20:25:49Z
links: [mem-6gpn6a3h]
kind: constraint
---

Rules for filling new_url_or_action in docs/migration/legacy-urls.csv (194 legacy miroir.id URLs):

- type=pagination (34 rows, e.g. /product-category/x/page/3/): 301 to page 1 of the same listing on the new site, not to the paginated URL, since page counts will differ.
- type=product-junk (4 rows: /product/9163/, /product/7943/, /product/8279/, /product/__trashed/): leftover numeric or trashed slugs from the old store. 301 to /shop/ unless a matching live product exists.
- Product, category and tag slugs should be kept identical on the new site where possible so most rows need no redirect at all; with permalinks at /%postname%/ WooCommerce serves /product/<slug>/ and /product-category/<slug>/ by default.
- Edit the CSV as the source of truth; the xlsx is a convenience copy and can drift.
