---
aimem: 1
id: mem-8f74c24k
type: memory
title: WhatsApp appends prefilled text to an unsent chat draft
summary: Repeat tests in one chat can show two orders joined in the compose box. Clear the draft first; site code cannot control it.
status: active
trust: verified
roles: [all]
paths: [wp-content/plugins/miroir-whatsapp-order/assets/sent.js]
author: human:akbar
created: 2026-11-05T22:41:14Z
updated: 2026-11-05T22:41:14Z
verified: 2026-11-05
kind: gotcha
---

A pasted compose box held old unsent order 455 immediately followed by new order 453 with no separator. Order 453 was saved correctly on its own, so the join happened in WhatsApp.
