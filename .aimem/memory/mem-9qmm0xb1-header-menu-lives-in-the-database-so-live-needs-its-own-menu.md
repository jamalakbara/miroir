---
aimem: 1
id: mem-9qmm0xb1
type: memory
title: Header menu lives in the database, so live needs its own menu update
summary: "Theme files do not change an existing Header menu; miroir theme setup skips menus that exist. Sale red is matched by link; overlay navbar uses lighter #ff5a4f."
status: active
trust: verified
roles: [all]
tags: [navbar, menu, hostinger]
paths: [wp-content/themes/miroir-theme/assets/css/header.css, wp-content/themes/miroir-theme/inc/cli.php]
author: human:akbar
created: 2026-11-05T23:09:57Z
updated: 2026-11-05T23:09:57Z
verified: 2026-11-05
kind: gotcha
---

The navbar and mobile drawer both render the primary location, which is the Header menu stored in the database. Deploying theme files does not change it, because menus() in inc/cli.php leaves existing menus alone. Update it per environment with wp menu item commands. wp menu item add-term --position does not reorder reliably; set menu_order with wp post update. Verify with wp menu item list header. Sale red comes from an href selector in header.css. The default red is hard to read on the transparent homepage navbar, so that state sets --sale to #ff5a4f.
