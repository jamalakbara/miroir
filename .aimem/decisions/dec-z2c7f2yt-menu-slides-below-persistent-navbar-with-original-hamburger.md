---
aimem: 1
id: dec-z2c7f2yt
type: decision
title: Menu slides below persistent navbar with original hamburger morph
summary: Keep the actual navbar visible; restore thin 30x20 hamburger, morph to X, reveal drawer below with no heading or separate close button.
status: proposed
trust: verified
roles: [all]
author: human:akbar
created: 2026-11-01T17:13:49Z
updated: 2026-11-01T17:13:49Z
verified: 2026-11-01
supersedes: dec-a36q4j0m
---

User rejected replacement 24px hamburger and requested previous thin-line design, morph to X, a downward drawer below the still-visible navbar, and no Menu title. Move the actual .site-nav into the native drawer dialog while open, leaving a height placeholder in its original position; restore it on close or search/currency transition. This keeps live bag/logo/toggle and native focus containment without duplicating the header. Drawer content reveals down in 280ms and closes up in 220ms, only content scrolls, background position preserved, reduced-motion skips animation. Preserve visible WordPress toolbar above navbar. Pen.dev shared header and screen 31 states updated through MCP.
