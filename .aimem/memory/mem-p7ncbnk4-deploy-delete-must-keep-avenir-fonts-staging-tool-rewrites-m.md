---
aimem: 1
id: mem-p7ncbnk4
type: memory
title: Deploy --delete must keep Avenir fonts; staging tool rewrites miroir.id everywhere
summary: Avenir ttf files are gitignored but needed on servers, so rsync excludes them. Hostinger Staging rewrites miroir.id in files and DB, including the Instagram handle.
status: active
trust: verified
roles: [all]
paths: [.github/workflows/deploy.yml]
author: human:akbar
created: 2026-11-06T13:30:41Z
updated: 2026-11-06T13:30:41Z
verified: 2026-11-06
kind: gotcha
---

If staging is recreated, redeploy development and run wp search-replace for instagram.com/staging.miroir.id on staging. Placeholder demo copy on staging may still read staging.miroir.id; harmless.
