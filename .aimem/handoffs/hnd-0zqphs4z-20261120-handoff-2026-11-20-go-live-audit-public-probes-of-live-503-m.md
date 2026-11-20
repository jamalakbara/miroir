---
aimem: 1
id: hnd-0zqphs4z
type: handoff
title: "Handoff 2026-11-20: Go-live audit: public probes of live (503 maintenance, www no redirect yet, temp-domain dns-prefetc…"
summary: "Done: Go-live audit: public probes of live (503 maintenance, www no redirect yet, temp-domain dns-prefetch in head). miroir-redirects 0.2.0 (uncommitted): 404 legacy products -> shop, dead category/t…"
status: final
trust: agent
roles: [all]
author: agent:claude-code
created: 2026-11-20T06:21:17Z
updated: 2026-11-20T06:21:17Z
task: tsk-gw91zc27
branch: development
---

## Done
Go-live audit: public probes of live (503 maintenance, www no redirect yet, temp-domain dns-prefetch in head). miroir-redirects 0.2.0 (uncommitted): 404 legacy products -> shop, dead category/tag listings -> nearest existing term or shop, single hop. Function checked via wp eval; php -l clean.

## Not done
Not committed/deployed. Legacy CSV check not run against real catalog: local Mac DB is empty (twentytwentyfive, 0 products). SSH to live denied by permission classifier, so live options unchecked.

## Next
User OK -> commit, deploy staging, run CSV check against staging, merge to main, deploy live, then maintenance off and post-launch checks (www redirect, robots, sitemap, /product-category/shop/ 301, OpenSEO audit, Search Console).

## Blocked
Live maintenance off and production deploy need user approval.

## Notes for next agent
none
