---
aimem: 1
id: mem-g7qs91re
type: memory
title: "Miroir Elementor layer: widgets render miroir/* block trees; templates swap in via pre_render_block"
summary: "inc/elementor/: Miroir_Widget::tree() -> render_block(); locations.php swaps header/footer parts and <main>; kit-tab.php filters miroir_mod/miroir_text; wp miroir elementor setup|reset"
status: active
trust: agent
roles: [all]
tags: [elementor, howto]
paths: [wp-content/themes/miroir-theme/inc/elementor/**, wp-content/themes/miroir-theme/assets/css/elementor.css]
author: agent:claude-code:claude-opus-5-5
created: 2026-11-01T10:13:26Z
updated: 2026-11-01T10:13:26Z
kind: howto
---

Widgets (inc/elementor/widgets.php) declare a block tree; Miroir_Widget builds parsed blocks from settings (block.json miroir.controls become Elementor controls, optional parts get a show switch) and calls render_block, so markup and state swaps equal the block templates. Widget texts override miroir_text() only while the widget renders (miroir_with_texts()); never put a text that also appears in an AJAX fragment or a panel (nav_bag, panel texts) on a widget. On the storefront widgets print no wrapper (before_render/after_render skipped outside the editor), the document class 'elementor' is renamed 'miroir-elementor' (so Elementor base CSS like .elementor img does not apply) and .miroir-stack containers are display:contents. Templates: Site Settings > Miroir > Templates (kit meta miroir_template_<location>); locations.php pre_render_block at priority 20. Elementor frontend JS is dequeued unless a non-Miroir widget, animation or video/slideshow background renders. Add a widget: subclass in widgets.php, list it in miroir_elementor_widget_classes(), add it to Miroir_Elementor_Command::TEMPLATES if it belongs in a default template.
