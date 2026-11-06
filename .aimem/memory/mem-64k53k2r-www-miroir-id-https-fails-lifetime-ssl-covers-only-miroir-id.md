---
aimem: 1
id: mem-64k53k2r
type: memory
title: "www.miroir.id HTTPS fails: Lifetime SSL covers only miroir.id"
summary: Certificate has one name, miroir.id. http://www redirects to https://www, which then shows a name mismatch. hPanel offers only Uninstall and Unforce HTTPS.
status: superseded
trust: verified
roles: [all]
tags: [hosting, ssl, hostinger]
author: human:akbar
created: 2026-11-06T11:50:22Z
updated: 2026-11-06T12:13:07Z
verified: 2026-11-06
kind: gotcha
superseded_by: mem-v8ktn2s9
---

Checked 2026-11-06 on four CDN edges. Certificate was issued during the nameserver switch. Must be fixed before launch because old links and browsers may use www.
