---
aimem: 1
id: dec-hbcz05wy
type: decision
title: Add-to-bag confirms in a drawer or mobile bottom sheet
summary: Keep the PDP in place; show item details, subtotal, View bag and Continue shopping in a dismissible native dialog.
status: proposed
trust: verified
roles: [all]
author: human:akbar
created: 2026-11-01T18:30:42Z
updated: 2026-11-01T18:30:42Z
verified: 2026-11-01
---

User approved the mini-bag recommendation. Desktop/tablet use a 440px right drawer; below 600px use a bottom sheet. Reuse existing native-dialog focus containment, Escape/backdrop close, scroll hold and reduced-motion fade. Submit the existing WooCommerce form through fetch and parse the redirected HTML so stock/variation validation remains native. Never replay an uncertain failed POST; provide a View bag link. Normal no-JS submissions render the same confirmation over the PDP. Pen.dev screen 39 at all three breakpoints and reusable drawer/sheet components were updated using MCP and saved.
