---
aimem: 1
id: mem-192pc4fs
type: memory
title: "Elementor under WP-CLI: Document::save needs a current user; settings must be arrays"
summary: documents->get()->save() silently saves nothing without a user who can edit; element settings as stdClass cause a TypeError
status: active
trust: agent
roles: [all]
tags: [elementor, cli, gotcha]
paths: [wp-content/themes/miroir-theme/inc/elementor/cli.php]
author: agent:claude-code:claude-opus-5-5
created: 2026-11-01T10:13:32Z
updated: 2026-11-01T10:13:32Z
kind: gotcha
---

wp miroir elementor setup sets the first administrator as current user (as_admin()). Element 'settings' must be PHP arrays (sanitize_settings(array)). New vendor plugins cannot be installed by wpcli uid 33 into the root-owned wp_plugins volume: install as root and chown 33 (README).
