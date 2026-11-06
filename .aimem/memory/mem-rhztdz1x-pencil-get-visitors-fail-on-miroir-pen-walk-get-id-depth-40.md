---
aimem: 1
id: mem-rhztdz1x
type: memory
title: Pencil Get visitors fail on miroir.pen; walk Get(id,{depth:40}) instead
summary: Get with a visitor throws 'cannot read property of undefined' on hi-fi and document reads; recurse plain Get(id,{depth:40}) in JS.
status: active
trust: verified
roles: [all]
tags: [pencil, design]
author: human:akbar
created: 2026-11-05T22:24:44Z
updated: 2026-11-05T22:24:44Z
verified: 2026-11-05
kind: gotcha
---

Seen 2026-11-06. Get('vmoZB', visit) and Get(visit) both threw in execute; Get(id,{depth:40}) returned the tree. Walk children with a small recursive function. Node IDs in Get(document) also fail by design.
