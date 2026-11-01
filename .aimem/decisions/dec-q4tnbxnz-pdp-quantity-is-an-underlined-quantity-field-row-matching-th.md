---
aimem: 1
id: dec-q4tnbxnz
type: decision
title: PDP quantity is an underlined Quantity Field row matching the size select
summary: "User rejected the boxed stepper on PDP as off-system. Full-width 36px underline row: 'Quantity' left, minus/value/plus icons right; WA link and choose-size state follow qty."
status: proposed
trust: agent
roles: [all]
tags: [pdp, quantity, whatsapp]
paths: [wp-content/themes/miroir-theme/template-parts/product-buy.php, wp-content/themes/miroir-theme/assets/css/product.css, wp-content/themes/miroir-theme/assets/js/theme.js, wp-content/plugins/miroir-whatsapp-order/assets/product.js]
author: agent:claude-code:claude-opus-5-5
created: 2026-11-01T19:03:39Z
updated: 2026-11-01T19:03:39Z
supersedes: dec-1qbdnxss
---

User (2026-11-02) disliked the boxed Qty Stepper (y40JRr) on the PDP: it did not fit the underlined size select and outline buttons. Replaced with Pen.dev component Quantity Field (nCGKo): same 36px height and hairline underline as the select, ink-2 'Quantity' label (editable text quantity_label), lucide minus/plus 14px in 28px hit areas flush right, ink-3 when at a limit, value centred 24px. Theme classes .qty-field*; the boxed .qty stepper stays for the bag drawer/cart. Behaviour unchanged: posts WooCommerce quantity, clamps to stock/variation limits, hidden for sold-individually, WhatsApp message uses qty and line total, Add to bag without size carries ?quantity to choose-size state 44. All nine Pen buy boxes (03, 39, navbar bag x desktop/tablet/mobile) use the Quantity Field.
