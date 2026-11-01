---
aimem: 1
id: dec-x4b1d3eg
type: decision
title: Account pages follow two lofi layouts as drawn (09 side menu vs 17-21 tabs)
summary: Orders (09) keep the 1100px side-menu layout; dashboard/details/addresses/order details (17-21) use the 820px column with tabs. Built literally per screen; flagged to user for a later unification
status: proposed
trust: agent
roles: [all]
tags: [design, account, theme]
paths: [wp-content/themes/miroir-theme/woocommerce/myaccount/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T14:25:06Z
updated: 2026-10-31T14:25:06Z
links: [dec-54vkenha]
---

The original lofi screen 09 (orders) draws My account with a mono side menu in an 1100px column; the later screens 17-21 draw it as an 820px content column with a Title-case tab row. The user asked for 1:1, so my-account.php switches shell by endpoint (orders -> side menu, everything else -> tabs). If the design is unified later, change only woocommerce/myaccount/my-account.php (and drop navigation.php or navigation-tabs.php). Screen 21's 'Integration note' text is a designer annotation and is not rendered.
