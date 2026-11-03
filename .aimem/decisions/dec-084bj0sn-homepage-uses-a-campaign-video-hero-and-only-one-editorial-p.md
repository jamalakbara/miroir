---
aimem: 1
id: dec-084bj0sn
type: decision
title: Homepage uses a campaign video hero and only one editorial pair
summary: "Approved: replace hero pair with video; remove soft-structure pair; keep green browse editorial and centered triptych. Sync storefront and lo-fi/hi-fi at all breakpoints."
status: proposed
trust: agent
roles: [all]
tags: [design, homepage, video, elementor]
paths: [docs/design/miroir.pen, wp-content/themes/miroir-theme/*, tools/apply-video-hero.php]
author: agent:pi
created: 2026-11-03T03:07:41Z
updated: 2026-11-03T03:07:41Z
---

User approved the Josephine Anni-inspired video hero and removal of the brown/yellow soft-structure editorial. Home sequence is video hero, New Arrivals, green Browse Editorial, centered triptych, collection products, footer. Desktop1440x640, tablet768x432, mobile390x520 hero media. Existing supplied yellow Miroir photo is a temporary poster, not a video; actual campaign video is still needed. Retained editorial still uses smaller-image-only on mobile. Elementor Campaign video hero widget and miroir/video-hero block support direct MP4/WebM plus optional portrait mobile clip, responsive posters, muted looping inline playback, accessible pause/play, reduced-motion/save-data poster fallback. Idempotent tools/apply-video-hero.php upgrades only the identified hero and obsolete editorial, backing up originals.
