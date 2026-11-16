---
aimem: 1
id: hnd-c3tqaz1s
type: handoff
title: "Handoff 2026-11-16: SSH login to Hostinger with ~/.ssh/miroir_hostinger verified (port 65002). Added MIROIR_UPLOADS_REM…"
summary: "Done: SSH login to Hostinger with ~/.ssh/miroir_hostinger verified (port 65002). Added MIROIR_UPLOADS_REMOTE=...:~/miroir-sync/uploads, port, key to the Mac .env. Real-server tests: rsync round-trip…"
status: final
trust: agent
roles: [all]
author: agent:claude-code:opus-5.5
created: 2026-11-16T14:08:39Z
updated: 2026-11-16T14:08:39Z
branch: development
---

## Done
SSH login to Hostinger with ~/.ssh/miroir_hostinger verified (port 65002). Added MIROIR_UPLOADS_REMOTE=...:~/miroir-sync/uploads, port, key to the Mac .env. Real-server tests: rsync round-trip and ssh+tar push/pull/delete pass. Fixed macOS ._ files in tar mode (COPYFILE_DISABLE=1). Test folder on server removed.

## Not done
Not committed. First real export (1.2 GB push) not run. Other machines' .env and SSH keys not set up.

## Next
Run tools/snapshot.sh export on the Mac, then on the other machine add its own key in hPanel, copy the MIROIR_UPLOADS_* lines into .env, git pull, tools/snapshot.cmd import. Commit snapshot.sh, .env.example, README.

## Blocked
none

## Notes for next agent
none
