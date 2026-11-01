---
aimem: 1
id: dec-bdpbse0v
type: decision
title: Search and currency open as modals over the current page
summary: Search and currency replace full-page panels with native modal dialogs; menu and filters retain existing panel behavior.
status: proposed
trust: verified
roles: [all]
author: human:akbar
created: 2026-10-31T17:31:53Z
updated: 2026-10-31T17:31:53Z
verified: 2026-10-31
---

User requested search and currency controls to open modals, not new pages. Native dialog elements in panels.php and panel-currency.php are moved to body by theme.js, keep page and scroll visible, focus input/checked radio, trap keyboard focus, dismiss via Escape/backdrop/Close and restore focus. Query fallbacks initialize as modals with JavaScript. Search submission and currency Apply still navigate. Updated six desktop/tablet/mobile states 32 and 34 in pen.dev with visible background, 40 percent backdrop and centered dialogs. This changes only the search/currency portion of dec-4fden8b5; other panel/state decisions stay valid.
