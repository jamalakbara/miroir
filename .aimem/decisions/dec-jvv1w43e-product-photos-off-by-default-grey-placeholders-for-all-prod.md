---
aimem: 1
id: dec-jvv1w43e
type: decision
title: "Product photos off by default: grey placeholders for all products (temporary)"
summary: User asked (2026-11-01) for design placeholders on all products for now; Customize > Products > 'Show product photos' (product_photos, default off) restores real WC images
status: proposed
trust: agent
roles: [all]
tags: [images, customizer]
paths: [wp-content/themes/miroir-theme/inc/woocommerce.php, wp-content/themes/miroir-theme/template-parts/product-card.php, wp-content/themes/miroir-theme/woocommerce/single-product.php]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T22:34:45Z
updated: 2026-10-31T22:34:45Z
links: [dec-w31np48s]
---

miroir_product_image_id() returns 0 while theme mod product_photos is off, so cards, PDP gallery (lead + gallery views), product-state, bag and checkout thumbs all render miroir_ph(). Editorial/banner block images are not product images and still show. The demo seeder's silhouette images stay attached; tick the setting to show them again.
