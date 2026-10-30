---
aimem: 1
id: dec-bw7kwt92
type: decision
title: miroir design system lives in miroir.pen (tokens + components + board)
summary: "Lofi v0.1 design system: type/spacing/colour tokens, 20 reusable components, 'Design System' board above the components"
status: proposed
trust: agent
roles: [all]
tags: [design, design-system]
paths: [docs/design/miroir.pen]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-30T22:45:01Z
updated: 2026-10-30T22:45:01Z
links: [dec-bf8ede4p, dec-986mmhej]
---

Built 2026-10-31 in docs/design/miroir.pen.

Tokens (pen variables): colours bg, ink, ink-2, ink-3, line, ph, ph-ink, surface, on-ink, error, success (the last two only for hi-fi states); fonts font-sans Inter Tight, font-mono DM Mono; type text-xs 11, text-sm 12, text-base 13, text-md 16, text-lg 19, text-xl 28, text-2xl 32, text-logo 26; spacing space-1..9 = 4, 8, 12, 16, 24, 32, 48, 64, 96; gutter 56; radius-none, radius-full; stroke-hair 1. All page font sizes are bound to the text-* tokens.

Components: Header, Footer, Product Card, Image Placeholder, Field, Button/Primary, Button/Outline, Button/WhatsApp, Button/Link, Checkbox/On+Off, Radio/On+Off, Qty Stepper, Badge, Section Link, Totals Row, Notice, WhatsApp Float. Pages use them for section links, qty, PLP colour filters, remember me and the bag note.

'Design System' board (frame at x=0, y=-5700) documents colour, type scale, spacing and layout rules, icons and lines, every component with its usage, and do/don't rules.

Gotcha: Get(...) layout 'problems' reports false clipping on frames whose gap/padding use variables. Pass {resolveVariables:true} to get correct bounds.
