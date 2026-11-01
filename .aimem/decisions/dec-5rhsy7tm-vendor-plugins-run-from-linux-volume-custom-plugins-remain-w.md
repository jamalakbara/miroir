---
aimem: 1
id: dec-5rhsy7tm
type: decision
title: Vendor plugins run from Linux volume; custom plugins remain Windows mounts
summary: Use wp_plugins shared by WordPress and WP-CLI; four miroir plugin directories override with bind mounts. Preserve Windows originals and DB/uploads.
status: proposed
trust: verified
roles: [all]
tags: [docker performance local]
author: human:akbar
created: 2026-10-31T16:43:56Z
updated: 2026-10-31T16:43:56Z
verified: 2026-10-31
links: [dec-c17cg0jt]
---

Authorized by user to fix measured ~10-second local page latency. Keep existing wp_core/db_data and parent wp-content bind (themes/uploads), overlay plugins with new wp_plugins named volume, then overlay four miroir custom plugins as individual Windows binds on both wordpress and wpcli services. This targets vendor PHP file overhead without relocating repo or changing custom development workflow. Existing vendor files copied, not removed. tools/Initialize-PluginVolume.ps1 archives vendor directories locally then imports missing destinations into volume via staging; preserves container-updated versions on rerun. Install vendor updates using wp-admin/WP-CLI; new custom plugin folders require mount entries in both services. README documents workflow. Bulk archive avoids slow per-file cp through Windows mount.
