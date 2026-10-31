---
aimem: 1
id: mem-kmk8k6fm
type: memory
title: "Pixel diff of theme vs miroir.pen: tools/visual-diff"
summary: Export pen frames at 1x to tools/visual-diff/out/design, node run.js <spec.json> captures site in Edge and reports % differing px; ~0.2-1.5% is text AA
status: active
trust: agent
roles: [all]
tags: [theme, testing, design]
paths: [tools/visual-diff/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T13:05:59Z
updated: 2026-10-31T13:05:59Z
kind: howto
---

See tools/visual-diff/README.md. Uses installed Microsoft Edge via playwright-core (channel msedge, --disable-lcd-text). Residual differences after a correct build are anti-aliasing and <=1px x-offsets: Pencil ceils text box widths, so flex rows of text can sit 0.5-1px apart from Chromium. Box sizes (screenshot dimensions) should match exactly; if they don't, it is a real layout bug.
