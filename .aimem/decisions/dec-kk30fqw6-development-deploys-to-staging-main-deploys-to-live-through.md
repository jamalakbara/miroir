---
aimem: 1
id: dec-kk30fqw6
type: decision
title: development deploys to staging, main deploys to live through GitHub Actions
summary: Workflow copies theme and four miroir plugins by rsync. Live needs reviewer approval. Database, uploads and vendor plugins are never deployed.
status: accepted
trust: verified
roles: [all]
paths: [.github/workflows/deploy.yml]
author: human:akbar
created: 2026-11-06T13:30:39Z
updated: 2026-11-06T13:30:39Z
verified: 2026-11-06
---

Chosen 2026-11-06 over Hostinger Git deploy so only our folders are copied. Server details and a dedicated deploy key live in GitHub secrets because the repo is public; DEPLOY_PATH is per environment. Workflow dispatch defaults to dry run. Database changes such as menus, Customizer values and pages still need per-site steps.
