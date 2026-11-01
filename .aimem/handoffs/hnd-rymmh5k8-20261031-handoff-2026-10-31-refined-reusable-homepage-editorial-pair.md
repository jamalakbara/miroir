---
aimem: 1
id: hnd-rymmh5k8
type: handoff
title: "Handoff 2026-10-31: Refined reusable homepage editorial pair sizing to fit each section plus caption inside viewport he…"
summary: "Done: Refined reusable homepage editorial pair sizing to fit each section plus caption inside viewport height while preserving original section gutters and padding. home.css uses viewport-based image…"
status: final
trust: verified
roles: [all]
author: human:akbar
created: 2026-10-31T17:18:51Z
updated: 2026-10-31T17:18:51Z
verified: 2026-10-31
branch: main
---

## Done
Refined reusable homepage editorial pair sizing to fit each section plus caption inside viewport height while preserving original section gutters and padding. home.css uses viewport-based image width cap: desktop (100svh - 160px)*0.75; tablet (100svh - 128px)*0.75; stacked mobile (100svh - 132px)*0.46875 with small image 60% of large. Desktop/tablet use two equal grid columns, large image anchored to outer edge and small centered in opposite column. Both images stay 3:4; desktop small/large scale stays 300/664. Updated reusable Pen component Im0MZ and all nine existing desktop/tablet/mobile instances for reference viewports 1440x900,768x1024,390x844. Verified ten viewports including 1920x916,1366x768,1024x600,320x568,844x390: section fits with 20px+ breathing room, matching geometry, original gutters, no overflow. Pen clipping checks empty. At 1920x916 large image now567x756 instead of904x1205. Screenshots in tools/visual-diff/out/editorial-*.png.

## Not done
No commit requested.

## Next
Preserve original outer section spacing and viewport-aware caps in future editorial adjustments.

## Blocked
none

## Notes for next agent
none
