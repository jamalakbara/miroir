---
aimem: 1
id: mem-gx714c22
type: memory
title: Bundled hero media URLs carry ?ver=filemtime
summary: home-video-hero.php appends ?ver=filemtime to bundled posters/videos so replacing a file busts browser cache; posters are first frames of the films.
status: active
trust: agent
roles: [all]
tags: [video, hero, cache]
paths: [wp-content/themes/miroir-theme/template-parts/home-video-hero.php]
author: agent:claude
created: 2026-11-03T06:53:57Z
updated: 2026-11-03T06:53:57Z
links: [mem-t3w0d385]
---

Posters regenerate from frame 0: desktop 1920x854 from desktop film, tablet 1536x864 center 16:9 crop of desktop film (tablet plays desktop film with object-fit cover), mobile 780x1040 from mobile film; cwebp -q 80 -m 6. Copy to both theme assets/images and docs/design/images/hifi (Pen uses the docs copies).
