---
aimem: 1
id: dec-3km1d8mt
type: decision
title: Editorial experience uses width-led Velvet-style canvases
summary: Browse and triptych share 8:5 desktop/tablet canvases, max1280px; mobile uses portrait stages. Preserve photos, gutter captions and native scrolling; remove viewport-height fitting.
status: proposed
trust: agent
roles: [all]
paths: [docs/design/miroir.pen, wp-content/themes/miroir-theme/assets/css/home.css, wp-content/themes/miroir-theme/assets/css/tokens.css, wp-content/themes/miroir-theme/inc/customizer/registry.php, tools/visual-diff/verify-hifi.js]
author: agent:codex
created: 2026-11-03T07:40:49Z
updated: 2026-11-03T07:40:49Z
links: [dec-wk9mzwwk]
supersedes: dec-pb2y41nz
---

User requested both editorial sections feel like shopatvelvet.com and supplied local/reference screenshots. Measured reference: desktop/tablet campaign artboards8:5 with bounded content width, large portrait4:5, secondary3:4 at half large width; sale artwork inside the same white canvas. Mobile uses5:7 Browse and2:3 sale canvases. Implemented max1280px width, no viewport-height sizing, 64px top/32px bottom desktop/tablet,112px top/48px bottom mobile. Keep existing large-left yellow/small-right navy Miroir art direction and caption56/32/20px gutter with18px separation; no new copy, overlays, parallax or scroll snap. Large uses4:5 cover crop; triptych retains2049:1152 contain fit at62.5% stage width desktop/tablet and full width mobile, with upper-quarter placement on mobile. Added editable editorial canvas/leading-space settings rather than tying them to product-strip spacing. Synced12 sections across both fidelities, shared component and tokens;11 live viewport checks passed in both orientations, including height invariance. This supersedes previous height-capped geometry, not the original caption-gutter requirement.
