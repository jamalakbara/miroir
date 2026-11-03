---
aimem: 1
id: mem-4ezkay3a
type: memory
title: overflow-x auto on carousels makes overflow-y auto too
summary: Carousel tracks with overflow-x:auto get overflow-y:auto; 0.5px vertical overflow makes them steal vertical wheel scroll.
status: active
trust: agent
roles: [all]
tags: [carousel, scroll, css]
paths: [wp-content/themes/miroir-theme/assets/css/home.css, wp-content/themes/miroir-theme/assets/css/product.css]
author: agent:claude-haiku-5-5
created: 2026-11-03T08:26:08Z
updated: 2026-11-03T08:26:08Z
kind: gotcha
---

Trace: .home-strip__grid (home.css:65-73) sets overflow-x:auto, which forces overflow-y to auto. Live check on localhost:8080: track scrollHeight 80 vs clientHeight 79; scrollTop=50 clamps to 0.5. Fix: overflow-y: clip on the strip track and product.css .pdp__track; also check card__link fractional height.
