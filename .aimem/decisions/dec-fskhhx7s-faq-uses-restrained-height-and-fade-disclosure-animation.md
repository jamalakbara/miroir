---
aimem: 1
id: dec-fskhhx7s
type: decision
title: FAQ uses restrained height and fade disclosure animation
summary: Animate native FAQ details over 280ms open and 220ms close with site easing; preserve Pen spacing, morph plus to minus, and skip motion under reduced-motion.
status: proposed
trust: verified
roles: [all]
paths: [wp-content/themes/miroir-theme/assets/js/theme.js, wp-content/themes/miroir-theme/assets/css/info.css]
author: human:akbar
created: 2026-11-02T17:49:00Z
updated: 2026-11-02T17:49:00Z
verified: 2026-11-02
---

Enhance only .info__content > .wp-block-details in theme.js. Wrap answer nodes at runtime and use cancellable Web Animations for measured height and opacity so closing animates before removing open. Native summary retains keyboard and no-JS behavior; closing content is inert. Match existing cubic-bezier(0.22, 1, 0.36, 1). No stored page content changes. Verified desktop/tablet/mobile, rapid reversal, Enter/Space, reduced-motion and no-JS fallback.
