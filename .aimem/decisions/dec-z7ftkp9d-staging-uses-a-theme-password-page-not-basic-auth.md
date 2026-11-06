---
aimem: 1
id: dec-z7ftkp9d
type: decision
title: Staging uses a theme password page, not Basic Auth
summary: User rejected the browser popup. Theme gate at staging.miroir.id; password set in Settings > Staging access, hash only, 30-day cookie. Inert unless environment is staging.
status: accepted
trust: verified
roles: [all]
paths: [wp-content/themes/miroir-theme/inc/staging-access.php, wp-content/themes/miroir-theme/template-parts/staging-access.php]
author: human:akbar
created: 2026-11-06T13:20:48Z
updated: 2026-11-06T13:20:48Z
verified: 2026-11-06
supersedes: dec-azeyt16t
---

Designed as S1 Private access in miroir.pen, default and wrong password states at all three widths. Trade-off accepted: unlike Basic Auth, direct uploads and theme asset URLs are public, and wp-login.php is reachable so admins can sign in. Live maintenance and no-Publish rules from dec-azeyt16t still stand: Hostinger Publish replaces live files and database and must not be used.
