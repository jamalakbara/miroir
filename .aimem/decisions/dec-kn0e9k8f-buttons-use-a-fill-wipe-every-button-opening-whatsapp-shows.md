---
aimem: 1
id: dec-kn0e9k8f
type: decision
title: Buttons use a fill wipe; every button opening WhatsApp shows the icon
summary: Fill rises from bottom 280ms with site easing, label inverts, press 0.98. Icon on all WhatsApp buttons, including Ask about labels.
status: accepted
trust: verified
roles: [all]
tags: [buttons, motion, whatsapp]
paths: [wp-content/themes/miroir-theme/assets/css/components.css, wp-content/themes/miroir-theme/inc/template-tags.php]
author: human:akbar
created: 2026-11-06T09:49:00Z
updated: 2026-11-06T09:49:00Z
verified: 2026-11-06
---

User chose fill wipe over soft lift and label slide, and chose icon on every button that opens WhatsApp. Lo-fi board stays grey with the lucide bubble: new Button/WhatsApp Outline mbsnn; hi-fi keeps green iS6e3. Motion reference board is Button states SO9Ay in miroir.pen. Block buttons get the icon from the the_content filter, so saved pages need no database edit.
