---
aimem: 1
id: mem-hfejpyx7
type: memory
title: Image skeleton must not treat naturalWidth 0 as broken
summary: With srcset, naturalWidth is density-corrected; at zero viewport width it reads 0 for a loaded photo and no load event follows.
status: active
trust: verified
roles: [all]
tags: [images, theme-js]
paths: [wp-content/themes/miroir-theme/assets/js/theme.js]
author: human:akbar
created: 2026-11-09T05:29:04Z
updated: 2026-11-09T05:29:04Z
verified: 2026-11-09
links: [dec-jr10fq3d]
---

Seen when a page loads before its window has width (browser pane opening, possibly background tabs or prerender): img.complete is true, naturalWidth 0, then naturalWidth becomes 1024 later with no load event, so the old settle() left has-image-error and a grey fallback forever. watchImages now uses load/error events, and for complete images with width 0 trusts image.decode(): resolve means loaded, reject means broken. Reproduce by opening a not-yet-cached product URL in a freshly opened browser pane.
