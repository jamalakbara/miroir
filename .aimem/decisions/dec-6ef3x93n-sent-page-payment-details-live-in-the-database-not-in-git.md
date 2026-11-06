---
aimem: 1
id: dec-6ef3x93n
type: decision
title: Sent page payment details live in the database, not in git
summary: Bank line reads woocommerce_bacs_accounts; PayPal line reads miroir_wa_paypal, a field in WhatsApp ordering settings. Set both per environment.
status: proposed
trust: verified
roles: [all]
paths: [wp-content/themes/miroir-theme/woocommerce/checkout/thankyou.php]
author: human:akbar
created: 2026-11-06T08:48:53Z
updated: 2026-11-06T08:48:53Z
verified: 2026-11-06
links: [dec-fa3qjtxf]
---

Account values were supplied by the user 2026-11-06 and are deliberately not stored in the repo or in aimem. The bank gateway stays disabled by the plugin; only the accounts option is read. Empty PayPal setting hides the line.
