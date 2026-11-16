---
aimem: 1
id: hnd-m3js6dw9
type: handoff
title: "Handoff 2026-11-16: Added import --uploads-from-server to tools/snapshot.sh (rsync, ssh+tar fallback, count-mismatch wa…"
summary: "Done: Added import --uploads-from-server to tools/snapshot.sh (rsync, ssh+tar fallback, count-mismatch warning) and .env.example vars. Tested fetch with a fake ssh in both modes; bash -n ok. Diagnose…"
status: final
trust: agent
roles: [all]
author: agent:claude-code:sonnet-5.5
created: 2026-11-16T13:41:33Z
updated: 2026-11-16T13:41:33Z
branch: development
---

## Done
Added import --uploads-from-server to tools/snapshot.sh (rsync, ssh+tar fallback, count-mismatch warning) and .env.example vars. Tested fetch with a fake ssh in both modes; bash -n ok. Diagnosed Drive stall of uploads.tar (timeouts every ~70s).

## Not done
Not committed. Not run against the real server or a real import; MIROIR_UPLOADS_REMOTE value (user, host, port, path) not known to repo. Server uploads may lag the Mac's 1638 files.

## Next
On Windows set MIROIR_UPLOADS_REMOTE in .env, run tools\snapshot.cmd import --uploads-from-server. If counts differ, copy the Mac's uploads another way (USB/LAN) or push Mac uploads to the server first.

## Blocked
none

## Notes for next agent
none
