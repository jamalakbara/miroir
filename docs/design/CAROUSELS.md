# Product carousel design states

`miroir.pen` now represents the two homepage product strips as horizontal carousels in both the Hi-fi Design and Lofi Design boards. The separate **Carousel States / Responsive** board (`dRBrf`) documents five states for each strip at three breakpoints: 30 section studies in total.

## Theme contract

Verified against `wp-content/themes/miroir-theme/template-parts/home-strip.php`, `assets/css/home.css`, and the product-carousel behavior in `assets/js/theme.js`.

| Viewport | New arrivals visible | Collection visible | Gap | Side gutters |
| --- | ---: | ---: | ---: | ---: |
| Desktop 1440 | 5 | 6 | 20 | 56 |
| Tablet 768 | 3 | 3 | 20 | 32 |
| Mobile 390 | 2 | 2 | 12 | 20 |

Image ratio stays 3:4. Cards keep their width and stay on one row; the viewport clips the offscreen track. The design uses 12 representative products per strip to illustrate overflow. Actual product count is data-driven: the block's default limit is `-1`.

Navigation is in the same header row as the section link, aligned right. Buttons are 44 × 28 with a 12-pixel gap and 24 × 12 arrow paths, matching the theme. Disabled arrows have 25% opacity.

## States

1. **Start:** track at zero; previous disabled, next enabled.
2. **Next page / snapped:** advance by the number of visible cards, clamped to the track end. Previous becomes enabled; next becomes disabled if the end is reached.
3. **Mid-scroll / dragging:** both arrows enabled; partially visible edge cards intentionally show an active drag. These partial cards are not the resting layout.
4. **End:** final product aligned to the viewport end; next disabled, previous enabled.
5. **All items fit:** sample contains only the visible card count; both navigation controls hidden and dragging inactive.

The native theme supports mouse dragging after a 6-pixel threshold, native touch swiping, horizontal scroll snapping on release, and Left/Right keyboard navigation when the track is focused. A drag suppresses accidental product-link activation. Navigation is smooth unless reduced motion requests instant movement. There is no autoplay, looping, dot pagination, or vertical wrapping.

The Pen designs are static state references with behavior in node context. Website code was not changed in this design update.

## Mobile PDP photo gallery

Below 600 px, PDP photos are now designed as one horizontal carousel above the product details. All four 350 × 466.67 photo slides share a clipped viewport; there is no separate stacked gallery below the details. A counter (`1 / 4` through `4 / 4`) and previous/next arrows sit below the active photo, with 44 × 44 targets and disabled end arrows at 25% opacity. Swiping and keyboard Left/Right advance one photo and snap to its edge. There is no autoplay or looping, and reduced motion uses instant movement. Zoom opens the active photo.

Both regular lo-fi/hi-fi mobile PDPs and lo-fi unavailable, choose-size, added-to-bag and navbar-bag backgrounds use this gallery. Desktop/tablet galleries remain stacked. The **Mobile PDP / Gallery Carousel States** board (`bB57s`) shows first, next and last photo in lo-fi and hi-fi. These are static design states; the WordPress implementation is unchanged by this design task.

## Homepage sections updated

| Board | Desktop | Tablet | Mobile |
| --- | --- | --- | --- |
| Hi-fi new arrivals | E8GZh | PI1KO | bnL6A |
| Hi-fi collection | fIsKF | Qq9hO | NJRbZ |
| Lo-fi new arrivals | F2b21K | NqDFf | g3gZN |
| Lo-fi collection | O3a3o | d3FpP | kNu0g |
