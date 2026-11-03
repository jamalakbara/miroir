---
aimem: 1
id: hnd-13qx9rmt
type: handoff
title: "Handoff 2026-11-03: Traced carousel vertical-scroll stuck. theme.js drag handler only activates on horizontal >=6px; th…"
summary: "Done: Traced carousel vertical-scroll stuck. theme.js drag handler only activates on horizontal >=6px; the real trigger is CSS: .home-strip__grid overflow-x:auto forces overflow-y:auto, and card__lin…"
status: final
trust: agent
roles: [all]
author: agent:claude-haiku-5-5
created: 2026-11-03T08:26:04Z
updated: 2026-11-03T08:26:04Z
branch: main
---

## Done
Traced carousel vertical-scroll stuck. theme.js drag handler only activates on horizontal >=6px; the real trigger is CSS: .home-strip__grid overflow-x:auto forces overflow-y:auto, and card__link overflows track by 0.5-1px so track is a vertical scroller that eats vertical wheel/trackpad gestures. Verified live on localhost:8080. No code changed.

## Not done
Fix not applied; exact trackpad latch behavior not reproduced with synthetic events. product.css .pdp__track has same overflow-x:auto pattern, not checked live.

## Next
Set overflow-y: clip on .home-strip__grid (home.css) and .pdp__track (product.css) and re-test vertical wheel over strips.

## Blocked
none

## Notes for next agent
none
