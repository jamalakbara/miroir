---
aimem: 1
id: hnd-rn0w28fq
type: handoff
title: "Handoff 2026-11-20: Speed: page cache works for returning page views (hit ~0.06s vs miss ~0.5s); first page per new vis…"
summary: "Done: Speed: page cache works for returning page views (hit ~0.06s vs miss ~0.5s); first page per new visitor uncached due to _lscache_vary (WCML currency). Tried LiteSpeed Guest Mode on live: mixed…"
status: final
trust: agent
roles: [all]
author: agent:claude-code
created: 2026-11-20T08:13:01Z
updated: 2026-11-20T08:13:01Z
branch: development
---

## Done
Speed: page cache works for returning page views (hit ~0.06s vs miss ~0.5s); first page per new visitor uncached due to _lscache_vary (WCML currency). Tried LiteSpeed Guest Mode on live: mixed currencies between visitors, reverted within minutes and purged; retest shows correct IDR/USD separation. Measured home (admin view): FCP/LCP ~1.0s, load 1.4s, 93 requests, 5.9MB (4.3MB hero video), 25 CSS + 21 script tags for guests, Avenir served as TTF. No Redis/Memcached server reachable.

## Not done
No speed changes kept. Lighthouse via npx denied.

## Next
Candidates (staging first): Avenir TTF->WOFF2, LiteSpeed CSS/JS minify, drop unused Elementor assets, LiteSpeed crawler warm-up, hPanel object cache if plan offers it.

## Blocked
Waiting on user choice.

## Notes for next agent
none
