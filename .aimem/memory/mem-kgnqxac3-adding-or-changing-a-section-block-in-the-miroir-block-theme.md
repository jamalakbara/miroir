---
aimem: 1
id: mem-kgnqxac3
type: memory
title: Adding or changing a section block in the miroir block theme
summary: "blocks/<name>/block.json (+ 'miroir' key: controls/container) + render.php printing classic markup; auto-registered; generic editor UI in assets/js/blocks-site.js"
status: active
trust: agent
roles: [all]
tags: [blocks, site-editor]
paths: [wp-content/themes/miroir-theme/blocks/**, wp-content/themes/miroir-theme/inc/blocks.php, wp-content/themes/miroir-theme/assets/js/blocks-site.js]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T23:26:27Z
updated: 2026-10-31T23:26:27Z
links: [dec-4a5wfd9e]
kind: howto
---

inc/blocks.php registers every blocks/*/block.json. Home blocks (editorial, banner, product-strip) keep their own UI in blocks-editor.js; every other block uses the generic UI in blocks-site.js, driven by the block.json key 'miroir': controls = [{attr, type: text|toggle|select|number, label, help, options}] for the sidebar; container = {tag, className ('{attr}' placeholders filled), template} makes it an InnerBlocks container whose render.php wraps $content in the exact wrapper element (no wp-block-group / layout classes). Leaves preview with ServerSideRender: render.php must cope with REST previews (miroir_block_preview(); miroir_block_product() / miroir_block_listing_products() return sample data). Shared helpers live in inc/block-helpers.php. Empty attributes fall back to the old theme_mod / miroir_text value. After editing templates/*.html or parts/*.html, run 'wp miroir theme reset-templates' if the Site Editor stored a copy, then tools/visual-diff/parity.js capture+compare against a classic or previous capture.
