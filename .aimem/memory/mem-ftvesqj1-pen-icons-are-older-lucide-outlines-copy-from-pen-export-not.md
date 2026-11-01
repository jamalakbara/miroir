---
aimem: 1
id: mem-ftvesqj1
type: memory
title: Pen icons are older lucide outlines; copy from pen export, not lucide-static
summary: Pencil draws lucide icons as filled 14x14 outline paths from an older lucide (menu lines at 6/12/18); inc/icons.php holds those exported paths
status: active
trust: agent
roles: [all]
tags: [theme, icons, design]
paths: [wp-content/themes/miroir-theme/inc/icons.php]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T13:05:57Z
updated: 2026-10-31T13:05:57Z
kind: gotcha
---

Get the exact glyph with execute Export([...], 'html-css', ...): each icon svg carries data-icon-name and a fill path on viewBox 0 0 14 14. Current lucide-static (1.66) moved geometry (menu bars at 5/12/19, new message-circle), which shows up in pixel diffs. inc/icons.php has: arrow-up-right, check, chevron-down, eye-off, image, maximize-2, menu, message-circle, minus, plus, x. Icons inside component instances need Get(..., {resolveInstances:true}) to find; export the instance id.
