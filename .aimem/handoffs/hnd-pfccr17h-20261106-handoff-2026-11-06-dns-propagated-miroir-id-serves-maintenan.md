---
aimem: 1
id: hnd-pfccr17h
type: handoff
title: "Handoff 2026-11-06: DNS propagated. miroir.id serves maintenance over HTTPS through Hostinger CDN; home and siteurl are…"
summary: "Done: DNS propagated. miroir.id serves maintenance over HTTPS through Hostinger CDN; home and siteurl are https. Preview tested over public HTTPS: home, shop, account 200, public 503, preview respons…"
status: final
trust: verified
roles: [all]
author: human:akbar
created: 2026-11-06T11:50:24Z
updated: 2026-11-06T11:50:24Z
verified: 2026-11-06
branch: fix/home-navbar-dialog-overlay
---

## Done
DNS propagated. miroir.id serves maintenance over HTTPS through Hostinger CDN; home and siteurl are https. Preview tested over public HTTPS: home, shop, account 200, public 503, preview responses no-store and noindex. Updated stale Jetpack firewall paths.

## Not done
www HTTPS still fails on certificate name. GSC Domain property not added. robots.txt rule not checkable during maintenance.

## Next
Fix www certificate by SSL reinstall or Hostinger support. User sends client preview link and sets up GSC. Before launch finish tsk-gw91zc27 and launch checks.

## Blocked
User to choose how to fix www certificate.

## Notes for next agent
Temp domain returns 404. Keep preview secret out of records.
