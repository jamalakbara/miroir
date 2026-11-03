---
aimem: 1
id: dec-qrtt9jv2
type: decision
title: Campaign hero media has no dark overlay
summary: Remove the hero contrast scrim/gradient from all responsive lo-fi/hi-fi designs and the local storefront; keep the existing transparent navigation positioning.
status: accepted
trust: agent
roles: [all]
paths: [docs/design/miroir.pen, wp-content/themes/miroir-theme/assets/css/header.css]
author: agent:codex
created: 2026-11-03T07:06:38Z
updated: 2026-11-03T07:06:38Z
links: [dec-br2a5vdx]
---

User rejected the dark overlay on the campaign hero after playback controls were removed. Removed 15 canvas scrim nodes (9 lo-fi including modal backgrounds, 3 hi-fi, 3 disabled solid-header previews) and the storefront hero media ::after gradient at all breakpoints. Video/poster imagery is unshaded. Navbar positioning, autoplay, captions and hero dimensions are unchanged.
