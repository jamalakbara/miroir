// End-to-end: PDP size pick updates the WhatsApp link; bag -> review -> submit -> order sent page.
const { chromium } = require('playwright-core');
const base = 'http://localhost:8080';
(async () => {
  const b = await chromium.launch({ channel: 'msedge' });
  const p = await b.newPage({ viewport: { width: 1440, height: 900 } });
  await p.route('https://wa.me/**', r => r.abort());
  const errors = []; p.on('pageerror', e => errors.push(e.message));

  await p.goto(base + '/product/vale-dress/', { waitUntil: 'networkidle' });
  const before = await p.getAttribute('[data-wa-product]', 'href');
  await p.selectOption('.variations_form select', 'L');
  await p.waitForTimeout(300);
  const after = await p.getAttribute('[data-wa-product]', 'href');
  console.log('PDP wa link changes on size:', before !== after, '|', decodeURIComponent(after.split('text=')[1]).replace(/\n/g, ' / '));
  await p.click('.single_add_to_cart_button');
  await p.waitForLoadState('networkidle');
  console.log('header after add:', (await p.textContent('.site-nav__bag')).trim());

  await p.goto(base + '/cart/', { waitUntil: 'networkidle' });
  await Promise.all([p.waitForNavigation({ waitUntil: 'networkidle' }), p.click('[data-qty-step="1"]')]);
  console.log('qty after +:', await p.inputValue('.qty__input'));

  await p.goto(base + '/checkout/', { waitUntil: 'networkidle' });
  await p.click('button[type=submit].btn--whatsapp'); await p.waitForLoadState('networkidle');
  console.log('empty submit errors:', (await p.locator('.woocommerce-error li, .woocommerce-error').allTextContents()).map(t => t.trim()).join(' | '));
  await p.fill('#wa_name', 'Test Customer'); await p.fill('#wa_city', 'Jakarta'); await p.fill('#wa_notes', 'gift wrap');
  console.log('preview:', (await p.textContent('[data-wa-preview]')).replace(/\n/g, ' / '));
  await p.click('button[type=submit].btn--whatsapp'); await p.waitForLoadState('networkidle');
  console.log('landed:', p.url().replace(base, ''));
  console.log('title:', await p.textContent('.page-title'), '| retry href ok:', (await p.getAttribute('[data-wa-open]', 'href')).startsWith('https://wa.me/6281200000000?text='));
  console.log('bag after:', (await p.textContent('.site-nav__bag')).trim(), '| js errors:', errors.length ? errors : 'none');
  await b.close();
})().catch(e => { console.error(e); process.exit(1); });
