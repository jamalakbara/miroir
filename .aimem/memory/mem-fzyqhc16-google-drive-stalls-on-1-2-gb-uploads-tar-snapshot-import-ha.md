---
aimem: 1
id: mem-fzyqhc16
type: memory
title: Google Drive stalls on 1.2 GB uploads.tar; snapshot import has --uploads-from-server
summary: Drive upload of uploads.tar times out (~70s, curl 42) and restarts; import --uploads-from-server pulls from Hostinger via MIROIR_UPLOADS_REMOTE instead. Server uploads can lag the Mac.
status: active
trust: agent
roles: [all]
tags: [snapshot, uploads, google-drive]
author: agent:claude-code:sonnet-5.5
created: 2026-11-16T13:41:33Z
updated: 2026-11-16T13:41:33Z
---

2026-11-16: Drive for desktop kept restarting the uploads.tar upload (drive_fs.txt: TIMEOUT_EXCEEDED 'Callback aborted' about every 70s, 'Retrying operation ... 0 cloud attempts'). File was not changing. Added tools/snapshot.sh import --uploads-from-server: skips the uploads.tar checksum and fetches wp-content/uploads over SSH (rsync if installed, else ssh+tar) from MIROIR_UPLOADS_REMOTE in .env (optional MIROIR_UPLOADS_SSH_PORT, MIROIR_UPLOADS_SSH_KEY). Gotcha: deploys never push uploads, so the server copy is only as new as the 2026-11-05 migration plus manual uploads; import warns when the file count differs from UPLOADS_FILES in snapshot.env. Host, port, user are not in git (public repo); see mem-rmk3b6b3.
