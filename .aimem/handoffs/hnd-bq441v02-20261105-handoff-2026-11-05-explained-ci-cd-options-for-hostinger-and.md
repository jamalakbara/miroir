---
aimem: 1
id: hnd-bq441v02
type: handoff
title: "Handoff 2026-11-05: Explained CI/CD options for Hostinger and recommended GitHub Actions over SSH, syncing only miroir…"
summary: "Done: Explained CI/CD options for Hostinger and recommended GitHub Actions over SSH, syncing only miroir theme and plugins. · Next: After the first migration, confirm the plan includes SSH and that r…"
status: final
trust: agent
roles: [all]
author: agent:claude:claude-opus-5-5
created: 2026-11-05T17:29:26Z
updated: 2026-11-05T17:29:26Z
branch: main
---

## Done
Explained CI/CD options for Hostinger and recommended GitHub Actions over SSH, syncing only miroir theme and plugins.

## Not done
No workflow added, no secrets set, SSH and rsync not tested on the Hostinger plan.

## Next
After the first migration, confirm the plan includes SSH and that rsync exists, then add .github/workflows/deploy.yml if the user approves.

## Blocked
none

## Notes for next agent
hPanel Git deploy has no documented way to pick a repo subfolder, so it could publish docs/ and .aimem/. rsync --delete must exclude gitignored Avenir fonts. Production DB and uploads stay out of deploys, and snapshot import must never target production.
