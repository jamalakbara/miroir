---
aimem: 1
id: dec-wk9mzwwk
type: decision
title: Editorial captions retain the original page gutter
summary: Keep Browse Editorial captions aligned to shared page gutters, like Sale Banner; center only the compact image pair, without changing image sizes or spacing.
status: accepted
trust: agent
roles: [all]
paths: [docs/design/miroir.pen, wp-content/themes/miroir-theme/assets/css/home.css, tools/visual-diff/verify-hifi.js]
author: agent:codex
created: 2026-11-03T07:24:46Z
updated: 2026-11-03T07:24:46Z
links: [dec-pb2y41nz]
---

User explicitly requested restoring caption EeEvY to previous padding/margin alignment. Caption stays at shared56px desktop/32px tablet/20px mobile page gutters, matching Sale Banner and other sections. Only the image pair is centered and width-capped; retain the refined1110px desktop pair,555px large/277.5px small image and138.75px gap. Restored desktop Pen section gutter and wrapped each desktop image pair in a centered row while preserving caption IDs and copy; tablet/mobile were already correctly aligned. Removed caption from storefront width-cap/alignment selectors and updated regression checks to assert page-gutter and Sale Banner alignment with original18px vertical gap. This changes only the caption-alignment part of dec-pb2y41nz.
