---
aimem: 1
id: dec-br10kbdt
type: decision
title: WhatsApp opens in a tab reserved on checkout submit
summary: checkout.js opens named tab during the click; sent.js loads wa.me into it. Falls back to same-tab redirect when no tab is available; refused submits close it.
status: proposed
trust: verified
roles: [all]
paths: [wp-content/plugins/miroir-whatsapp-order/assets/checkout.js, wp-content/plugins/miroir-whatsapp-order/assets/sent.js]
author: human:akbar
created: 2026-11-05T22:46:38Z
updated: 2026-11-05T22:46:38Z
verified: 2026-11-05
---

window.open on the sent page alone would be popup-blocked because the link only exists after the POST and redirect, outside the click. Fallback screen runs only after same-tab redirect, since the sent page staying visible proves nothing when WhatsApp has its own tab.
