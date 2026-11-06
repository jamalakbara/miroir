---
aimem: 1
id: dec-j1zrkt5f
type: decision
title: Review form collects a required street address
summary: wa_address saves to billing and shipping address_1 and joins the Deliver to line; colour filter removed from sidebar and panel at client request.
status: superseded
trust: verified
roles: [all]
paths: [wp-content/plugins/miroir-whatsapp-order/**, wp-content/themes/miroir-theme/woocommerce/checkout/**]
author: human:akbar
created: 2026-11-06T08:28:43Z
updated: 2026-11-06T08:45:08Z
verified: 2026-11-06
superseded_by: dec-p1sej72r
---

Client feedback 2026-11-06. Address sits in the existing miroir_wa_deliver template as {address}, so a customised live template without {address} would drop it from the message: check that option on each environment. Requirement toggled by miroir_wa_require_address. Client also asked to remove the colour menu; size stays in the filter panel. Payment copy says bank transfer or PayPal; client wrote and, changed to or because customers use one.
