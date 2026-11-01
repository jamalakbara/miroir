// Captures 08 (login, logged out) and 09 (orders) for the local test customer, then diffs them.
// The customer is created by `wp user create shopper ...` (see README); orders are placed through the WhatsApp review form.
const { chromium } = require('playwright-core'); const path = require('path'); const diff = require('./diff');
const base = 'http://localhost:8080', out = path.join(process.env.VDIFF_DIR || path.join(__dirname, 'out'), 'site');
const sizes = { D: 1440, T: 768, M: 390 }, design = { '08': { D: 'Bpejo', T: 'Kj9lu', M: 'ER8Ma' }, '09': { D: 'fNtP7', T: 'q4vB9x', M: 'n2X5m' } };
const user = process.env.SHOPPER_USER || 'shopper', pass = process.env.SHOPPER_PASS || 'shopper-local-test';
async function shoot(p, screen) {
  for (const [bp, w] of Object.entries(sizes)) {
    await p.setViewportSize({ width: w, height: 900 }); await p.waitForTimeout(200);
    await p.addStyleTag({ content: '.wa-float{display:none!important}' });
    await p.screenshot({ path: path.join(out, `${screen}-${bp}.png`), fullPage: true });
    diff(design[screen][bp], `${screen}-${bp}`);
  }
}
(async () => {
  const b = await chromium.launch({ channel: 'msedge', args: ['--disable-lcd-text', '--font-render-hinting=none'] });
  const p = await b.newPage({ viewport: { width: 1440, height: 900 } });
  await p.route('https://wa.me/**', r => r.fulfill({ status: 204 }));
  await p.goto(base + '/my-account/', { waitUntil: 'networkidle' }); await p.evaluate(() => document.fonts.ready);
  await shoot(p, '08');
  await p.setViewportSize({ width: 1440, height: 900 });
  await p.fill('#username', user); await p.fill('#password', pass);
  await Promise.all([p.waitForNavigation({ waitUntil: 'networkidle' }), p.click('button[name=login]')]);
  const want = +(process.env.ORDERS || 3);
  await p.goto(base + '/my-account/orders/', { waitUntil: 'networkidle' });
  for (let have = await p.locator('.orders-table__row:not(.orders-table__head)').count(); have < want; have++) {
    await p.goto(base + '/?add-to-cart=228', { waitUntil: 'networkidle' });
    await p.goto(base + '/checkout/', { waitUntil: 'networkidle' });
    await p.fill('#wa_name', 'Customer'); await p.fill('#wa_city', 'Jakarta');
    await Promise.all([p.waitForNavigation({ waitUntil: 'networkidle' }), p.click('button[type=submit].btn--whatsapp')]);
  }
  await p.goto(base + '/my-account/orders/', { waitUntil: 'networkidle' }); await p.evaluate(() => document.fonts.ready);
  await shoot(p, '09');
  await b.close();
})().catch(e => { console.error(e); process.exit(1); });
