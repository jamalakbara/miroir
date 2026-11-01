---
aimem: 1
id: dec-f06vg0zb
type: decision
title: "Storefront is edited in Elementor (free): Miroir section widgets render the theme's blocks"
summary: "User chose (2026-11-01, branch elementor): Elementor manages every screen via section widgets + Site Settings > Miroir; block templates remain the 1:1 fallback"
status: proposed
trust: agent
roles: [all]
tags: [elementor, theme, editing]
paths: [wp-content/themes/miroir-theme/inc/elementor/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-11-01T10:13:24Z
updated: 2026-11-01T10:13:24Z
links: [dec-4a5wfd9e, dec-w31np48s]
---

User asked for the whole store to be managed in Elementor (free), not optional, design kept 1:1, work on its own branch (elementor). Chosen: section widgets (one per section, inner layout fixed) over fine-grained widgets in Elementor containers, because containers take over layout and break 1:1. Settings move to Elementor Site Settings: Global Colors/Fonts (miroir_* ids map onto the theme.json palette and --font-sans/--font-mono) and a Miroir tab (templates per screen, miroir_settings(), all miroir_texts() labels). Customizer Miroir panels and Appearance > Editor are hidden while Elementor is active. Elementor becomes a required plugin (supersedes the no-third-party-dependency part of dec-w31np48s); Site Editor is no longer the editing surface (dec-4a5wfd9e) but its block templates stay as the fallback when Elementor is off or a screen has no template. Header/footer/product/collection/search/text page/404/maintenance are Elementor library templates; home, bag, checkout, account are Elementor pages. Rejected: Elementor Pro Theme Builder (paid, replaces theme templates).
