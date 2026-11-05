---
aimem: 1
id: mem-vddkvtbs
type: memory
title: Hostinger package must be built from the container, not the Windows wp-content folder
summary: Vendor plugins and WP core live in Docker volumes, and the image wp-config reads env vars, so a Windows zip is incomplete
status: active
trust: agent
roles: [all]
tags: [hosting, migration, hostinger]
paths: [docker-compose.yml]
author: agent:claude:claude-opus-5-5
created: 2026-11-05T17:25:25Z
updated: 2026-11-05T17:25:25Z
kind: gotcha
---

Vendor plugins live in the wp_plugins volume and WordPress core in wp_core, so wp-content on Windows does not hold the running site. Build any host migration archive from /var/www/html in the wordpress container. The Docker image wp-config reads DB settings and WORDPRESS_CONFIG_EXTRA (WP_ENVIRONMENT_TYPE local, WP_DEBUG) from env vars, so it needs a plain replacement for a normal host. Exclude macOS ._ files. siteurl is http://localhost:8080 and needs a serialized-safe search-replace. Hostinger admin-login migration cannot reach localhost; use Upload your website files (site archive plus .sql).
