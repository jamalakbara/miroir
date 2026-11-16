---
aimem: 1
id: dec-7vemv429
type: decision
title: Uploads move between machines through a private server folder by rsync, not uploads.tar in Drive
summary: When MIROIR_UPLOADS_REMOTE is set, snapshot export mirrors wp-content/uploads (rsync --delete) to a private server folder outside public_html; import pulls it. No uploads.tar.
status: proposed
trust: agent
roles: [all]
tags: [sync, uploads, snapshot]
paths: [tools/snapshot.sh, .env.example]
author: agent:claude-code:opus-5.5
created: 2026-11-16T13:57:15Z
updated: 2026-11-16T13:57:15Z
links: [dec-jv207tkf, mem-fzyqhc16]
---

Chosen 2026-11-16 by user over per-month tar shards and Syncthing: rsync sends only changed files like the deploy workflow, and every machine already reaches the server. snapshot.env gets UPLOADS_VIA=server; import then pulls automatically and checks SSH before replacing the database. Export refuses paths under public_html or wp-content/uploads because it deletes on the server. Without rsync (Windows Git Bash) ssh+tar sends only files whose size differs. Pull never deletes locally. Unset the variable to fall back to uploads.tar.
