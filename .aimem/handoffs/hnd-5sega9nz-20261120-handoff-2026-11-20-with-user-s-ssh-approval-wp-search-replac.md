---
aimem: 1
id: hnd-5sega9nz
type: handoff
title: "Handoff 2026-11-20: With user's SSH approval: wp search-replace for temp domain on live found 0 rows; source was option…"
summary: "Done: With user's SSH approval: wp search-replace for temp domain on live found 0 rows; source was option wcml_currency_switcher_template_objects (serialized objects). Backed it up to ~/backups/wcml_…"
status: final
trust: agent
roles: [all]
author: agent:claude-code
created: 2026-11-20T07:54:19Z
updated: 2026-11-20T07:54:19Z
branch: development
---

## Done
With user's SSH approval: wp search-replace for temp domain on live found 0 rows; source was option wcml_currency_switcher_template_objects (serialized objects). Backed it up to ~/backups/wcml_currency_switcher_template_objects-20261120.txt, deleted it, flushed WP and LiteSpeed caches. Live HTML now has no temp-domain references; WCML CSS loads from //miroir.id.

## Not done
Maintenance still on. COD gateway still enabled (user undecided).

## Next
Maintenance off (user), then post-launch checks: www redirect, robots, sitemap, full legacy CSV check, CDN dev mode, Search Console, OpenSEO.

## Blocked
Maintenance off is user's call.

## Notes for next agent
none
