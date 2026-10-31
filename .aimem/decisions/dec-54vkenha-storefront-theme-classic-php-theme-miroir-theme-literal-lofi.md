---
aimem: 1
id: dec-54vkenha
type: decision
title: "Storefront theme: classic PHP theme miroir-theme, literal lofi, built in 5 phases"
summary: User chose (2026-10-31) classic theme + WC template overrides for 1:1 control, grey placeholders instead of photos, all 44 screens phased, and miroir-whatsapp-order saving pending WC orders
status: proposed
trust: agent
roles: [all]
tags: [theme, woocommerce, design]
paths: [wp-content/themes/miroir-theme/**, wp-content/plugins/miroir-whatsapp-order/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T13:06:17Z
updated: 2026-10-31T13:06:17Z
links: [dec-bf8ede4p, dec-grer46n5, dec-c17cg0jt]
---

Choices from the user on 2026-10-31: (1) classic PHP theme (wp-content/themes/miroir-theme) with WooCommerce template overrides, not a block theme: full markup control is what makes 1:1 with the pen feasible; WC block markup would need heavy overrides. (2) Literal lofi: every product/editorial image renders as the grey Image Placeholder (miroir_ph()), no photos until a hi-fi pass. (3) All 44 screen types x 1440/768/390, phased: 1 foundation; 2 storefront 01-11 + miroir-whatsapp-order plugin; 3 account 12-21; 4 info 22-30; 5 overlays/states 31-44. (4) WhatsApp handoff saves a pending WC order before redirect so My account > Orders fills. Rejected: block theme (Site Editor editability not needed; harder pixel match). Theme drops all WC/block/global CSS; tokens.css mirrors pen variables; breakpoints <=1023 tablet, <=599 mobile. Header currency toggle is the theme's own button (screen 34 panel), not the miroir/currency-switcher block.
