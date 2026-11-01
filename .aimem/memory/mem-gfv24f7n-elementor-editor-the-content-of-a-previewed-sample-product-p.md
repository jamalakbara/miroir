---
aimem: 1
id: mem-gfv24f7n
type: memory
title: "Elementor editor: the_content of a previewed sample product prints the edited template"
summary: In Elementor preview the global post is the template; apply_filters('the_content') on a product description injects the template again. Swap global $post to the product first
status: active
trust: agent
roles: [all]
tags: [elementor, gotcha]
paths: [wp-content/themes/miroir-theme/blocks/product-info/render.php]
author: agent:claude-code:claude-opus-5-5
created: 2026-11-01T10:30:15Z
updated: 2026-11-01T10:30:15Z
kind: gotcha
---

blocks/product-info/render.php sets $GLOBALS['post'] to the sample product while filtering its description in previews, then restores it. Without that the Product page template appeared nested inside .pdp__desc in the editor, with its saved settings, which looked like widget switches not working.
