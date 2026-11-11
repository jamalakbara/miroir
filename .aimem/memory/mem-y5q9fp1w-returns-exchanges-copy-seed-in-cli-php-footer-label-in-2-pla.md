---
aimem: 1
id: mem-y5q9fp1w
type: memory
title: "Returns & Exchanges copy: seed in cli.php, footer label in 2 places + menu item"
summary: "Page returns-exchanges (id 258) is Elementor-built; footer label 'Returns & Exchanges' is in cli.php, block-helpers.php and the Footer: policies menu item."
status: active
trust: agent
roles: [all]
tags: [returns, elementor, copy]
paths: [wp-content/themes/miroir-theme/inc/cli.php, wp-content/themes/miroir-theme/inc/block-helpers.php]
author: agent:claude-code:sonnet-5.5
created: 2026-11-11T03:22:13Z
updated: 2026-11-11T03:22:13Z
---

Client-approved Returns & Exchanges copy (2026-11-11), incl. studio return address (Miroir Studio, Jl. Nayaga No. 6, Bandung). Slug stays returns-exchanges. Seed in Miroir_Theme_Command::pages(); blocks() now accepts a list of paragraphs per section. Each environment still needs: post_title, post_content, _elementor_data widget text (miroir-text-page), _miroir_blocks_before_elementor, nav menu item title in Footer: policies (was 'Return policy'), then wp elementor flush-css. Draft note removed. Same pattern as the FAQ refresh, see [[mem-p431md0s]].
