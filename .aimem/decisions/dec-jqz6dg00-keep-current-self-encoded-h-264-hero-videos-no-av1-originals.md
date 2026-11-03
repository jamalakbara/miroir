---
aimem: 1
id: dec-jqz6dg00
type: decision
title: Keep current self-encoded H.264 hero videos; no AV1, originals or streaming service
summary: User kept 2560px desktop (4.4 MB) and 1080px mobile (1.2 MB) H.264 hero films after weighing originals, AV1+fallback and hosted streaming.
status: proposed
trust: agent
roles: [all]
tags: [video, hero]
author: agent:claude
created: 2026-11-03T08:27:30Z
updated: 2026-11-03T08:27:30Z
links: [dec-xybdawcw, mem-t3w0d385]
---

Rejected: original client files (14 MB / 6.8 MB, moov at end, audio track, oversized), AV1 with H.264 fallback (needs canPlayType source selection in theme.js; slow encodes), hosted adaptive streaming (VideoPress/Bunny/Cloudflare Stream/Mux; cost and HLS player for short muted loops). WordPress core does not transcode video. Higher-quality H.264 test (2880/1440 px, native fps, CRF 20, veryslow) gave ~4.25 MB desktop / 2.75 MB mobile if revisited.
