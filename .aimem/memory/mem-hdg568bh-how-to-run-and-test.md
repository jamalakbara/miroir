---
aimem: 1
id: mem-hdg568bh
type: memory
title: How to run and test
summary: Docker Compose stack on :8080; WP-CLI via docker compose run --rm wpcli; config in .env
status: active
trust: agent
roles: [all]
tags: [pinned]
paths: [docker-compose.yml, .env.example]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-30T20:25:18Z
updated: 2026-10-30T20:25:18Z
kind: howto
---

Requires Docker Desktop (no local PHP/MySQL). Config comes from .env (copy .env.example; .env is git-ignored and holds the admin + DB passwords).

Start:   docker compose up -d
Site:    http://localhost:8080   Admin: http://localhost:8080/wp-admin (user from WP_ADMIN_USER)
WP-CLI:  docker compose run --rm wpcli <command>   e.g. `plugin list`, `option get siteurl` (entrypoint is already `wp`)
Logs:    docker compose logs -f wordpress ; PHP debug log at wp-content/debug.log (WP_DEBUG_LOG on, display off)
Stop:    docker compose down   (add -v to wipe DB and WP core volumes: destroys all site data)

Fresh install on a wiped stack: `docker compose up -d`, then `wpcli core install` with the .env values, then `wpcli plugin install woocommerce --activate`, then set permalink_structure to /%postname%/ and `wpcli rewrite flush --hard`.

No automated tests yet.
