---
aimem: 1
id: mem-rmk3b6b3
type: memory
title: SSH to Hostinger from this Mac uses ~/.ssh/miroir_hostinger (key name miroir-deploy-mac in hPanel)
summary: Local key added 2026-11-11 in hPanel SSH Access; CI uses separate miroir-deploy. Host, port, user are in hPanel, not in git.
status: active
trust: agent
roles: [all]
tags: [ssh, hostinger, deploy]
author: agent:claude-code:sonnet-5.5
created: 2026-11-11T03:55:36Z
updated: 2026-11-11T03:55:36Z
---

Private key ~/.ssh/miroir_hostinger (no passphrase) on the user's Mac; public half registered in hPanel as miroir-deploy-mac, separate from CI key miroir-deploy (GitHub secret, unreadable). Host/port/user: hPanel > Advanced > SSH Access (not recorded here, repo is public). Live site is domains/miroir.id/public_html, staging is the staging folder inside it. Use ssh -i ~/.ssh/miroir_hostinger -p <port> user@host. wp prints WooCommerce 'routes' warnings; filter them. DB backups: mysqldump into ~/backups on the server (wp db export fails, proc_open disabled). After a deploy that changes DB-stored copy, run wp miroir theme refresh-copy on each site. See [[dec-kk30fqw6]].
