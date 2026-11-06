---
aimem: 1
id: mem-p8xkca1e
type: memory
title: Staging differs from live on purpose; refresh needs guards reapplied
summary: "Staging: placeholder WhatsApp number, cron off, mail blocked by host-checked mu-plugin, Jetpack, MailPoet, Google Listings and TikTok deactivated, no preview token."
status: active
trust: verified
roles: [all]
author: human:akbar
created: 2026-11-06T12:44:52Z
updated: 2026-11-06T12:44:52Z
verified: 2026-11-06
kind: gotcha
---

Staging lives in a folder inside the live web root, so miroir.id/staging also reaches it; the Basic Auth lock in its .htaccess covers both addresses. WP-CLI commands that need proc_open, such as wp db query and wp search-replace --dry-run reports, fail on this host; use wp eval. Recreating or refreshing staging from live overwrites these settings, so lock it first and reapply them. The guard mu-plugin stored blog_public filter returns 0 while the stored value stays 1; the noindex header and Basic Auth do the real work.
