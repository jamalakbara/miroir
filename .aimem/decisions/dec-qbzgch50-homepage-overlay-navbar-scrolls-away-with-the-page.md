---
aimem: 1
id: dec-qbzgch50
type: decision
title: Homepage overlay navbar scrolls away with the page
summary: Keep the transparent navbar at the top of the homepage hero, but do not fix or stick it to the viewport.
status: accepted
trust: agent
roles: [all]
tags: [header, navigation, scroll]
paths: [wp-content/themes/miroir-theme/assets/css/header.css, tools/visual-diff/check-header-overlay.cjs]
author: agent:codex
created: 2026-11-03T03:42:54Z
updated: 2026-11-03T03:42:54Z
links: [dec-br2a5vdx, dec-da8sn68v]
---

At the user's request, position the overlay homepage header absolutely at the page top instead of fixing it to the viewport. The announcement and navbar retain their top-of-hero composition and paint-state transitions, while ordinary page scrolling moves the entire header offscreen. Other pages and the Solid header option remain unchanged.
