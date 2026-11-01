---
aimem: 1
id: dec-jr10fq3d
type: decision
title: Skeletons track real photo loads only
summary: "User chose image skeletons: shimmer while photos load, static under reduced motion; no page-navigation skeletons or fake delays."
status: proposed
trust: verified
roles: [all]
author: human:akbar
created: 2026-11-01T18:05:44Z
updated: 2026-11-01T18:05:44Z
verified: 2026-11-01
---

Shared miroir_image markup supplies a hidden fallback icon. theme.js watches existing and dynamically inserted photo wrappers; cached photos settle immediately, errors restore accessible grey placeholders, and hover-image failures do not hide loaded primary photos. CSS preserves media geometry and disables shimmer for reduced motion. Product photo preference remains off. Browser checks: node tools/visual-diff/check-image-skeleton.cjs. Pen.dev shared Image Placeholder and Product Card component contexts document loading behavior at all breakpoints.
