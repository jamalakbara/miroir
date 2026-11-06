---
aimem: 1
id: mem-v8ktn2s9
type: memory
title: Hostinger CDN serves an old certificate for www.miroir.id
summary: SSL reinstall on 2026-11-06 issued a certificate covering both names, but CDN still serves the earlier miroir.id-only certificate for www. CDN cache flush did not help.
status: active
trust: verified
roles: [all]
tags: [hosting, ssl, hostinger, cdn]
author: human:akbar
created: 2026-11-06T12:13:07Z
updated: 2026-11-06T12:13:07Z
verified: 2026-11-06
supersedes: mem-64k53k2r
kind: gotcha
---

miroir.id serves the new certificate. Requests with www SNI get the old one on every checked edge, so https://www.miroir.id fails on name mismatch. Must be fixed before launch. Remaining options: wait for CDN refresh, Hostinger support, or turn CDN off and on. CDN Development mode is also on and should be reviewed at launch.
