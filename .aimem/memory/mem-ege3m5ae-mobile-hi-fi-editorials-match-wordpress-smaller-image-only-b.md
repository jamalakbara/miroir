---
aimem: 1
id: mem-ege3m5ae
type: memory
title: Mobile hi-fi editorials match WordPress smaller-image-only behavior
summary: Below600px, all8 mobile editorial instances in lo-fi/hi-fi hide larger image and center small200.25x267 image at y0; hero no longer stacks two images.
status: superseded
trust: verified
roles: [all]
author: human:akbar
created: 2026-11-02T16:07:26Z
updated: 2026-11-03T07:40:49Z
verified: 2026-11-02
superseded_by: mem-f4y473et
---

User explicitly requested matching existing WordPress editorial visibility. Verified home.css hides .home-editorial__large at max-width599px. Eight Pen mobile instances (5 lo-fi including search/currency modal backgrounds;3 hi-fi) now show small image only at x74.875,y0,width200.25,height267 inside350px row. Hi-fi hero YM54f height reduced737 to267. Desktop/tablet retain two-image compositions. This replaces only the two-image mobile hero portion of dec-pmc8en1s; split assets and nine-screen hi-fi scope remain valid.
