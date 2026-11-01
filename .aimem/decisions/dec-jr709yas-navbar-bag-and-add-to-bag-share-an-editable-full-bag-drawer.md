---
aimem: 1
id: dec-jr709yas
type: decision
title: Navbar bag and add-to-bag share an editable full-bag drawer
summary: Show every bag line with quantity controls and current totals; keep the drawer and backdrop below the visible WordPress toolbar.
status: proposed
trust: verified
roles: [all]
author: human:akbar
created: 2026-11-01T18:42:54Z
updated: 2026-11-01T18:42:54Z
verified: 2026-11-01
---

User requested toolbar clearance, quantity editing, and navbar bag entry. Drawer now renders every cart line for both entry points. Navbar uses Bag heading and refreshes contents from the native cart page; add success retains Added to your bag. Quantity edits post standard cart quantities and WooCommerce nonce to the native cart handler, then refresh drawer, navbar fragments and any visible bag-page content. Zero removes a line; empty state remains in the drawer. Toolbar offset measures visible wpadminbar bottom on open and resize, including mobile scroll-away; guest offset is zero. Pen.dev components and three responsive confirmation screens updated, plus three explicit navbar-entry screens.
