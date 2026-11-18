---
aimem: 1
id: mem-3pvgbxyr
type: memory
title: Manual guide page 28 contains a blank WhatsApp settings screenshot
summary: wa-settings.png is all white, so the rendered page 28 figure is blank and must be recaptured before delivery
status: active
trust: agent
roles: [all]
paths: [docs/manual/content.html, docs/design/miroir-assets/manual-guide/shots/wa-settings.png]
author: agent:codex
created: 2026-11-18T07:24:57Z
updated: 2026-11-18T07:24:57Z
links: [mem-c1ebj1a5]
kind: gotcha
---

Visual QA on 2026-11-18 found that docs/design/miroir-assets/manual-guide/shots/wa-settings.png (and crop/wa-settings-12.png) is completely white. It renders as a large empty bordered figure on Manual guide / 28 — WhatsApp orders. Recapture WooCommerce → Settings → WhatsApp ordering, then rerun docs/manual/build.mjs and slice.mjs. Other reviewed screenshots rendered normally.
