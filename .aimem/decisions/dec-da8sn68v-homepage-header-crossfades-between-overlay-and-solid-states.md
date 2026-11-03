---
aimem: 1
id: dec-da8sn68v
type: decision
title: Homepage header crossfades between overlay and solid states
summary: Animate background, navigation foreground and logo treatment for 280ms with the existing site easing; reduced motion remains instant.
status: accepted
trust: agent
roles: [all]
tags: [header, animation, accessibility]
paths: [wp-content/themes/miroir-theme/assets/css/header.css, tools/visual-diff/check-header-overlay.cjs]
author: agent:codex
created: 2026-11-03T03:40:24Z
updated: 2026-11-03T03:40:24Z
links: [dec-br2a5vdx]
---

Keep the existing 16px scroll threshold and visual endpoints. Transition only paint properties (background-color, color and custom-logo filter) for 280ms using cubic-bezier(0.22, 1, 0.36, 1), so layout does not shift and reversal stays interruptible. Disable these transitions under prefers-reduced-motion. The announcement bar remains solid and unchanged.
