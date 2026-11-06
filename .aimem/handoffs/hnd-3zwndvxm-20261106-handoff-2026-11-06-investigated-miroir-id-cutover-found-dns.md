---
aimem: 1
id: hnd-3zwndvxm
type: handoff
title: "Handoff 2026-11-06: Investigated miroir.id cutover. Found DNS on Cloudflare, old WordPress.com site suspended, no MX. L…"
summary: "Done: Investigated miroir.id cutover. Found DNS on Cloudflare, old WordPress.com site suspended, no MX. Live site options read: maintenance off, home is temp domain. Existing theme maintenance mode c…"
status: final
trust: verified
roles: [all]
author: human:akbar
created: 2026-11-06T08:25:09Z
updated: 2026-11-06T08:25:09Z
verified: 2026-11-06
branch: main
---

## Done
Investigated miroir.id cutover. Found DNS on Cloudflare, old WordPress.com site suspended, no MX. Live site options read: maintenance off, home is temp domain. Existing theme maintenance mode covers the page and admin bypass.

## Not done
No DNS, hosting or code change. Strategy not yet chosen by user.

## Next
User confirms Cloudflare access. Then: maintenance on, connect miroir.id to the Hostinger site, update URLs, verify. Optional client preview link needs code. Launch gates: tsk-gw91zc27, Googlebot robots rule check, test orders.

## Blocked
Need to know who controls the Cloudflare zone.

## Notes for next agent
maintenance.php returns 503 with Retry-After 3600 and bypasses only manage_options users. Temp site robots.txt blocks Googlebot; check whether miroir.id still does after connecting.
