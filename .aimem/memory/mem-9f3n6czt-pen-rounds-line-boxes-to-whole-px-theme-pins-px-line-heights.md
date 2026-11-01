---
aimem: 1
id: mem-9f3n6czt
type: memory
title: Pen rounds line boxes to whole px; theme pins px line-heights
summary: Pencil text 'normal' = round(size*1.21) Inter Tight / round(size*1.3) DM Mono; 1.5/1.6 rounded per line. Use --lh-* tokens, never line-height normal
status: active
trust: agent
roles: [all]
tags: [theme, css, design, typography]
paths: [wp-content/themes/miroir-theme/assets/css/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T13:05:56Z
updated: 2026-10-31T13:05:56Z
links: [dec-986mmhej]
kind: gotcha
---

Measured 2026-10-31 across all 132 lofi layouts. Inter Tight normal: 12->15, 13->16, 16->19 (Chromium gives 20), 19->23, 28->34, 32->39. DM Mono normal: 11->14, 12->16, 13->17, 16->21, 22->29. Multipliers are rounded per line: 13px x 1.5 = 20px lines (3 lines = 60, not 58.5), 13 x 1.6 = 21, 13 x 1.55 = 20, 12 x 1.6 = 19, 28 x 1.6 = 45, 32 x 1.6 = 51. tokens.css holds --lh-sans-N / --lh-mono-N; every font-size rule in the theme must set one of them or an explicit px value, else blocks drift 1px per line. Also: pen strokes are inside the frame, so bordered boxes take 1px off padding (buttons 14/43 + 1px border = pen 15/44).
