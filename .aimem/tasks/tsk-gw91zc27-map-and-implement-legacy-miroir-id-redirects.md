---
aimem: 1
id: tsk-gw91zc27
type: task
title: Map and implement legacy miroir.id redirects
summary: Fill new_url_or_action for 194 legacy URLs, implement 301s, verify with a scripted check
status: open
trust: agent
roles: [all]
tags: [migration seo]
paths: [docs/migration/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-30T20:25:57Z
updated: 2026-10-30T20:25:57Z
links: [mem-emqm9rkc]
priority: p1
---

Fill the new_url_or_action column of docs/migration/legacy-urls.csv for all 194 rows (currently empty), following the "Legacy URL redirect rules" memory. Then implement the redirects on the new site (e.g. Redirection plugin import or a miroir-* mu-plugin) and verify each legacy path returns 200 or a single 301 hop to a 200.

Done when: every row has a target or "keep", redirects are live locally, and a scripted check over the CSV passes.
