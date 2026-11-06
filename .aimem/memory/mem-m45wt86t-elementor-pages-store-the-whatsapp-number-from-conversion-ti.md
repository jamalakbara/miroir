---
aimem: 1
id: mem-m45wt86t
type: memory
title: Elementor pages store the WhatsApp number from conversion time
summary: Elementor conversion saves rendered content, so _elementor_data can hold old wa.me numbers. The the_content filter rewrites bare wa.me links to miroir_wa_number.
status: active
trust: verified
roles: [all]
tags: [whatsapp, elementor]
paths: [wp-content/themes/miroir-theme/inc/template-tags.php, wp-content/themes/miroir-theme/inc/elementor/cli.php]
author: human:akbar
created: 2026-11-06T09:53:07Z
updated: 2026-11-06T09:53:07Z
verified: 2026-11-06
supersedes: mem-2ekd5hy8
kind: gotcha
---

Contact, Payment confirmation, Track order and Size guide held placeholder wa.me/6281200000000 in _elementor_data, locally and on Hostinger. Fixed at render time with no database edit, so live needs inc/template-tags.php deployed. Links with a prefilled text query are left alone. Verify with curl on those pages and grep wa.me.
