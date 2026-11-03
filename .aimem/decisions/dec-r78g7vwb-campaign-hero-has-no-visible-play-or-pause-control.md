---
aimem: 1
id: dec-r78g7vwb
type: decision
title: Campaign hero has no visible play or pause control
summary: Remove hero playback buttons in all responsive lo-fi/hi-fi designs and the storefront; retain muted autoplay, preference-aware posters and offscreen/tab suspension.
status: accepted
trust: agent
roles: [all]
paths: [docs/design/miroir.pen, wp-content/themes/miroir-theme/template-parts/home-video-hero.php, wp-content/themes/miroir-theme/assets/js/theme.js]
author: agent:codex
created: 2026-11-03T07:00:53Z
updated: 2026-11-03T07:00:53Z
links: [dec-084bj0sn]
---

User explicitly requested removing the play/pause button on mobile, tablet and desktop in both design fidelity levels and the local storefront. Removed 15 canvas controls including lo-fi search/currency backgrounds and solid-header previews. Storefront no longer renders or requires a button; muted inline looping playback, reduced-motion/save-data poster fallback and visibility suspension remain. Only the playback-control portion of dec-084bj0sn changes; campaign/editorial layout is preserved.
