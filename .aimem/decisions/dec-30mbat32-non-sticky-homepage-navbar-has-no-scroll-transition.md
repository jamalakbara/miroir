---
aimem: 1
id: dec-30mbat32
type: decision
title: Non-sticky homepage navbar has no scroll transition
summary: Keep the overlay navbar transparent while visible and let it scroll away without changing paint state.
status: accepted
trust: agent
roles: [all]
tags: [header, navigation, animation]
paths: [wp-content/themes/miroir-theme/assets/css/header.css, wp-content/themes/miroir-theme/assets/js/theme.js, tools/visual-diff/check-header-overlay.cjs]
author: agent:codex
created: 2026-11-03T03:45:18Z
updated: 2026-11-03T03:45:18Z
links: [dec-qbzgch50]
supersedes: dec-da8sn68v
---

Because the homepage navbar is no longer fixed, remove its 280ms background/color/logo transitions and remove the scroll listener that added home-header-solid after 16px. The navbar remains transparent over the hero until it naturally leaves the viewport. Dialog-open and focus-visible selectors still force the readable solid state when needed, and the explicit Solid header option remains unchanged.
