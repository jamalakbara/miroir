---
aimem: 1
id: dec-whjda914
type: decision
title: "Page changes use native cross-document view transitions: fade + 8px rise, header pinned"
summary: CSS-only @view-transition; old page fades 160ms, new fades in rising 8px over 240ms on modal curve; header/admin bar static; reduced motion off; hover prefetch.
status: proposed
trust: agent
roles: [all]
tags: [design, motion]
paths: [wp-content/themes/miroir-theme/assets/css/transitions.css, wp-content/themes/miroir-theme/inc/setup.php, wp-content/themes/miroir-theme/inc/woocommerce.php]
author: agent:claude-code:claude-opus-5-5
created: 2026-11-01T17:55:00Z
updated: 2026-11-01T17:55:00Z
links: [dec-0r4kanpy]
---

User asked (2026-11-02) for smooth page changes and picked 'Fade + soft rise' over pure crossfade and fade-through-white. transitions.css: @view-transition navigation auto; .site-header and #wpadminbar get their own view-transition-name with no animation (old snapshot hidden) so logo/nav/bag never flicker; root old 160ms ease-in fade, root new 240ms cubic-bezier(0.22,1,0.36,1) fade + translateY(8px), matching dec-0r4kanpy. prefers-reduced-motion disables it. Unsupported browsers (no cross-document VT) navigate as before. No JS router: rejected Barba/Swup-style routers because they break WP/Woo/Elementor scripts and admin bar. Core speculative loading (WP 7.1, logged-out only) raised from auto/conservative to moderate eagerness (prefetch on hover) in setup.php; inc/woocommerce.php excludes cart, checkout and my-account paths so a prefetched bag is never stale after add-to-bag. Verified in Chromium 152: pageswap reports viewTransition for nav link and user-submitted search; script-triggered requestSubmit got none (no user activation), which is expected.
