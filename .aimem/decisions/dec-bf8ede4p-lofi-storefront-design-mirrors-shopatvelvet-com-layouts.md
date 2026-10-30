---
aimem: 1
id: dec-bf8ede4p
type: decision
title: Lofi storefront design mirrors shopatvelvet.com layouts
summary: docs/design/miroir.pen holds 11 grayscale lofi desktop pages built from shared components, layout copied from shopatvelvet.com
status: proposed
trust: agent
roles: [all]
tags: [design, lofi]
paths: [docs/design/miroir.pen]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-30T21:23:35Z
updated: 2026-10-30T21:23:35Z
---

User asked (2026-10-31) for lofi of every storefront page to match https://shopatvelvet.com/ layout (velvet also runs WooCommerce).

Pages (1440 wide, row at y=200): 01 Home, 02 PLP, 03 PDP, 04 Cart, 05 Cart Empty, 06 Checkout, 07 Order Received, 08 Account Login, 09 Account Orders, 10 Search Results, 11 FAQ info page.
Reusable components above the pages (y=-800): Header, Footer, Product Card, Img placeholder, Button (outline; solid = fill ink override), Field (underline input; chevron icon for selects).
Tokens: grayscale colors, IBM Plex Mono (logo, product names, labels) + Inter light (nav, body), gutter 56.

Velvet patterns copied: mono announcement bar; logo left / sale + shop women + search centre / IDR, my account, bag (n) right; home = editorial pairs (small image + big image + caption) plus product strips; PLP = left category tree + colour checkboxes, 3-col grid, Load more; PDP = narrow left info column (description, price, size select, Add to bag, prev/next) + single centred image stack + similar products row; centred narrow forms with hairline rules for cart and account.

Not seen live: velvet filled cart and checkout (cart was empty; we did not add items on their live store). Those two pages follow velvet's visual language with standard WooCommerce structure. Checkout payment options match local seed (bank transfer, COD). Only desktop; no mobile frames yet.
