---
aimem: 1
id: mem-y7yavdgp
type: memory
title: MariaDB 11.4 dumps need the sandbox line removed before Hostinger import
summary: mariadb-dump starts with a /*M!999999 sandbox line that older MySQL or MariaDB imports can reject; strip line 1 for host migration
status: active
trust: agent
roles: [all]
tags: [hosting, migration, hostinger, database]
author: agent:claude:claude-opus-5-5
created: 2026-11-05T17:51:13Z
updated: 2026-11-05T17:51:13Z
links: [mem-vddkvtbs]
kind: gotcha
---

Local mariadb-dump 11.4 writes a sandbox comment as line 1. Strip it with sed before giving a .sql file to Hostinger. Tables use utf8mb4_unicode_520_ci, with no uca1400 collation in the dump. Tested archive is about 1.04 GB. wp config create in the local wpcli image has no --stdout, so build the package wp-config from wp-config-sample.php and run wp config shuffle-salts on the server. URL replacement should follow tools/snapshot.sh, including wp elementor replace_urls and deleting wcml_currency_switcher_template_objects.
