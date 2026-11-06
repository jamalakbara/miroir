---
aimem: 1
id: dec-azeyt16t
type: decision
title: Client reviews on password-locked staging.miroir.id; miroir.id stays behind maintenance
summary: User chose staging before launch. Hostinger staging copy, Basic Auth, own database. Launch remains turning live maintenance off; never use Publish after launch.
status: superseded
trust: verified
roles: [all]
author: human:akbar
created: 2026-11-06T12:44:51Z
updated: 2026-11-06T13:20:48Z
verified: 2026-11-06
supersedes: dec-dezak5k6
superseded_by: dec-z7ftkp9d
---

User chose on 2026-11-06 to add staging now, over the earlier plan of waiting until after launch. Live miroir.id stays in maintenance and its secret preview link still works. Staging has maintenance off and is protected by Basic Auth, which fits there because staging has no public visitors. Hostinger Publish replaces live files and database, and staging holds a placeholder WhatsApp number, maintenance off and disabled plugins, so do not use Publish. Move code by the normal deploy; live database is source of truth.
