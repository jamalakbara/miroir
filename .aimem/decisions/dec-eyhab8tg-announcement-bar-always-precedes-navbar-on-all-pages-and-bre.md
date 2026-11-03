---
aimem: 1
id: dec-eyhab8tg
type: decision
title: Announcement bar always precedes navbar on all pages and breakpoints
summary: User requested global announcement-first order in lo-fi, hi-fi and storefront, including both homepage styles and open navigation menus. Non-sticky scrolling remains unchanged.
status: proposed
trust: agent
roles: [all]
tags: [announcement, header, responsive, design]
paths: [docs/design/miroir.pen, wp-content/themes/miroir-theme/assets/css/header.css, wp-content/themes/miroir-theme/assets/js/theme.js, tools/visual-diff/check-announcement-order.cjs]
author: agent:pi
created: 2026-11-03T03:54:06Z
updated: 2026-11-03T03:54:06Z
---

This replaces the announcement-below-navbar portion of dec-dwscr3kn; existing logo/action grouping remains unchanged. Global site-header is a vertical layout with announcement order -1; compact order1 override removed. All page types and transparent/solid homepage modes now share announcement-first order. Native menu offsets reserve the actually visible announcement bottom plus toolbar protection on every page, not only overlay homepage, while bag drawer offsets remain unchanged. Updated89 Pen announcement overrides (83 lo-fi,4 hi-fi,2 solid-option previews), re-enabled announcement in3 nav-open states and resized their content/currency offsets to fit. All145 Header references in saved miroir.pen validated. Preserved subsequent non-sticky/no-scroll-paint decisions dec-qbzgch50 and dec-30mbat32. Added dependency-free check-announcement-order.cjs tests.
