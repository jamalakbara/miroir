---
aimem: 1
id: mem-9tb3t8xm
type: memory
title: Adding admin-editable settings and texts in miroir-theme
summary: Register in inc/customizer/registry.php (miroir_mod) or text.php (miroir_text); CSS tokens per breakpoint (-d/-t/-m) so overrides do not leak
status: active
trust: agent
roles: [all]
tags: [customizer]
paths: [wp-content/themes/miroir-theme/inc/customizer/**, wp-content/themes/miroir-theme/assets/css/tokens.css]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T18:24:24Z
updated: 2026-10-31T18:24:24Z
kind: howto
---

- New setting: add an entry to miroir_settings() (inc/customizer/registry.php); read it with miroir_mod( 'id' ). The Customizer control, sanitizer and live preview are generated. Entries with 'var' write a CSS custom property only when the value differs from the default (miroir_custom_css(), printed after tokens.css).
- Breakpoint values use separate tokens (--gutter-d/-t/-m, --shop-cols-d/-t/-m …) and tokens.css media queries pick the active one. Overriding a single --gutter from inline CSS would also win inside the media queries and break tablet/mobile.
- New store copy: add id => [ section, __( default ), __( label ) ] to miroir_texts() (inc/customizer/text.php) and print miroir_text( 'id', ...args ). Pass sprintf args to miroir_text, never sprintf( miroir_text() ): miroir_text falls back to the default when an edited text breaks its placeholders (vsprintf ValueError/ArgumentCountError).
- Home page: front-page.php renders the_content() only when page_on_front contains a "<!-- wp:miroir/" block (miroir_front_page_id()); the demo seeder's old cover/shortcode content is ignored. `wp miroir theme setup` writes the six lofi sections as blocks when none exist.
