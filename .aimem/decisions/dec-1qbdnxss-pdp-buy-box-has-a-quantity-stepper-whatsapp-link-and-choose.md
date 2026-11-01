---
aimem: 1
id: dec-1qbdnxss
type: decision
title: PDP buy box has a quantity stepper; WhatsApp link and choose-size state follow it
summary: Qty Stepper sits between size and Add to bag at all breakpoints; posts WooCommerce quantity, clamps to stock/variation limits, WA message uses qty and line total.
status: superseded
trust: agent
roles: [all]
tags: [pdp, quantity, whatsapp]
paths: [wp-content/themes/miroir-theme/template-parts/product-buy.php, wp-content/themes/miroir-theme/assets/js/theme.js, wp-content/plugins/miroir-whatsapp-order/assets/product.js]
author: agent:claude-code:claude-opus-5-5
created: 2026-11-01T18:58:54Z
updated: 2026-11-01T19:03:39Z
links: [dec-hbcz05wy]
superseded_by: dec-q4tnbxnz
---

User requested a quantity changer on the PDP (2026-11-02). Reuses the Qty Stepper component (y40JRr / .qty) left-aligned under the size select. Starts at the product minimum; minus disabled at minimum, plus at max purchase quantity; typed values are clamped on change. Sold-individually simple products keep a hidden quantity=1; variable products get min/max/sold-individually per variation from a jQuery found_variation/reset_data hook in theme.js. Order via WhatsApp now uses the chosen quantity and line total (unit display price x qty), restoring the server-built link at qty 1 with no size change. Add to bag before choosing a size carries ?quantity=N to the choose-size state (44), whose hidden quantity keeps it. Pen.dev: stepper added to all nine PDP buy boxes (03, 39, navbar-bag screens x desktop/tablet/mobile) with disabled-looking minus.
