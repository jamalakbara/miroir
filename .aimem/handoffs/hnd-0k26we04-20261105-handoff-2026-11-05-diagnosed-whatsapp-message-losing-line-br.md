---
aimem: 1
id: hnd-0k26we04
type: handoff
title: "Handoff 2026-11-05: Diagnosed WhatsApp message losing line breaks: esc_url removes %0A from wa.me href on order sent pa…"
summary: "Done: Diagnosed WhatsApp message losing line breaks: esc_url removes %0A from wa.me href on order sent page (thankyou.php), same gotcha as mem-2f087ftk. Reproduced with wp eval locally. · Next: If us…"
status: final
trust: agent
roles: [all]
author: agent:claude:claude-opus-5-5
created: 2026-11-05T22:31:57Z
updated: 2026-11-05T22:31:57Z
branch: main
---

## Done
Diagnosed WhatsApp message losing line breaks: esc_url removes %0A from wa.me href on order sent page (thankyou.php), same gotcha as mem-2f087ftk. Reproduced with wp eval locally.

## Not done
No code changed. Could not read order 455 on Hostinger: SSH read denied by auto mode, so 1-item preview vs 2-item message is unconfirmed.

## Next
If user approves: print wa.me links with esc_attr instead of esc_url in thankyou.php, view-order.php, product-buy.php, product-state.php, then deploy and test a real order.

## Blocked
none

## Notes for next agent
Order ref line is intentional and absent from preview because order number does not exist yet.
