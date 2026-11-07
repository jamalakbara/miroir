---
aimem: 1
id: mem-d25djc24
type: memory
title: Use update-catalog-photos.php to change product photos
summary: tools/update-catalog-photos.php applies catalog.json photos to named products only; import-catalog.php rewrites prices, sales and copy for all 18.
status: active
trust: verified
roles: [all]
tags: [images, catalog]
paths: [tools/update-catalog-photos.php, tools/import-catalog.php]
author: human:akbar
created: 2026-11-07T14:18:13Z
updated: 2026-11-07T14:18:13Z
verified: 2026-11-07
links: [mem-mzwh3nj7]
---

import-catalog.php resets price, sale price, stock, description and featured on every product, so it is unsafe once wp-admin edits exist. Photos must also be in uploads/miroir-catalog-source, which is gitignored and not deployed; staging and live need those files before running the script there.
