---
aimem: 1
id: mem-90asp7ps
type: memory
title: Text pages are Elementor pages with the Text page widget (miroir/page-text)
summary: setup converts every page except home/bag/checkout/account/shop into one miroir-text-page widget holding the page's rendered the_content HTML; output 1:1
status: active
trust: agent
roles: [all]
tags: [elementor, pages, howto]
paths: [wp-content/themes/miroir-theme/inc/elementor/cli.php, wp-content/themes/miroir-theme/blocks/page-text/**, wp-content/themes/miroir-theme/inc/elementor/widgets.php]
author: agent:claude-code:claude-opus-5-5
created: 2026-11-01T10:54:38Z
updated: 2026-11-01T10:54:38Z
kind: howto
---

User wants every page in Elementor (2026-11-01). Miroir_Widget_Text_Page renders miroir/page (variant from page template: page-faq=info, page-guide=guide) with page-title and the internal block miroir/page-text (inserter false), which prints the WYSIWYG html in the same info__content/flow__content wrapper as page-content. setup stores the page's the_content output (Elementor's apply_builder_in_content filter removed during capture) so markup is byte-identical (verified for 13 pages; parity 117/117). Block markup is kept in _miroir_blocks_before_elementor; reset restores every page carrying that meta. TinyMCE keeps details/summary, tables and wp-block-button divs with classes. The output is not run through wpautop (it would wrap block divs). Shop page is skipped: it is the product listing (collection template).
