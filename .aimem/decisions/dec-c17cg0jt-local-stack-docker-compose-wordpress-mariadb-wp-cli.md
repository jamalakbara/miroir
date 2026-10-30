---
aimem: 1
id: dec-c17cg0jt
type: decision
title: "Local stack: Docker Compose WordPress + MariaDB + WP-CLI"
summary: Official wordpress php8.3 + MariaDB 11.4 images; core in volume, only wp-content bind-mounted
status: accepted
trust: agent
roles: [all]
tags: [stack devops]
paths: [docker-compose.yml]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-30T20:25:48Z
updated: 2026-10-30T20:25:48Z
links: [mem-hdg568bh]
---

Decision: develop the site locally with Docker Compose: official `wordpress:php8.3-apache` image, MariaDB 11.4, and the `wordpress:cli-php8.3` image as a one-off WP-CLI runner (compose profile "tools").

Why:
- The dev machine (Windows 11) has no PHP, MySQL or Composer; Docker Desktop was already installed.
- Same images can back staging/production later, so PHP/DB versions stay pinned and reproducible.
- WP-CLI in a container makes setup steps scriptable instead of wp-admin clicks.

Layout choice: WordPress core lives in the `wp_core` named volume; only `./wp-content` is bind-mounted. Bind-mounting all of core from a Windows drive is slow under WSL2, and core is not our code.

Rejected:
- LocalWP / XAMPP: GUI-driven, not reproducible from the repo.
- Installing PHP + MariaDB natively via winget: pollutes the host, versions drift from production.
- Bedrock/Composer-managed WordPress: more structure than a single-shop rebuild needs right now; revisit if plugin versions need locking.

Versions at install (2026-10-31): WordPress 7.1.4, WooCommerce 11.2.1.
