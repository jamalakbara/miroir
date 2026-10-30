---
aimem: 1
id: mem-mz6c0wtf
type: memory
title: WP-CLI container usage
summary: wpcli entrypoint is already wp; runs as uid 33; tools profile so not started by up -d
status: active
trust: agent
roles: [all]
tags: [wp-cli docker]
paths: [docker-compose.yml]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-30T20:25:50Z
updated: 2026-10-30T20:25:50Z
links: [mem-hdg568bh]
kind: gotcha
---

WP-CLI gotchas in this setup:
- The wpcli service sets `entrypoint: ["wp"]`, so run `docker compose run --rm wpcli plugin list`, not `... wpcli wp plugin list` (that becomes `wp wp ...`). Without the entrypoint override the image only adds `wp` when the first argument starts with `-`, and `core install` fails with "exec: core: not found".
- It runs as uid 33 (www-data) so files it writes into wp-content match the web container. WP_CLI_CACHE_DIR is set to /tmp/wp-cli-cache because www-data has no writable home.
- The wpcli service is in the "tools" profile, so `docker compose up -d` does not start it; that is intended.
