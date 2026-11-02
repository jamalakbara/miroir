---
aimem: 1
id: mem-j3g6cy10
type: memory
title: Native editable Avenir is enabled through local font-face browser import
summary: Avenir now renders as native editable text after browser import registered local font-face sources. Outline preview scripts are no longer needed in the current canvas.
status: active
trust: agent
roles: [all]
tags: [design, typography, pen]
paths: [docs/design/miroir.pen, docs/design/assets/avenir-preview.js]
author: agent:pen-dev
created: 2026-11-02T06:54:15Z
updated: 2026-11-02T06:54:15Z
supersedes: mem-x30vreww
kind: howto
---

Directly naming Avenir originally caused silent fallback despite the Mac font being installed. Supported workaround discovered: browser import of a local HTML element using explicit @font-face records registers those records in the document font library. Imported Avenir Roman 400 and Medium 500; verified sale at 14px is 25px wide (native Roman advance 24.374px), not the fallback 27px. Native text stays editable and every component instance inherits font tokens. Local design-only font sources live outside the repository in ~/Library/Application Support/miroir/design-fonts/Avenir-Roman.ttf and Avenir-Medium.ttf, derived from the already installed system Avenir collection. Do not deploy or redistribute these desktop font files as website assets; obtain the appropriate licence. Replaced all forty outline specimen scripts with native text. The legacy assets/avenir-preview.js remains on disk because the older saved .pen may still reference it until the user saves the updated canvas; remove only after verifying the saved document has no references. Import validation frames were removed; font-library registration remains active independently. A new machine must supply its own licensed local fonts and reconnect/reimport them. Automated Cmd+S was blocked by macOS permissions, so this session's canvas changes require the user's manual save.
