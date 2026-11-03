---
aimem: 1
id: dec-pb2y41nz
type: decision
title: Bound the editorial pair together to avoid oversized image gaps
summary: Center a viewport-height-bounded pair; use a small image at half the large image width and an inter-image gap at one quarter. Keep mobile single-image behavior.
status: superseded
trust: agent
roles: [all]
paths: [docs/design/miroir.pen, wp-content/themes/miroir-theme/assets/css/home.css, tools/visual-diff/verify-hifi.js]
author: agent:codex
created: 2026-11-03T07:17:19Z
updated: 2026-11-03T07:40:49Z
links: [dec-bf8ede4p]
superseded_by: dec-3km1d8mt
---

User requested tightening Browse Editorial spacing with shopatvelvet.com as the visual reference. The old image height cap shrank individual images inside full-width equal columns, causing gap inflation on wide displays. Bound the row and aligned caption to twice the viewport-capped large-image width instead, keeping the overall section full-width and its existing vertical spacing. Large image remains 3:4; small is 50% of large width, centered in its half-column, so gap is 25% of large width. At 1440×900: pair1110, large555×740, small277.5×370, gap138.75 (previous315.62). Tablet768: large352, small176, gap88. Mobile retains centered smaller-image-only behavior and existing size. Synced shared Pen component and all six lo-fi/hi-fi instances. Live browser geometry checks pass for both orientations at nine viewport sizes from375 to2560 wide; verify-hifi.js now asserts alignment/proportions.
