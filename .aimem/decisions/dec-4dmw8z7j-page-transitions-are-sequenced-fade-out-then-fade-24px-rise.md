---
aimem: 1
id: dec-4dmw8z7j
type: decision
title: Page transitions are sequenced fade-out then fade + 24px rise, header pinned
summary: Old page fades out 200ms rising 6px; new page fades in after 150ms over 450ms rising 24px on modal curve. Header/admin bar static; reduced motion off; hover prefetch.
status: proposed
trust: verified
roles: [all]
tags: [design, motion]
paths: [wp-content/themes/miroir-theme/assets/css/transitions.css]
author: agent:claude-code:claude-opus-5-5
created: 2026-11-01T17:59:13Z
updated: 2026-11-01T18:00:09Z
links: [dec-0r4kanpy]
supersedes: dec-whjda914
verified: 2026-11-01
---

User said the first version (160ms out / 240ms in, 8px, simultaneous) did not feel like a page transition: two similar white layouts crossfading read as no motion. Now sequenced: ::view-transition-old(root) 200ms ease-in to opacity 0 / translateY(-6px); ::view-transition-new(root) 450ms cubic-bezier(0.22,1,0.36,1) with 150ms delay from opacity 0 / translateY(24px). Everything else from dec-whjda914 unchanged: native cross-document view transitions (Chrome/Edge 126+, Safari 18.2+; others navigate normally), .site-header and #wpadminbar pinned, reduced motion disables, speculative loading moderate with cart/checkout/my-account excluded.
