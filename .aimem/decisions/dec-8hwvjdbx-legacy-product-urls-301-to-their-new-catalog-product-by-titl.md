---
aimem: 1
id: dec-8hwvjdbx
type: decision
title: Legacy product URLs 301 to their new-catalog product by title, else shop
summary: 22 legacy slugs map to a product title in MIROIR_LEGACY_PRODUCTS (same, renamed or sibling colour); other 404 products and dead listings go to shop or nearest term.
status: proposed
trust: agent
roles: [all]
paths: [wp-content/plugins/miroir-redirects/**, docs/migration/legacy-urls.csv]
author: agent:claude-code
created: 2026-11-20T06:26:34Z
updated: 2026-11-20T06:26:34Z
links: [tsk-gw91zc27]
supersedes: dec-3n1dn4z9
---

User approved 2026-11-20 redirecting renamed products and discontinued colours to the same model. Title lookup instead of slug because live slugs were unverified. Tara white/cream go to Tara Green (agent pick over Tara Stripe Blue). Sabine ballon -> Sabine White Pants unconfirmed. Unpublished target falls back to shop.
