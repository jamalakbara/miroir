# Miroir hi-fi assets

Source: the user-supplied [Website Miroir Drive folder](https://drive.google.com/drive/folders/1j6333qtWCZPU3x23O8AhsWh6uhT8Q_2K).

The `Hi-fi Design` board (`vmoZB`) in `../../miroir.pen` contains Home, Shop, and Product Detail at 1440, 768, and 390 pixels. Existing lo-fi designs and shared component defaults were preserved. Typography remains Avenir + DM Mono.

## Independent hero photos

`banner-desktop-1.jpg` is the original 2049 × 1152 composite from `banner desk/1.jpg` (Drive file `1XsNKekceY21GTK2Wk-GIk1XB7C4G_nh6`). Its two photographs were extracted without generative changes:

- `hero-navy.jpg`: pixel crop `(0, 0, 931, 1152)` → 931 × 1152.
- `hero-yellow.jpg`: pixel crop `(1119, 115, 1913, 1037)` → 794 × 922; surrounding white margin removed.

These are separate image fills in each desktop/tablet hero. Matching WordPress below 600 px, mobile displays only the smaller navy photograph, centered at the top of the editorial row; the larger yellow photograph stays hidden with no reserved space. This replaces the earlier two-image mobile hero. All mobile editorial sections in both lo-fi and hi-fi use this small-image-only behavior. Canvas slots use cover fitting, so their visible crop can differ from the full extracted file.

`banner-desktop-2.jpg` is the supplied three-photo composite from `banner desk/2.jpg`. Its three panels were also cropped into `editorial-kala.webp`, `editorial-brown.webp`, and `editorial-green.webp` for independent editorial placement.

The full triptych banner uses its original 2049:1152 aspect ratio with `contain` fitting. Following the user's Velvet screenshot reference, it is centered inside the section with whitespace rather than filling the content width: desktop 800 × 449.78, tablet 560 × 314.85, mobile 300 × 168.67. The corresponding lo-fi placeholders use the same dimensions. Captions remain aligned with the outer section gutter. All three complete photographs remain visible.

## Product photography and copy

`catalog-N.webp` retains the source catalog filename number, resized to at most 1600 × 2000 for canvas use. Names, prices, description, material, measurements, and weight come from `catalog detail.xlsx` (Drive file `189UsC34OMTTsrtorkxbQIUQT-GioP19L`). The workbook contains embedded product reference photographs that are omitted by a text-only connector fetch.

The 12 products shown on Shop use the following mappings:

| Product | Image | IDR |
| --- | --- | ---: |
| Vibe Stripe White | catalog-59.webp | 325000 |
| Tempo Stripe White | catalog-64.webp | 325000 |
| Lui Stripe Yellow | catalog-69.webp | 325000 |
| Kala Stripe Yellow | catalog-74.webp | 325000 |
| Devon Cream Pants | catalog-79.webp | 425000 |
| Wave Stripe Navy | catalog-90.webp | 325000 |
| Lui Stripe Green | product-reference-8.webp | 325000 |
| Emme Stripe Green | catalog-108.webp | 325000 |
| Morty Stripe Green | product-reference-16.webp | 325000 |
| Tara Green | catalog-100.webp | 325000 |
| Tara Stripe Blue | catalog-110.webp | 325000 |
| Sabine White Pants | catalog-114.webp | 425000 |

The two `product-reference` files used in the design are the workbook's embedded photographs (about 474 px square), adequate for these card sizes; higher-resolution matching originals are preferable for future production use. Other extracted reference images remain available for mapping additional products.

Product Detail features Vibe Stripe White with catalog photos 59, 60, 61, and 62. No stock availability or discounted prices were inferred. Unverified model height was omitted. The source's suspect Lumi Pants measurement was not used.

Full-resolution downloaded catalog originals and inspection contact sheets are retained outside the repository in the current chat's visualization workspace under `miroir-source`.
