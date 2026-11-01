---
aimem: 1
id: mem-m9w82r5g
type: memory
title: "Measured local slowdown: Windows wp-content mount"
summary: Homepage TTFB ~10s; bootstrap 1649 PHP files, 48 queries/0.020s. Windows plugin read+stat ~750x slower than core Linux volume in microbenchmark.
status: active
trust: verified
roles: [all]
tags: [performance docker local]
author: human:akbar
created: 2026-10-31T16:32:36Z
updated: 2026-10-31T16:32:36Z
verified: 2026-10-31
kind: fact
---

2026-10-31 diagnostic, no stack changes: HTTP homepage TTFB 10.508s then 9.973s; theme CSS TTFB 0.010s. CLI bootstrap (uncached CLI, not equivalent to Apache) 41.251s, 1649 included files, 48 DB queries totaling 0.0198s; DISABLE_WP_CRON set only in diagnostic process. Repeated file_exists + file_get_contents with clearstatcache x300: Linux-volume wp-includes/version.php 0.002s vs Windows-mounted WooCommerce main file 1.508s (different file sizes; microbenchmark supports I/O suspicion, not exact attribution of whole request). OPcache enabled for web, 128MB, revalidate_freq=2. Whole wp-content is Windows bind-mounted. Strong likely bottleneck is Windows/Linux file access, amplified by WooCommerce/WCML includes. Consider Linux storage for third-party plugins with selective mounts for miroir custom code, or moving repo into WSL filesystem. No isolated full-request A/B or per-plugin profiling performed.
