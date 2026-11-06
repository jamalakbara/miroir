---
aimem: 1
id: mem-1trwj8w9
type: memory
title: Moving a Pen component child can drop instance fill overrides
summary: Moving Search into Utility Links left homepage Header instances with ink search text; re-apply overrides and check with resolveInstances.
status: active
trust: verified
roles: [all]
tags: [pen, navbar]
paths: [docs/design/miroir.pen]
author: human:akbar
created: 2026-11-05T23:18:29Z
updated: 2026-11-05T23:18:29Z
verified: 2026-11-05
kind: gotcha
---

Seen 2026-11-06. Move('PNT2R','jT1yX',1) in Header dk4Eq kept the node id, but homepage instances q3WywO and PzbGp resolved search to ink instead of white. Fixed with Update(instance + '/PNT2R', {fill}). After moving a component child, read instances with Get(id, {resolveInstances:true}) and compare the moved node to its siblings.
