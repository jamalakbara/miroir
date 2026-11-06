---
aimem: 1
id: hnd-vvb8j309
type: handoff
title: "Handoff 2026-11-06: Built client preview link in maintenance.php and cli.php, deployed by scp with DB and file backup i…"
summary: "Done: Built client preview link in maintenance.php and cli.php, deployed by scp with DB and file backup in ~/backups/domain-cutover-20261106. Maintenance on. Connected miroir.id in hPanel, which move…"
status: final
trust: verified
roles: [all]
author: human:akbar
created: 2026-11-06T10:47:48Z
updated: 2026-11-06T10:47:48Z
verified: 2026-11-06
branch: fix/home-navbar-dialog-overlay
---

## Done
Built client preview link in maintenance.php and cli.php, deployed by scp with DB and file backup in ~/backups/domain-cutover-20261106. Maintenance on. Connected miroir.id in hPanel, which moved the site and rewrote URLs. Nameservers changed to Hostinger at the registry. Re-added Google TXT.

## Not done
DNS still propagating. SSL not installed. WordPress home is http until SSL is done. Preview code not committed. miroir.id over public DNS not verified.

## Next
When miroir.id resolves to Hostinger: install SSL in hPanel, set home and siteurl to https, flush caches, verify maintenance, preview, www, robots.txt, then send user the preview link. Add GSC Domain property and review owners.

## Blocked
Waiting for DNS propagation.

## Notes for next agent
Temp domain now returns 404. Raw queries to Hostinger nameservers from this machine returned REFUSED even for dns-parking.com, so that test is unreliable; use public resolvers.
