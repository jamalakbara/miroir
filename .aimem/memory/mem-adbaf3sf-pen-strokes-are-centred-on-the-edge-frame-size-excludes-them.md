---
aimem: 1
id: mem-adbaf3sf
type: memory
title: Pen strokes are centred on the edge; frame size excludes them
summary: Pencil export uses outline 1px + outline-offset -0.5px and content-box height = frame-0.5; theme uses outline -0.5px for boxes and .hair-* box-shadows for single edges
status: active
trust: agent
roles: [all]
tags: [theme, css, design]
paths: [wp-content/themes/miroir-theme/assets/css/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T14:05:14Z
updated: 2026-10-31T14:05:14Z
links: [mem-9f3n6czt]
kind: gotcha
---

A bordered pen frame keeps its natural size (text + padding); the 1px stroke straddles the edge. Theme: buttons/inputs/summary boxes use outline:1px + outline-offset:-0.5px; single edges use .hair-b/.hair-t/.hair-tb (inset 0.5px + outset 0.5px box-shadows). Chromium snaps the outline to whole pixels, so one edge lands 1px off the pen raster; geometry stays exact. Do not use border (adds 1-2px per box). Also: <details> with display:flex keeps a (hidden) content slot when closed, so flex gap must only apply to [open].
