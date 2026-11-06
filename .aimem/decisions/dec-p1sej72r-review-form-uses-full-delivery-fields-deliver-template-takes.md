---
aimem: 1
id: dec-p1sej72r
type: decision
title: Review form uses full delivery fields; deliver template takes one joined address
summary: Adds required phone, postcode, province (when country has states), optional email and line 2. Deliver template default is {address}, joined by full_address() and checkout.js.
status: proposed
trust: verified
roles: [all]
paths: [wp-content/plugins/miroir-whatsapp-order/**, wp-content/themes/miroir-theme/woocommerce/checkout/**]
author: human:akbar
created: 2026-11-06T08:45:08Z
updated: 2026-11-06T08:45:08Z
verified: 2026-11-06
supersedes: dec-j1zrkt5f
---

User asked 2026-11-06 for more than street address, based on the WooCommerce admin billing form. Skipped company, payment method and transaction ID because payment is outside the site. Province select follows country via localized states and hides when a country has none. Providing email sets billing_email, so WooCommerce customer on-hold email now goes out for those orders. Colour filter removal from the superseded record still stands.
