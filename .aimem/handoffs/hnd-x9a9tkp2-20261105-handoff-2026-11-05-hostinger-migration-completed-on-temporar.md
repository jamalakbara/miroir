---
aimem: 1
id: hnd-x9a9tkp2
type: handoff
title: "Handoff 2026-11-05: Hostinger migration completed on temporary domain. Enabled SSH with a deploy key, secured backups…"
summary: "Done: Hostinger migration completed on temporary domain. Enabled SSH with a deploy key, secured migration backups, backed up DB, replaced 51 :8080 URLs, shuffled salts, flushed ca…"
status: final
trust: agent
roles: [all]
author: agent:claude:claude-opus-5-5
created: 2026-11-05T21:50:40Z
updated: 2026-11-05T21:50:40Z
branch: main
---

## Done
Hostinger migration completed on temporary domain. Enabled SSH with a deploy key, secured migration backups, backed up DB, replaced 51 :8080 URLs, shuffled salts, flushed caches. Browser checks passed for product, add to bag, checkout page and USD currency.

## Not done
Order submit, wp-admin, mobile, and redirects not tested. No DNS change, workflow not added.

## Next
User finishes post-migration security follow-up. Set real WhatsApp number and admin email. Decide on Jetpack, MailPoet and Google Listings. Finish tsk-gw91zc27, then cut over miroir.id.

## Blocked
Security follow-up must be done by the user.

## Notes for next agent
SSH details are kept outside the repo. WhatsApp is still 6281200000000. wp db export fails because proc_open is disabled; use mysqldump. Server backups are in ~/backups.
