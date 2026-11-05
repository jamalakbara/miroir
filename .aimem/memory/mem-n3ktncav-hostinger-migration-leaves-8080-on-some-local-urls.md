---
aimem: 1
id: mem-n3ktncav
type: memory
title: Hostinger migration leaves :8080 on some local URLs
summary: Hostinger rewrote localhost URLs to the temporary domain but kept :8080 on stored shop and account links, so those links time out
status: active
trust: agent
roles: [all]
tags: [hosting, migration, hostinger]
author: agent:claude:claude-opus-5-5
created: 2026-11-05T18:06:24Z
updated: 2026-11-05T18:06:24Z
links: [mem-vddkvtbs]
kind: gotcha
---

After backup migration to the temporary domain, homepage shop-2 and my-account links used the temporary domain with :8080. Port 8080 timed out. Replace both the domain:8080 form and any remaining http://localhost:8080 values after hPanel marks migration complete.
