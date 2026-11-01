// Site-vs-site parity: proves a refactor (e.g. classic -> block theme) renders the same pixels.
//   node parity.js capture <dir>      screenshots every screen below at 1440/768/390 into <dir>
//   node parity.js compare <a> <b>    pixel-compares two capture dirs; writes diff-*.png into <b>
// ONLY=pdp,cart limits screens. Needs the local test customer (see README) for the account screens.
// Not covered (data changes per run): 07 order sent / 43 fallback (new order each time), 15 set new password,
// 40 stock changed (edits stock), 42 registration (creates a user). flow-*.js and sweep.sh cover those.
const path = require('path'), fs = require('fs');
const { chromium } = require('playwright-core');
const { PNG } = require('pngjs');
const pixelmatch = require('pixelmatch');

const base = 'http://localhost:8080';
const user = process.env.SHOPPER_USER || 'shopper', pass = process.env.SHOPPER_PASS || 'shopper-local-test';
const widths = { D: 1440, T: 768, M: 390 };
const only = (process.env.ONLY || '').split(',').filter(Boolean);
// Random or time-based parts are hidden so two captures of the same site match.
const freeze = '.wa-float,#wpadminbar{display:none!important}html{margin-top:0!important}' +
  '.announce__track{animation:none!important}.similar{display:none!important}';

// [name, url, steps run first in the same session]; order matters: the bag fills up as the list goes.
const loggedOut = [
  ['home', '/'], ['shop', '/shop-2/'], ['cat', '/product-category/new-collection/'],
  ['search', '/?s=dress&post_type=product'], ['noresults', '/?s=silver+linen+jacket&post_type=product'],
  ['emptycat', '/product-category/baju/women/tops/?filter_color=purple&query_type_color=or'],
  ['pdp', '/product/arlene-outer-dress/'], ['soldout', '/product/sabine-ballon-pants/'],
  ['choosesize', '/product/vale-dress/?choose-size=1'],
  ['faq', '/faq/'], ['guide', '/size-guide/'], ['shipping', '/shipping-information/'], ['returns', '/returns-exchanges/'],
  ['terms', '/terms-of-use/'], ['privacy', '/privacy-policy/'], ['contact', '/contact/'],
  ['payconfirm', '/payment-confirmation/'], ['track', '/track-your-order/'], ['404', '/no-such-page-xyz/'],
  ['maintenance', '/?miroir-maintenance-preview'],
  ['p-nav', '/?panel=nav'], ['p-search', '/?panel=search'], ['p-filters', '/shop-2/?panel=filters'], ['p-currency', '/?panel=currency'],
  ['cartempty', '/cart/'],
  ['login', '/my-account/'], ['register', '/my-account/?action=register'], ['lostpw', '/my-account/lost-password/'],
  ['resetsent', '/my-account/lost-password/?reset-link-sent=true'], ['pwupdated', '/my-account/?password-reset=true'],
  ['added', null, ['/?add-to-cart=228']],
  ['cart', '/cart/', ['/?add-to-cart=218&variation_id=221&attribute_size=M']],
  ['review', '/checkout/'],
];
const loggedIn = [
  ['dashboard', '/my-account/'], ['orders', '/my-account/orders/'], ['details', '/my-account/edit-account/'],
  ['addresses', '/my-account/edit-address/'], ['editaddress', '/my-account/edit-address/shipping/'], ['vieworder', 'first-order'],
];

async function shoot(p, dir, name, url) {
  if (only.length && !only.includes(name)) return;
  for (const [bp, w] of Object.entries(widths)) {
    await p.setViewportSize({ width: w, height: 900 });
    if (url) await p.goto(base + url, { waitUntil: 'networkidle' }); else await p.reload({ waitUntil: 'networkidle' });
    await p.evaluate(() => document.fonts.ready);
    await p.addStyleTag({ content: freeze });
    await p.waitForTimeout(150);
    await p.screenshot({ path: path.join(dir, `${name}-${bp}.png`), fullPage: true });
    fs.writeFileSync(path.join(dir, `${name}-${bp}.html`), await p.evaluate(() => (document.querySelector('main') || document.body).outerHTML));
  }
}

async function capture(dir) {
  fs.mkdirSync(dir, { recursive: true });
  const b = await chromium.launch({ channel: 'msedge', args: ['--disable-lcd-text', '--font-render-hinting=none'] });
  const ctx = await b.newContext({ viewport: { width: 1440, height: 900 }, deviceScaleFactor: 1 });
  await ctx.route('https://wa.me/**', (r) => r.fulfill({ status: 204 }));
  const p = await ctx.newPage();
  for (const [name, url, pre = []] of loggedOut) {
    for (const u of pre) await p.goto(base + u, { waitUntil: 'networkidle' });
    await shoot(p, dir, name, url);
  }
  await p.goto(base + '/my-account/', { waitUntil: 'networkidle' });
  await p.fill('#username', user); await p.fill('#password', pass);
  await Promise.all([p.waitForNavigation({ waitUntil: 'networkidle' }), p.click('button[name=login]')]);
  for (const [name, url] of loggedIn) {
    let u = url;
    if (u === 'first-order') {
      await p.goto(base + '/my-account/orders/', { waitUntil: 'networkidle' });
      const href = await p.$eval('a[href*="view-order"]', (a) => a.href).catch(() => null);
      if (!href) { console.log('vieworder: no orders, skipped'); continue; }
      u = href.replace(base, '');
    }
    await shoot(p, dir, name, u);
  }
  await b.close();
  console.log('captured', dir);
}

function compare(a, b) {
  let bad = 0, n = 0;
  for (const f of fs.readdirSync(a).filter((x) => x.endsWith('.png') && !x.startsWith('diff-'))) {
    n++;
    if (!fs.existsSync(path.join(b, f))) { console.log(f.padEnd(22), 'missing'); bad++; continue; }
    const A = PNG.sync.read(fs.readFileSync(path.join(a, f))), B = PNG.sync.read(fs.readFileSync(path.join(b, f)));
    if (A.width !== B.width || A.height !== B.height) { console.log(f.padEnd(22), `size ${A.width}x${A.height} vs ${B.width}x${B.height}`); bad++; continue; }
    const d = new PNG({ width: A.width, height: A.height });
    const px = pixelmatch(A.data, B.data, d.data, A.width, A.height, { threshold: 0.05 });
    if (px) { fs.writeFileSync(path.join(b, 'diff-' + f), PNG.sync.write(d)); console.log(f.padEnd(22), px, 'px differ'); bad++; }
  }
  console.log(bad ? `${bad} of ${n} differ` : `all ${n} identical`);
  process.exitCode = bad ? 1 : 0;
}

const [, , mode, a, b] = process.argv;
if (mode === 'capture' && a) capture(a).catch((e) => { console.error(e); process.exit(1); });
else if (mode === 'compare' && a && b) compare(a, b);
else console.log('usage: node parity.js capture <dir> | compare <a> <b>');
