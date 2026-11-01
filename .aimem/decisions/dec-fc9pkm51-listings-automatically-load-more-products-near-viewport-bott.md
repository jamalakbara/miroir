---
aimem: 1
id: dec-fc9pkm51
type: decision
title: Listings automatically load more products near viewport bottom
summary: User requested scroll loading. Observe pagination link within 400px; retain manual/no-JS fallback and pause automatic retries after failure.
status: proposed
trust: verified
roles: [all]
author: human:akbar
created: 2026-11-01T18:21:06Z
updated: 2026-11-01T18:21:06Z
verified: 2026-11-01
---

theme.js uses IntersectionObserver for existing shop/search listing pagination. Unobserve during fetch to prevent parallel requests, validate successful non-redirected responses and product markup, append cards, reobserve next link, remove at final page. Screen reader status announces progress. Automatic errors retain current products and pause retries; manual errors navigate via existing next-page link. Verified shop at 1440/768/375px (12 to 24 to 28), failure/manual retry and no-JS navigation. Pen.dev desktop/tablet/mobile pagination contexts synchronized.
