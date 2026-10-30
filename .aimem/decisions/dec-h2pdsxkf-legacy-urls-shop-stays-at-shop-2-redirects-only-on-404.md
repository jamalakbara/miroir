---
aimem: 1
id: dec-h2pdsxkf
type: decision
title: "Legacy URLs: shop stays at /shop-2/, redirects only on 404"
summary: Shop page slug shop-2 to match legacy; miroir-redirects plugin 301s junk product slugs and out-of-range pagination only when WP would 404
status: proposed
trust: agent
roles: [all]
paths: [wp-content/plugins/miroir-redirects/**, docs/migration/legacy-urls.csv]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-30T21:04:20Z
updated: 2026-10-30T21:04:20Z
links: [tsk-gw91zc27, mem-emqm9rkc]
---

Scope agreed with user (2026-10-31): product URLs (/product/<slug>/) and shop-by-collection URLs are out of scope for now; every other legacy URL must work on the new store.

Decisions:
- The WooCommerce shop page slug is shop-2 so legacy /shop-2/ is identical (set by wp miroir demo seed). /shop/ is not used.
- wp-content/plugins/miroir-redirects hooks template_redirect at priority 1 and acts only when is_404(): /product/<digits>/ and /product/__trashed/ -> shop page; /product-category/.../page/N/, /product-tag/.../page/N/, /shop-2/page/N/ -> page 1 of the same listing. In-range pagination keeps working because it is not a 404.
- Added legacy category baju > women (only seen in pagination URLs). /product-category/at-the-studio/ resolves to the nested shop-by-collection term, no extra term needed.

Rejected: always redirecting legacy pagination (would break real page 2+ on the new store); renaming to /shop/ plus a 301 (user wants identical URLs).

Result: 46 in-scope rows checked locally, 19 return 200 and 27 return a 301 to a 200 page; new_url_or_action filled with keep / 301 <path>. The xlsx copy was not updated.
