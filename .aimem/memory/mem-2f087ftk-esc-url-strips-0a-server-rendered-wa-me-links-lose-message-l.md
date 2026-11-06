---
aimem: 1
id: mem-2f087ftk
type: memory
title: "esc_url strips %0A: server-rendered wa.me links lose message line breaks"
summary: PDP Order via WhatsApp href (before a size is picked) is escaped with esc_url, which removes encoded newlines; product.js links keep them
status: superseded
trust: agent
roles: [all]
tags: [whatsapp, gotcha]
paths: [wp-content/themes/miroir-theme/template-parts/product-buy.php, wp-content/plugins/miroir-whatsapp-order/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T18:24:26Z
updated: 2026-11-05T22:33:42Z
kind: gotcha
superseded_by: mem-77j3d0yb
---

Seen 2026-11-01: the initial PDP href reads text=Hi%20miroir...order%3A1.%20Name — the %0A%0A are gone because esc_url() deep-replaces %0a/%0d. Present before the customization work (baseline HTML shows it). After a size is chosen product.js builds the href in JS and the newlines survive. Fix idea: esc_attr() the already-built wa.me URL (it is built from rawurlencode) instead of esc_url(), or use esc_url with a protocol whitelist after confirming no stripping. Not fixed yet.
