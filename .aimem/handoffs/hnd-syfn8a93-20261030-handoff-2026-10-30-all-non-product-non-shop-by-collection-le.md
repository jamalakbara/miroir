---
aimem: 1
id: hnd-syfn8a93
type: handoff
title: "Handoff 2026-10-30: All non-product, non-shop-by-collection legacy URLs (46 rows) verified on local store: 19 keep, 27…"
summary: "Done: All non-product, non-shop-by-collection legacy URLs (46 rows) verified on local store: 19 keep, 27 301 via new miroir-redirects plugin. Shop page moved to /shop-2/, added baju > women category,…"
status: final
trust: agent
roles: [all]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-30T21:04:22Z
updated: 2026-10-30T21:04:22Z
task: tsk-gw91zc27
---

## Done
All non-product, non-shop-by-collection legacy URLs (46 rows) verified on local store: 19 keep, 27 301 via new miroir-redirects plugin. Shop page moved to /shop-2/, added baju > women category, filled new_url_or_action for those rows in legacy-urls.csv.

## Not done
133 product URLs and 15 shop-by-collection URLs (incl. neuve category) deliberately skipped per user. xlsx copy not synced. Verification is a one-off curl script, not a committed test.

## Next
Product URLs once real products are imported; shop-by-collection URLs; commit a repeatable legacy URL check.

## Blocked
none

## Notes for next agent
none
