---
aimem: 1
id: mem-p431md0s
type: memory
title: FAQ copy lives in Miroir_Theme_Command::faq_items() and the page's _elementor_data
summary: FAQ page (id 251) is Elementor-built; changing copy needs the seed in inc/cli.php plus the stored _elementor_data, post_content on each environment.
status: active
trust: agent
roles: [all]
tags: [faq, elementor, copy]
paths: [wp-content/themes/miroir-theme/inc/cli.php]
author: agent:claude-code:sonnet-5.5
created: 2026-11-11T03:18:46Z
updated: 2026-11-11T03:18:46Z
---

Client-approved FAQ copy (2026-11-11) is in Miroir_Theme_Command::faq_items() (public static, inc/cli.php). The seed only applies when the page is created or setup --force-pages runs. Existing FAQ page renders from _elementor_data (a miroir-text-page widget, settings.text holds the details markup) so each environment needs that meta, post_content and _elementor_element_cache refreshed, then wp elementor flush-css. Local done via a throwaway wp eval-file script run from inside the theme dir (wpcli container cannot see project root). Page title stays 'FAQ'.
