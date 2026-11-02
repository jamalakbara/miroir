---
aimem: 1
id: dec-jv207tkf
type: decision
title: "Two-machine sync: plugins.txt + git hooks for vendor plugins, tools/snapshot.sh for DB/uploads/fonts"
summary: Git carries code; plugins.txt (auto-frozen pre-commit, applied post-merge) syncs vendor plugins; snapshot export/import via private cloud folder syncs content
status: proposed
trust: agent
roles: [all]
tags: [workflow, sync, plugins]
paths: [tools/plugins.sh, tools/snapshot.sh, tools/setup.sh, tools/lib/*, .githooks/*, plugins.txt]
author: agent:claude-code:claude-opus-5-5
created: 2026-11-02T18:11:09Z
updated: 2026-11-02T18:11:09Z
links: [dec-5rhsy7tm]
---

User works on Windows and macOS; Elementor/other vendor plugins live in the wp_plugins Docker volume and content in db_data, so git pull on the Mac had neither.

Chosen: plugins.txt manifest (slug version status). Pre-commit hook freezes it in merge mode (never drops a listed plugin or lowers a version, so an out-of-date machine cannot undo the other's change); post-merge/post-rewrite apply only the lines that changed since ORIG_HEAD (install/update/activate, delete removed). Full sync warns on extras; --prune deletes them. Snapshot import uses --prune so plugins mirror the imported DB.
Content: tools/snapshot.sh export/import to MIROIR_SNAPSHOT_DIR (private Drive/iCloud folder): mariadb-dump from db container, uploads.tar (reused when unchanged), private.tar (gitignored Avenir fonts), plugins.txt, snapshot.env with sha256s (import refuses partially synced files). Export refuses to overwrite a snapshot this clone never imported (--force). Import backs up the local DB (.snapshots-backup, last 3), rewrites URLs and deletes WCML's wcml_currency_switcher_template_objects cache (serialized objects search-replace cannot rewrite).

Rejected: committing vendor plugins (thousands of files, update churn); Composer/wpackagist (rejected before as too much structure); DB dumps in git (customer data, unmergeable); Git LFS for 900 MB uploads (cost). Long term a staging server should become the content source of truth.

Verified 2026-11-03 on a throwaway compose project (fresh volumes, port 8081): import installed all 5 plugins (~15 min, mostly wordpress.org downloads), 18 products/pages/Elementor docs matched, homepage identical apart from Elementor CSS cache versions; hook add/remove of hello-dolly worked.
