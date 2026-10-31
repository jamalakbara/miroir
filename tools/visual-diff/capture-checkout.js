// Captures screens 06 (review, empty fields) and 07 (order sent) inside one shopping session, then diffs them.
const { chromium } = require('playwright-core'); const path = require('path'); const diff = require('./diff');
const base = 'http://localhost:8080', out = path.join(process.env.VDIFF_DIR || path.join(__dirname, 'out'), 'site');
const sizes = { D: 1440, T: 768, M: 390 }, design = { '06': { D: 'h3hSeq', T: 'KH9TM', M: 'V8DJkV' }, '07': { D: 'dHVmF', T: 'C3HhJO', M: 'gyPDZ' } };
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
  await p.route('https://wa.me/**', r => r.fulfill({ status: 204 })); // 204: the auto-open stays on the page
  for (const u of ['/?add-to-cart=228', '/?add-to-cart=218&variation_id=221&attribute_size=M', '/cart/']) await p.goto(base + u, { waitUntil: 'networkidle' });
  await p.goto(base + '/checkout/', { waitUntil: 'networkidle' }); await p.evaluate(() => document.fonts.ready);
  await shoot(p, '06');
  await p.fill('#wa_name', 'Full Name'); await p.fill('#wa_city', 'Town / City');
  await Promise.all([p.waitForNavigation({ waitUntil: 'networkidle' }), p.click('button[type=submit].btn--whatsapp')]);
  await p.evaluate(() => document.fonts.ready);
  await shoot(p, '07');
  await b.close();
})().catch(e => { console.error(e); process.exit(1); });
