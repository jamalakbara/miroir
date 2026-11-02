---
aimem: 1
id: mem-x30vreww
type: memory
title: Avenir comparison previews depend on an outlined script asset
summary: Font Pairs / Avenir uses assets/avenir-preview.js for exact Avenir Roman outlines; canvas text silently substitutes Avenir despite its local installation.
status: superseded
trust: agent
roles: [all]
tags: [design, typography, pen]
paths: [docs/design/miroir.pen, docs/design/assets/avenir-preview.js]
author: agent:pen-dev
created: 2026-11-02T04:47:16Z
updated: 2026-11-02T06:54:15Z
kind: gotcha
superseded_by: mem-j3g6cy10
---

In miroir.pen, board s0OpM (Font Pairs / Avenir, x4800 y-5700) explores Avenir Roman with Instrument Serif, Bodoni Moda, EB Garamond, Cormorant Garamond and DM Mono. Avenir fontFamily text rendered substitute metrics (sale at 14px measured 27px vs native Avenir Roman 24.374px), so its forty sample text layers now use script nodes referencing assets/avenir-preview.js. The script contains exact CoreText-generated glyph outlines for eight fixed sample strings, not a bundled font. Keep this asset with the .pen file; editing the sample text requires regenerating the outlines or using a properly supported/licensed font. Companion text is editable. Existing Font Pairs L2Izto and site font variables Inter Tight/DM Mono remain unchanged. These are alternatives, not an approved typography change; Avenir web use still needs the appropriate licence.
