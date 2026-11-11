---
aimem: 1
id: hnd-zsh6sgg2
type: handoff
title: "Handoff 2026-11-11: Shop sidebar menus: removed accessories from 'shop women' (plp-categories); 'featured' (plp-feature…"
summary: "Done: Shop sidebar menus: removed accessories from 'shop women' (plp-categories); 'featured' (plp-featured) is now new arrivals, best sellers, by miroir collection (at-the-studio category), collabora…"
status: final
trust: agent
roles: [all]
author: agent:claude-code:sonnet-5.5
created: 2026-11-11T03:38:50Z
updated: 2026-11-11T03:38:50Z
branch: main
---

## Done
Shop sidebar menus: removed accessories from 'shop women' (plp-categories); 'featured' (plp-featured) is now new arrivals, best sellers, by miroir collection (at-the-studio category), collaboration removed. Changed seed in cli.php, fallback lists in template-parts/shop-filters.php, and local nav menu items (deleted 235 and 243, renamed 242, reordered). Verified on localhost:8080/shop-2/. Uncommitted.

## Not done
Not deployed: staging/live menus (shop-women, featured) still old; need same wp menu item delete/update by title (db ids differ per env). Accessories category itself and collaboration category/products untouched. Kept label 'best sellers' (user wrote 'best seller').

## Next
Commit, deploy staging, update the two menus on staging+live.

## Blocked
none

## Notes for next agent
none
