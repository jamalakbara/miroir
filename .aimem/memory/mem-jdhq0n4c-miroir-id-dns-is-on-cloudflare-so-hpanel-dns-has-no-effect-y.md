---
aimem: 1
id: mem-jdhq0n4c
type: memory
title: miroir.id DNS is on Cloudflare, so hPanel DNS has no effect yet
summary: Registrar shows in hPanel, but nameservers are Cloudflare; A records point to a suspended WordPress.com site. No MX records found on 2026-11-06.
status: superseded
trust: verified
roles: [all]
tags: [hosting, dns, hostinger, cloudflare]
author: human:akbar
created: 2026-11-06T08:25:08Z
updated: 2026-11-06T09:01:00Z
verified: 2026-11-06
kind: gotcha
superseded_by: mem-285bw29b
---

Checked 2026-11-06. NS: kia and nikon.ns.cloudflare.com. A: 192.0.78.187 and 192.0.78.241 (WordPress.com), returning 503 Account Suspended. Public lookup found no MX or DMARC, one google-site-verification TXT. Other subdomain records cannot be listed without Cloudflare access. Old site sent HSTS for one year, so valid HTTPS is needed on Hostinger for returning browsers.
