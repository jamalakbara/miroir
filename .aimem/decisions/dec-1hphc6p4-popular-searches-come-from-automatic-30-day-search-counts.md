---
aimem: 1
id: dec-1hphc6p4
type: decision
title: Popular searches come from automatic 30-day search counts
summary: User rejected manual suggestions; show the top three successful product searches with at least three counts, or hide the section.
status: proposed
trust: agent
roles: [all]
author: agent:codex
created: 2026-11-01T16:45:24Z
updated: 2026-11-01T16:45:24Z
---

Replaced popular_searches theme setting with daily aggregate search counts. Count successful first-page product searches, normalize case/whitespace, exclude known bots, previews and staff. Cookie dedup once per term/browser/day (20 term limit); no stored visitor IDs or IPs. Restrict input to 2-60 letters/spaces/apostrophes/hyphens. Rank last 30 UTC days, minimum 3 counts, limit 3; no fallback. Daily expiry via WP-Cron; five-minute cache invalidates on count. Search result full-page caches must be bypassed. Pen desktop/tablet/mobile modal frames now show empty history state with dynamic behavior in context. Regression test tools/test-search-popularity.php rolls back synthetic data.
