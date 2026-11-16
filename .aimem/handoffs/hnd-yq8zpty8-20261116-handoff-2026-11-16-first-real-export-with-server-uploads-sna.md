---
aimem: 1
id: hnd-yq8zpty8
type: handoff
title: "Handoff 2026-11-16: First real export with server uploads: snapshot 20261116T141050Z, 1638 files / 1.2 GB rsynced to ~/…"
summary: "Done: First real export with server uploads: snapshot 20261116T141050Z, 1638 files / 1.2 GB rsynced to ~/miroir-sync/uploads; server list matches Mac exactly (size+path). snapshot.env has UPLOADS_VIA…"
status: final
trust: agent
roles: [all]
author: agent:claude-code:opus-5.5
created: 2026-11-16T14:32:09Z
updated: 2026-11-16T14:32:09Z
branch: development
---

## Done
First real export with server uploads: snapshot 20261116T141050Z, 1638 files / 1.2 GB rsynced to ~/miroir-sync/uploads; server list matches Mac exactly (size+path). snapshot.env has UPLOADS_VIA=server.

## Not done
snapshot.sh, .env.example, README not committed/pushed. Other machine not set up (key, .env).

## Next
Commit and push, then on the other machine: add SSH key in hPanel, MIROIR_UPLOADS_* in .env, git pull, import. Old uploads.tar in Drive can be deleted after that import works.

## Blocked
none

## Notes for next agent
none
