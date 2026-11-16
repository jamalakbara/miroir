---
aimem: 1
id: dec-77zvrw1x
type: decision
title: "Banner widget has Image width option: Framed (default) or Full width"
summary: miroir/banner + Elementor Banner get width attr framed|full. Full = edge-to-edge image at natural ratio, no 8:5 canvas; caption stays on gutter.
status: proposed
trust: agent
roles: [all]
tags: [elementor, home, banner]
paths: [wp-content/themes/miroir-theme/template-parts/home-banner.php, wp-content/themes/miroir-theme/assets/css/home.css, wp-content/themes/miroir-theme/inc/elementor/widgets.php]
author: agent:claude-code:opus-5.5
created: 2026-11-16T16:16:18Z
updated: 2026-11-16T16:16:18Z
---

User asked for an Elementor option to show the triptych banner full width. Default stays framed (62.5% inside 8:5 canvas) so existing pages are unchanged. Full mode: .home-banner--full removes side gutter, image position static height auto (own ratio, all breakpoints). Selectors doubled to beat the max-width:599px .home-banner > a rule.
