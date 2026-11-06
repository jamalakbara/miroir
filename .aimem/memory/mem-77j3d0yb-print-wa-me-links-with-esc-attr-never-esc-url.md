---
aimem: 1
id: mem-77j3d0yb
type: memory
title: Print wa.me links with esc_attr, never esc_url
summary: esc_url strips %0A, flattening WhatsApp messages. Sent, view-order, PDP buy and state links now use esc_attr; links are built from fixed wa.me base.
status: active
trust: verified
roles: [all]
paths: [wp-content/themes/miroir-theme/woocommerce/checkout/thankyou.php, wp-content/themes/miroir-theme/woocommerce/myaccount/view-order.php, wp-content/themes/miroir-theme/template-parts/product-buy.php, wp-content/themes/miroir-theme/template-parts/product-state.php]
author: human:akbar
created: 2026-11-05T22:33:42Z
updated: 2026-11-05T22:33:42Z
verified: 2026-11-05
supersedes: mem-2f087ftk
kind: gotcha
---

WordPress esc_url() removes encoded CR/LF outside mailto links, so a prefilled wa.me message arrives on one line. Links without a text parameter, such as maintenance-body.php, can keep esc_url.
