---
aimem: 1
id: hnd-3veprnga
type: handoff
title: "Handoff 2026-11-05: Explained how to move the local Docker store to Hostinger; measured site (914 MB uploads, 43 produc…"
summary: "Done: Explained how to move the local Docker store to Hostinger; measured site (914 MB uploads, 43 products, 17 orders, 8 users; PHP 8.3, WP 7.1.4) and confirmed Hostinger's upload route for offline…"
status: final
trust: agent
roles: [all]
author: agent:claude:claude-opus-5-5
created: 2026-11-05T17:25:29Z
updated: 2026-11-05T17:25:29Z
branch: main
---

## Done
Explained how to move the local Docker store to Hostinger; measured site (914 MB uploads, 43 products, 17 orders, 8 users; PHP 8.3, WP 7.1.4) and confirmed Hostinger's upload route for offline sites.

## Not done
No migration package built, nothing uploaded, no DNS changed.

## Next
User decides whether to build the Hostinger package (site archive plus .sql with a plain wp-config). Finish tsk-gw91zc27 legacy redirects before pointing miroir.id at Hostinger.

## Blocked
none

## Notes for next agent
miroir.id DNS is on Cloudflare; Hostinger does not migrate DNS, email or cron. Check whether old miroir.id orders and customers must be kept, and whether miroir-demo should stay active.
