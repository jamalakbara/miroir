---
aimem: 1
id: mem-c1ebj1a5
type: memory
title: "Manual guide pipeline: content.html -> build.mjs -> slice.mjs -> miroir.pen"
summary: Client manual source is docs/manual; build+slice scripts regenerate A4 pages and block images used by miroir.pen manual frames
status: active
trust: agent
roles: [all]
paths: [docs/manual/**, docs/design/miroir-assets/manual-guide/**]
author: agent:claude-code:opus-5.5
created: 2026-11-16T23:07:40Z
updated: 2026-11-16T23:07:40Z
---

English client manual follows the jamalakbara manual-guide template. docs/manual/content.html holds blocks; build.mjs crops screenshots with Chrome (sips --cropOffset is ignored and crops centered) and paginates into manual-guide.html (61 A4 pages); slice.mjs renders each block at 2x into docs/design/miroir-assets/manual-guide/pages and writes manual-guide.layout.json. miroir.pen frames 'Manual guide / NN — ... / A4' sit at y=53200 and point at those images by file name, so reslicing updates them when pagination does not change; if it changes, the frames must be rebuilt. Screenshots come from the local site using WP-CLI generated auth cookies, with customer names and addresses scrubbed in the DOM. Fonts and stamp load from the sibling jamalakbara repo by absolute path.
