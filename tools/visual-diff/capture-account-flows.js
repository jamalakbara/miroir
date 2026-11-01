// Captures screens 12-21 (account flows) at all widths and diffs them.
// Logged out: register, lost password, reset sent, set new password (needs RESET_QUERY="key=...&login=shopper",
// from `wp eval 'echo get_password_reset_key(get_user_by("login","shopper"));'`), password updated.
// Logged in as the local test customer: dashboard, details, addresses, edit address, order details.
const { chromium } = require('playwright-core'); const path = require('path'); const diff = require('./diff');
const base = 'http://localhost:8080', out = path.join(process.env.VDIFF_DIR || path.join(__dirname, 'out'), 'site');
const sizes = { D: 1440, T: 768, M: 390 };
const ids = {"12-D":"jMN4n","13-D":"Wdbxn","14-D":"v2Ztf","15-D":"dyoOP","16-D":"Akrju","17-D":"ypqBb","18-D":"R8ChJq","19-D":"fFZ4R","20-D":"bJTnq","21-D":"tXUSQ","12-T":"A21fD","13-T":"uMVBE","14-T":"juYgi","15-T":"P0ok3","16-T":"PoRRo","17-T":"IWtsd","18-T":"p2Hyux","19-T":"i4Qmjf","20-T":"sfhFt","21-T":"WDvZh","12-M":"wW9la","13-M":"pgb7B","14-M":"V78t9N","15-M":"c1Jtj","16-M":"aA2Sf","17-M":"JzjvP","18-M":"bq2Vn","19-M":"znZN8","20-M":"IFy46","21-M":"y0ShR"};
const user = process.env.SHOPPER_USER || 'shopper', pass = process.env.SHOPPER_PASS || 'shopper-local-test';
const only = (process.env.ONLY || '').split(',').filter(Boolean);
async function shoot(p, screen, url) {
  if (only.length && !only.includes(screen)) return;
  await p.goto(base + url, { waitUntil: 'networkidle' }); await p.evaluate(() => document.fonts.ready);
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
  const p = await b.newPage({ viewport: { width: 1440, height: 900 } });
  await shoot(p, '12', '/my-account/?action=register');
  await shoot(p, '13', '/my-account/lost-password/');
  await shoot(p, '14', '/my-account/lost-password/?reset-link-sent=true');
  if (process.env.RESET_QUERY) await shoot(p, '15', '/my-account/lost-password/?' + process.env.RESET_QUERY);
  await shoot(p, '16', '/my-account/?password-reset=true');
  await p.goto(base + '/my-account/', { waitUntil: 'networkidle' });
  await p.fill('#username', user); await p.fill('#password', pass);
  await Promise.all([p.waitForNavigation({ waitUntil: 'networkidle' }), p.click('button[name=login]')]);
  await shoot(p, '17', '/my-account/');
  await shoot(p, '18', '/my-account/edit-account/');
  await shoot(p, '19', '/my-account/edit-address/');
  await shoot(p, '20', '/my-account/edit-address/shipping/');
  await p.goto(base + '/my-account/orders/', { waitUntil: 'networkidle' });
  const view = await p.getAttribute('.orders-table__row:not(.orders-table__head) a.btn', 'href');
  if (view) await shoot(p, '21', view.replace(base, ''));
  await b.close();
})().catch(e => { console.error(e); process.exit(1); });
