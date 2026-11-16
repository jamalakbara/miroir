---
aimem: 1
id: hnd-eqm5amkn
type: handoff
title: "Handoff 2026-11-16: tools/snapshot.sh export pushes uploads to MIROIR_UPLOADS_REMOTE (rsync --delete; ssh+tar size-diff…"
summary: "Done: tools/snapshot.sh export pushes uploads to MIROIR_UPLOADS_REMOTE (rsync --delete; ssh+tar size-diff fallback; public_html guard) and records UPLOADS_VIA=server; import pulls automatically, chec…"
status: final
trust: agent
roles: [all]
author: agent:claude-code:opus-5.5
created: 2026-11-16T13:57:16Z
updated: 2026-11-16T13:57:16Z
branch: development
---

## Done
tools/snapshot.sh export pushes uploads to MIROIR_UPLOADS_REMOTE (rsync --delete; ssh+tar size-diff fallback; public_html guard) and records UPLOADS_VIA=server; import pulls automatically, checks SSH before DB replace; status shows uploads location. .env.example and README updated. Tested push/pull in both modes with fake ssh; bash -n ok.

## Not done
Not committed. Not run against the real Hostinger server or a full export/import (would overwrite the shared Drive snapshot). MIROIR_UPLOADS_REMOTE not set in any .env yet. Old uploads.tar stays in the Drive folder.

## Next
User sets MIROIR_UPLOADS_REMOTE=user@host:~/miroir-sync/uploads plus SSH port/key in .env on each machine, runs export on the Mac (first push ~1.2 GB), then import on the other machine. Commit after.

## Blocked
none

## Notes for next agent
none
