---
aimem: 1
id: dec-xybdawcw
type: decision
title: Client campaign films bundled in theme as default hero videos
summary: Desktop 4320x1920 (9:4) and mobile 2494x3324 (3:4) client MP4s ship in miroir-theme/assets/video; uploaded Elementor/block videos override them.
status: proposed
trust: agent
roles: [all]
tags: [video, hero, home]
paths: [wp-content/themes/miroir-theme/template-parts/home-video-hero.php, wp-content/themes/miroir-theme/assets/video/*]
author: agent:claude
created: 2026-11-03T06:38:46Z
updated: 2026-11-03T06:38:46Z
links: [dec-084bj0sn]
---

Client supplied Drive files 'video poster desktop (2).mp4' and 'web mobile video poster (1).mp4'. Bundled in theme (uploads/ is gitignored, so media library import would not be repeatable). Template falls back to them when the video/videoMobile attrs are empty. Files are 14 MB / 18 MB at very high resolution; a compressed 1080p-class encode would cut load weight (no ffmpeg on host at the time).
