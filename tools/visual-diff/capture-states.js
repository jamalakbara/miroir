// Captures screens 35-44 (states) at all widths and diffs them. 41 (notice catalogue) has no page of its own.
// 40 needs a product whose stock is below the bag quantity: run with STOCK_PRODUCT=<id> after lowering its
// stock to 1 (see README). 42 registers a throwaway customer (reg<timestamp>@example.test): delete it afterwards.
const { chromium } = require('playwright-core'); const path = require('path'); const diff = require('./diff');
const base = 'http://localhost:8080', out = path.join(process.env.VDIFF_DIR || path.join(__dirname, 'out'), 'site');
const ids = require('./ids-phase5.json'), sizes = { D: 1440, T: 768, M: 390 };
const only = (process.env.ONLY || '').split(',').filter(Boolean);
async function shoot(p, screen, url) {
  if (only.length && !only.includes(screen)) return;
  if (url) await p.goto(base + url, { waitUntil: 'networkidle' });
  await p.evaluate(() => document.fonts.ready);
  for (const [bp, w] of Object.entries(sizes)) {
    await p.setViewportSize({ width: w, height: 900 }); await p.waitForTimeout(150);
    await p.addStyleTag({ content: '.wa-float{display:none!important}' });
    await p.screenshot({ path: path.join(out, `${screen}-${bp}.png`), fullPage: true });
    diff(ids[`${screen}-${bp}`], `${screen}-${bp}`);
  }
  await p.setViewportSize({ width: 1440, height: 900 });
}
(async () => {
  const b = await chromium.launch({ channel: 'msedge', args: ['--disable-lcd-text', '--font-render-hinting=none'] });
  const ctx = await b.newContext({ viewport: { width: 1440, height: 900 } });
  await ctx.route('https://wa.me/**', r => r.fulfill({ status: 204 }));
  await ctx.route('**/miroir-whatsapp-order/assets/sent.js*', r => r.fulfill({ status: 200, contentType: 'text/javascript', body: '' }));
  const p = await ctx.newPage();
  await shoot(p, '35', '/?s=silver+linen+jacket&post_type=product');
  await shoot(p, '36', '/product-category/baju/women/tops/?filter_color=purple&query_type_color=or');
  await shoot(p, '37', '/size-guide/');
  await shoot(p, '38', '/product/sabine-ballon-pants/');
  await shoot(p, '44', '/product/vale-dress/?choose-size=1');
  if (!only.length || only.includes('39')) { await p.goto(base + '/?add-to-cart=228', { waitUntil: 'networkidle' }); await shoot(p, '39'); }
  if (process.env.STOCK_PRODUCT && (!only.length || only.includes('40'))) {
    // Bag 2, then drop stock to 1 behind the bag's back, capture, restore.
    const { execFileSync } = require('child_process');
    const stock = n => execFileSync('docker', ['compose', 'run', '--rm', 'wpcli', 'eval', `$p=wc_get_product(${+process.env.STOCK_PRODUCT});$p->set_stock_quantity(${n});$p->save();`], { cwd: path.join(__dirname, '..', '..'), stdio: 'ignore' });
    await p.goto(base + '/cart/', { waitUntil: 'networkidle' });
    for (const a of await p.$$eval('.cart-item__remove', l => l.map(x => x.href))) await p.goto(a, { waitUntil: 'networkidle' });
    await p.goto(base + `/?add-to-cart=${process.env.STOCK_PRODUCT}&quantity=2`, { waitUntil: 'networkidle' });
    stock(1);
    try { await shoot(p, '40', '/cart/'); } finally { stock(12); }
  }
  if (!only.length || only.includes('43')) {
    await p.goto(base + '/checkout/', { waitUntil: 'networkidle' });
    await p.fill('#wa_name', 'Customer'); await p.fill('#wa_city', 'City');
    await Promise.all([p.waitForNavigation({ waitUntil: 'networkidle' }), p.click('button[type=submit].btn--whatsapp')]);
    await shoot(p, '43', p.url().replace(base, '') + '&wa-fallback=1');
  }
  if (!only.length || only.includes('42')) {
    const fresh = await (await b.newContext({ viewport: { width: 1440, height: 900 } })).newPage();
    await fresh.goto(base + '/my-account/?action=register', { waitUntil: 'networkidle' });
    await fresh.fill('#reg_first_name', 'New'); await fresh.fill('#reg_last_name', 'Customer');
    await fresh.fill('#reg_email', `reg${Date.now()}@example.test`); await fresh.fill('#reg_password', 'a-long-local-test-pass');
    await Promise.all([fresh.waitForNavigation({ waitUntil: 'networkidle' }), fresh.click('button[name=register]')]);
    await shoot(fresh, '42', '/my-account/orders/');
  }
  await b.close();
})().catch(e => { console.error(e); process.exit(1); });
