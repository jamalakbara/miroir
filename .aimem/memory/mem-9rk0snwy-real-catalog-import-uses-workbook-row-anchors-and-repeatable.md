---
aimem: 1
id: mem-9rk0snwy
type: memory
title: Real catalog import uses workbook row anchors and repeatable CLI scripts
summary: 18 products imported from Drive; embedded photo numbers are not product-order indexes. Lumi length 962 cm omitted for review.
status: active
trust: verified
roles: [all]
author: human:akbar
created: 2026-11-02T16:40:03Z
updated: 2026-11-02T16:40:03Z
verified: 2026-11-02
kind: howto
---

tools/prepare-catalog.py SOURCE_DIR extracts workbook text and drawing anchor rows into tools/catalog.json and uploads/miroir-catalog-source. Source folder needs catalog-detail.xlsx; downloaded catalog-N.jpg files are retained in uploads. Some reference images are out of sequence: Morty Stripe Green row13=image16; Tara Green row14=image15; Isley row15=image10; Flow row16=image11; Devon Black row17=image12; Emme Brown row18=image13; Morty Brown row19=image17. tools/import-catalog.php runs via docker compose run --rm -T -v F:/projects/miroir/tools:/tmp/miroir-tools:ro wpcli eval-file /tmp/miroir-tools/import-catalog.php. Idempotently imports media, converts matching demos in place, blanks synthetic SKUs and moves other demos to drafts only after successful import. All 18 simple products accept orders without stock tracking, per user. apply-hifi.php updates existing Elementor home widgets and block fallback plus kit settings. DB backup is outside webroot at .aimem/.cache/backups/before-catalog-20261102.sql. Fonts require local gitignored licensed Avenir files on deployment.
