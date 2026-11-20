---
aimem: 1
id: dec-3n1dn4z9
type: decision
title: Legacy product and listing URLs missing on new store 301 to shop page
summary: Any 404 /product/<slug>/ goes to shop; dead category/tag listings go to closest existing parent term, else shop; all in one 301 hop.
status: superseded
trust: agent
roles: [all]
paths: [wp-content/plugins/miroir-redirects/**]
author: agent:claude-code
created: 2026-11-20T06:21:16Z
updated: 2026-11-20T06:26:34Z
links: [tsk-gw91zc27, mem-emqm9rkc]
superseded_by: dec-8hwvjdbx
---

133 legacy product URLs in docs/migration/legacy-urls.csv are not in the new 18-product catalog and would 404 at launch. miroir-redirects 0.2.0 resolves them only when WordPress would 404, so live products keep their own URL. Proposed by agent 2026-11-20 at go-live prep; not yet deployed. Alternative (410 Gone / per-product mapping) rejected for now: no per-product equivalents exist.
