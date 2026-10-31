---
aimem: 1
id: dec-dwscr3kn
type: decision
title: Compact mobile and tablet navbar follows Velvet grouping
summary: Below 1024px keep Miroir logo left, bag count and menu right; currency stays in menu; announcement follows nav.
status: proposed
trust: verified
roles: [all]
author: human:akbar
created: 2026-10-31T22:40:53Z
updated: 2026-10-31T22:40:53Z
verified: 2026-10-31
---

Replaced the floating center menu and inline currency with grouped 44px bag/menu targets. Nav height is 68px on mobile, 76px on tablet; announcement remains 40px beneath it. Desktop retains existing layout. Applied to 86 responsive Header instances in miroir.pen. Shared miroir_header_bag() renders initial and WooCommerce fragment markup; fragment cache version bumped to avoid stale text-only bag.
