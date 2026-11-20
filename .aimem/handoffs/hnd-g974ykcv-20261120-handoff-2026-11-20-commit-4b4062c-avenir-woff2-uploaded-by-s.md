---
aimem: 1
id: hnd-g974ykcv
type: handoff
title: "Handoff 2026-11-20: Commit 4b4062c: Avenir WOFF2 (uploaded by scp to staging and live font dirs; TTF fallback kept; git…"
summary: "Done: Commit 4b4062c: Avenir WOFF2 (uploaded by scp to staging and live font dirs; TTF fallback kept; gitignore + rsync exclude now Avenir-*), preload Avenir-Roman instead of unused Inter Tight, main…"
status: final
trust: agent
roles: [all]
author: agent:claude-code
created: 2026-11-20T09:14:50Z
updated: 2026-11-20T09:14:50Z
branch: development
---

## Done
Commit 4b4062c: Avenir WOFF2 (uploaded by scp to staging and live font dirs; TTF fallback kept; gitignore + rsync exclude now Avenir-*), preload Avenir-Roman instead of unused Inter Tight, maintenance.css enqueued only via miroir_maintenance_screen action (maintenance + staging-access), MailPoet pages out of sitemap. Staging deployed and verified in Chrome. Merged to main; production run 44709006285 waiting approval. Skipped LiteSpeed minify: guest CSS/JS is 95KB gz total, ~8-10KB possible saving, not worth risk. eicons only loads for logged-in admins.

## Not done
Production deploy not approved yet; OpenSEO audit not run (waiting for live deploy).

## Next
After approval: verify live preloads/fonts/sitemap, then OpenSEO run_site_audit project 1181f14c-a7b9-43b2-a033-4a7ef59d3424.

## Blocked
Production approval (user).

## Notes for next agent
none
