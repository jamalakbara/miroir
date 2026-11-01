---
aimem: 1
id: dec-a36q4j0m
type: decision
title: Navigation uses a full-screen drawer and shared desktop primary links
summary: Replace in-page mobile menu with a native full-screen dialog, search-style X and desktop primary menu source.
status: superseded
trust: verified
roles: [all]
author: human:akbar
created: 2026-11-01T17:00:14Z
updated: 2026-11-01T17:13:49Z
verified: 2026-11-01
superseded_by: dec-z2c7f2yt
---

User requested full-screen drawer, the same X as search, and matching desktop navigation. panels.php now calls miroir_menu(primary) plus search/currency/account/bag controls; old panel-nav remains registered for compatibility but no longer drives the drawer. Native dialog covers header/footer/floating chat, scrolls internally, traps focus, holds background position and returns focus on close. Search/currency replace the drawer while preserving its original trigger. Supersedes menu behavior only in dec-4fden8b5 and dec-bdpbse0v. Updated existing screen 31 desktop/tablet/mobile in Pen.dev through MCP and documented interaction context.
