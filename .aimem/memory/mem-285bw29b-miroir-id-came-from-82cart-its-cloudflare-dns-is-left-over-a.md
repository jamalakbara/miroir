---
aimem: 1
id: mem-285bw29b
type: memory
title: miroir.id came from 82cart; its Cloudflare DNS is left over and hPanel DNS has no effect yet
summary: User transferred domain from 82cart to Hostinger. Nameservers still Cloudflare, A records hit an expected suspended old site. Move nameservers to Hostinger. No MX found 2026-11-06.
status: active
trust: verified
roles: [all]
tags: [hosting, dns, hostinger, cloudflare]
author: human:akbar
created: 2026-11-06T09:01:00Z
updated: 2026-11-06T09:01:00Z
verified: 2026-11-06
supersedes: mem-jdhq0n4c
kind: fact
---

User said on 2026-11-06 that 82cart handled miroir.id before and the domain was transferred to their Hostinger. Suspended page on 192.0.78.187 and 192.0.78.241 is expected, not a fault. NS remain kia and nikon.ns.cloudflare.com, likely 82cart's zone (owner not confirmed). Public lookup found no MX or DMARC, one google-site-verification TXT. Old site sent HSTS for one year, so valid HTTPS is needed on Hostinger.
