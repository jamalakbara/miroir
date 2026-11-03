---
aimem: 1
id: mem-t3w0d385
type: memory
title: Hero video encode settings
summary: "Hero MP4s: H.264 CRF 24 preset slow, 30fps, no audio, faststart; desktop 2560 wide, mobile 1080x1440. Re-encode new client films the same way."
status: active
trust: agent
roles: [all]
tags: [video, hero]
paths: [wp-content/themes/miroir-theme/assets/video/*]
author: agent:claude
created: 2026-11-03T06:42:50Z
updated: 2026-11-03T06:42:50Z
links: [dec-xybdawcw]
---

ffmpeg -i in.mp4 -vf 'scale=W:-2:flags=lanczos,fps=30' -c:v libx264 -preset slow -crf 24 -profile:v high -pix_fmt yuv420p -an -movflags +faststart out.mp4 (W=2560 desktop, 1080 mobile). Client originals were 4320x1920 / 2494x3324 at 10-14 Mbps (14 MB / 18 MB); result 4.4 MB / 3.8 MB with no visible loss. ffmpeg installed via Homebrew 2026-11-03.
