---
aimem: 1
id: dec-br2a5vdx
type: decision
title: Homepage navbar can overlay campaign hero transparently or stay solid
summary: Homepage defaults to transparent white nav over video/poster, with solid announcement above. Elementor/Customizer offers Transparent/Solid; other pages stay unchanged.
status: proposed
trust: agent
roles: [all]
tags: [homepage, header, design, elementor]
paths: [docs/design/miroir.pen, wp-content/themes/miroir-theme/assets/css/header.css, wp-content/themes/miroir-theme/assets/js/theme.js, wp-content/themes/miroir-theme/inc/customizer/*]
author: agent:pi
created: 2026-11-03T03:35:26Z
updated: 2026-11-03T03:35:26Z
---

User requested a Josephine Anni-like transparent homepage navbar and a storefront option to disable transparency. Implemented home_header_style select under Elementor Site Settings > Miroir > Header and the matching Customizer section; default transparent. Homepage with campaign hero reserves only the solid40px announcement and overlays a fixed navbar on video/poster, with white logo/text/icons and a hero-only dark contrast scrim. All breakpoints put announcement above video/nav in this mode. Navbar becomes solid/dark-text after16px scroll, keyboard focus-visible or open dialog. Solid option restores original in-flow header and compact ordering; other pages remain unchanged. CSS requires an actual hero; no-JS fallback remains solid/readable. Toolbar offset, sticky focus clearance and mobile drawer40px announcement offset measured in JS. Updated six lo-fi/hi-fi homes, six homepage search/currency backgrounds, and added solid-option previews rtb4F at1440/768/390. Actual video still pending; Miroir poster unchanged.
