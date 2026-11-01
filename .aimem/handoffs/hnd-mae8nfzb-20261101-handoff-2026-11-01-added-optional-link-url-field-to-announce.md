---
aimem: 1
id: hnd-mae8nfzb
type: handoff
title: "Handoff 2026-11-01: Added optional Link URL field to announcement block metadata, shared by Elementor Announcement bar…"
summary: "Done: Added optional Link URL field to announcement block metadata, shared by Elementor Announcement bar widget and WordPress block editor. Full bar renders as a native anchor only for a safe nonempt…"
status: final
trust: verified
roles: [all]
author: human:akbar
created: 2026-11-01T18:41:29Z
updated: 2026-11-01T18:41:29Z
verified: 2026-11-01
branch: main
---

## Done
Added optional Link URL field to announcement block metadata, shared by Elementor Announcement bar widget and WordPress block editor. Full bar renders as a native anchor only for a safe nonempty URL; blank or unsafe URLs retain the div. Preserved ticker and 40px layout; focus outline stays inside bar. Verified PHP syntax, diff whitespace, internal/external/empty/unsafe URL render, hidden bar, Elementor setting-to-render mapping, and browser link dimensions/focus at 1440/768/390 plus Enter navigation.

## Not done
No destination URL chosen or saved; no commit requested.

## Next
Edit Header in Elementor, select Announcement bar, fill Link URL and update. Leave blank for no link.

## Blocked
none

## Notes for next agent
none
