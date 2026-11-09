---
aimem: 1
id: dec-c19gnxwb
type: decision
title: Theme prints SEO head and schema itself; stands down when an SEO plugin is active
summary: "inc/seo.php: meta description, listing canonicals, OG/Twitter, Org+WebSite JSON-LD, WC Product+Breadcrumb JSON-LD, favicon, noindex variants/empty collections, lean sitemap."
status: proposed
trust: verified
roles: [all]
tags: [seo]
paths: [wp-content/themes/miroir-theme/inc/seo.php]
author: human:akbar
created: 2026-11-09T14:43:06Z
updated: 2026-11-09T14:43:06Z
verified: 2026-11-09
---

No SEO plugin installed, and the theme's blocks skip woocommerce_single_product_summary and woocommerce_breadcrumb, so WooCommerce never generated its JSON-LD. Theme now calls WC()->structured_data generators at wp_footer 5. ?panel=, sort/filter and currency URLs get noindex,follow (not robots.txt Disallow, which would hide the canonical) and panel links get rel=nofollow. Empty product categories are noindex. Home and collections get a screen-reader h1 because the desktop design shows no page title. Descriptions editable under Customize > Text & labels > Search engines. If Yoast/Rank Math/AIOSEO/SEOPress/TSF is installed, head output and schema stop.
