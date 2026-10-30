---
aimem: 1
id: mem-z1ajmc8c
type: memory
title: Conventions
summary: Own code in miroir-* plugins/themes only; repeatable setup via WP-CLI; no secrets in git or records
status: active
trust: agent
roles: [all]
tags: [pinned]
paths: [wp-content/**, .gitignore]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-30T20:25:19Z
updated: 2026-10-30T20:25:19Z
kind: preference
---

- Own code lives in wp-content/plugins/miroir-* and wp-content/themes/miroir-*; .gitignore tracks only those. Third-party plugins/themes are installed with WP-CLI, never committed.
- Manage WordPress through WP-CLI (docker compose run --rm wpcli ...) rather than clicking through wp-admin when the change should be repeatable; note repeatable setup steps in the "How to run and test" record.
- Never commit .env or put credentials in records.
- Keep permalinks at /%postname%/ so product/category paths match the legacy site.
