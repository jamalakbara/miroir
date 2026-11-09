---
aimem: 1
id: hnd-0jqrmyqp
type: handoff
title: "Handoff 2026-11-09: Applied client photo mapping (catalog, drive-N and pic revisi files) to 10 products locally and on…"
summary: "Done: Applied client photo mapping (catalog, drive-N and pic revisi files) to 10 products locally and on staging; verify-catalog passed locally. Pushed development and main. · Next: With user OK run…"
status: final
trust: verified
roles: [all]
author: human:akbar
created: 2026-11-09T05:21:41Z
updated: 2026-11-09T05:21:41Z
verified: 2026-11-09
branch: development
---

## Done
Applied client photo mapping (catalog, drive-N and pic revisi files) to 10 products locally and on staging; verify-catalog passed locally. Pushed development and main.

## Not done
Live DB not updated: production write was blocked by permission check. Photos are already copied to live source folder; scripts sit in ~/miroir-tools-tmp outside web root.

## Next
With user OK run update-catalog-photos.php on live for the 10 products, then remove ~/miroir-tools-tmp. Investigate theme.js settle() leaving has-image-error after image loads.

## Blocked
User permission for live write.

## Notes for next agent
Local catalog-N files are numbered about 10-11 higher than Drive catalog numbers; see prepare-catalog.py groups. Local catalog-84 (Devon Cream side view) dropped per client list.
