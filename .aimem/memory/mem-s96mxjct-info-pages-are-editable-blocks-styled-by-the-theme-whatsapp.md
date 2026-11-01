---
aimem: 1
id: mem-s96mxjct
type: memory
title: "Info pages are editable blocks styled by the theme; #whatsapp links resolve at render"
summary: Screens 22-29 = default page template; content = paragraph + Group blocks with class 'section' + Button block; href=#whatsapp becomes the wa.me link; FAQ uses page template faq.php
status: active
trust: agent
roles: [all]
tags: [theme, content, pages, wp-cli]
paths: [wp-content/themes/miroir-theme/inc/cli.php, wp-content/themes/miroir-theme/page.php, wp-content/themes/miroir-theme/assets/css/flow.css]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-31T14:47:56Z
updated: 2026-10-31T14:47:56Z
links: [mem-jc3esnfa, tsk-gw91zc27]
kind: howto
---

wp miroir theme setup [--force-pages] creates/overwrites: shipping-information, returns-exchanges, terms-of-use, privacy-policy (also set as WP privacy page), contact, payment-confirmation, track-your-order, faq (template page-templates/faq.php). Content is lofi placeholder copy incl. 'DRAFT · Policy copy requires approval before launch.' (p.note-draft) and the WhatsApp-first scope note on 28/29 (p.note-scope): editors delete those paragraphs when approved copy lands. Editing rules for flow.css: top-level paragraphs = intro copy; Group block with additional CSS class 'section' = ruled section (first heading = mono label); Buttons block = full-width buttons, 'Outline' style = secondary. Any link to '#whatsapp' in page content is rewritten to the shop's wa.me URL (the_content filter in template-tags.php). Classic themes wrap Group children in .wp-block-group__inner-container (handled with display:contents). Core block CSS is forced into the dequeued wp-block-library (should_load_separate_core_block_assets false). Legacy pages delivery-and-returns, terms-and-conditions, refund_returns, about still exist with demo content: map them in the redirect task. Maintenance: wp miroir theme maintenance on|off (503 + Retry-After for non-admins); local preview ?miroir-maintenance-preview.
