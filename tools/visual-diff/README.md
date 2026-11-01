# Visual diff: site vs miroir.pen

Compares the running site (http://localhost:8080) with PNG exports of the lofi screens.

1. Export the design frames at 1x from Pencil into `out/design/<pen node id>.png`
   (execute: `Export([ids], "png", "<abs path>/tools/visual-diff/out/design", {scale: 1})`).
   Export the inner screen frame, not the "… presentation" wrapper with the label.
2. `npm install` here once (uses the installed Microsoft Edge; no browser download).
3. `node run.js spec-foundation.json` → per item: sizes and % differing pixels;
   site captures in `out/site/`, red-pixel diffs in `out/diff/`.
4. `node zoom.js <pen id> <name> x y w h [scale]` → `out/zoom/<name>.png`, design above site.
5. `node boxes.js <url> <width> <selector>` prints element boxes to compare with pen bounds.

Spec items: `{name, design, url, width, selector?, pre?, setup?}`; `pre` lists URLs visited first in the same
session (e.g. `?add-to-cart=ID`), `setup` is JS run in the page before the shot. `spec-phase2.json` covers
screens 01-05, 10, 11 and `spec-phase4.json` screens 22-30 (30 via `?miroir-maintenance-preview`, allowed on local) at all widths.

Session-bound screens have their own scripts:
- `capture-checkout.js`: 06 review + 07 order sent (adds to bag, submits the WhatsApp form; wa.me answered with 204).
- `capture-account.js`: 08 login + 09 orders for the local test customer, placing orders until there are 3.
  Customer: `docker compose run --rm wpcli user create shopper shopper@example.test --role=customer --display_name=Customer --user_pass=shopper-local-test`
  (local-only test account; override with SHOPPER_USER / SHOPPER_PASS).
- `capture-account-flows.js`: screens 12-21. Set RESET_QUERY for 15:
  `RESET_QUERY="key=$(docker compose run --rm wpcli eval 'echo get_password_reset_key(get_user_by("login","shopper"));' | tail -1)&login=shopper"`;
  `ONLY=12,16` limits screens.
- `flow-account.js`: functional check of register (validation + success), address and details saves, lost password.
  Creates `reg<timestamp>@example.test` customers; delete them afterwards.
- `spec-panels.json`: screens 31-34 via `?panel=nav|search|filters|currency`.
- `flow-navigation.js`: persistent navbar, original hamburger morph, drawer reveal/close animations, shared desktop links, internal scrolling, focus/scroll restoration, admin toolbar offsets, reduced motion and search/currency transitions at responsive sizes.
- `capture-states.js`: screens 35-44 (41 is a notice catalogue with no page). `STOCK_PRODUCT=<id>` enables 40: it bags 2,
  drops that product's stock to 1 through WP-CLI, captures, and restores stock to 12. Screen 42 registers a throwaway
  `reg<timestamp>@example.test` customer: delete it afterwards. `ids-phase5.json` maps screens to pen frame ids.
- `flow-states.js`: functional check of panels, filter form, currency switch, choose-size / added-to-bag states, login notice.
- `sweep.sh`: every layout in one go (set RESET_QUERY first); results in `out/sweep.txt`.
- `flow-whatsapp.js`: functional check of PDP link, bag qty update, review validation and order creation.
- `parity.js`: site vs site, for refactors that must not change the output (e.g. the classic -> block theme
  switch). `node parity.js capture out/parity/<name>` shoots 39 screens x 1440/768/390 (logged out, then the
  test customer); `node parity.js compare out/parity/<before> out/parity/<after>` lists screens with any
  differing pixel and writes `diff-*.png` into the second folder. `ONLY=pdp,cart` limits screens. Capture the
  "before" twice first to confirm the site is deterministic.
- `condense.py out/ref/<NN>-<D|T|M>.html` turns a Pencil html-css export into a short layout outline (`c.sh` shortens it further).

Known data-driven differences: product names that wrap, number of products per category/search, sale prices,
random related products, WooCommerce's "confirm email address" notice on orders, the WhatsApp
message text on 43 (real order message vs the designer's sample).
The floating WhatsApp button and admin bar are hidden in captures. Expect ~0.2-1.5% residual from
text anti-aliasing (Pencil's renderer vs Chromium); layout and box sizes should match exactly.
