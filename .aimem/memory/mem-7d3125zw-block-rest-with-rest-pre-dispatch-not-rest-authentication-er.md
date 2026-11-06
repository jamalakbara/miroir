---
aimem: 1
id: mem-7d3125zw
type: memory
title: Block REST with rest_pre_dispatch, not rest_authentication_errors
summary: WooCommerce Store API returns true from rest_authentication_errors, so a gate there leaks products. Basic Auth headers also turn REST responses into 401 during tests.
status: active
trust: verified
roles: [all]
paths: [wp-content/themes/miroir-theme/inc/staging-access.php]
author: human:akbar
created: 2026-11-06T13:20:50Z
updated: 2026-11-06T13:20:50Z
verified: 2026-11-06
kind: gotcha
---

Found when /wp-json/wc/store/v1/products returned 200 without the gate cookie. Test gated routes without any Authorization header. REMOTE_ADDR on Hostinger behind hcdn is the real visitor IP, so per-IP attempt limits work. Wrong-password lockout is 10 attempts for 15 minutes.
