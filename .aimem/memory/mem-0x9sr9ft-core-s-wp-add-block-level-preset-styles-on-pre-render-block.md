---
aimem: 1
id: mem-0x9sr9ft
type: memory
title: Core's _wp_add_block_level_preset_styles on pre_render_block (prio 10) returns null and drops earlier swaps
summary: A pre_render_block filter that returns markup must run after priority 10, or core resets it to null
status: active
trust: agent
roles: [all]
tags: [blocks, gotcha]
paths: [wp-content/themes/miroir-theme/inc/elementor/locations.php]
author: agent:claude-code:claude-opus-5-5
created: 2026-11-01T10:13:30Z
updated: 2026-11-01T10:13:30Z
kind: gotcha
---

wp-includes/block-supports/settings.php adds _wp_add_block_level_preset_styles to pre_render_block at 10; it ignores the incoming value and returns null. Our Elementor location swap ran at 5 and was silently discarded (header showed the block version although the template CSS was enqueued). Use priority 20. block-helpers.php works at 10 only because it is added after core's callback.
