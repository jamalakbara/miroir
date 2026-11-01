// Functional check of account flows: register (validation + success), lost password, address save, details save.
const { chromium } = require('playwright-core');
const base = 'http://localhost:8080';
const notices = async p => (await p.locator('.woocommerce-error li, .woocommerce-message, .woocommerce-info').allTextContents()).map(t => t.replace(/\s+/g, ' ').trim()).filter(Boolean).join(' | ');
(async () => {
  const b = await chromium.launch({ channel: 'msedge' }); const p = await b.newPage();
  const errors = []; p.on('pageerror', e => errors.push(e.message));
  const email = `reg${Date.now()}@example.test`;

  await p.goto(base + '/my-account/?action=register', { waitUntil: 'networkidle' });
  await p.fill('#reg_first_name', 'Test'); await p.fill('#reg_last_name', 'Shopper'); await p.fill('#reg_email', email); await p.fill('#reg_password', 'short');
  await Promise.all([p.waitForNavigation(), p.click('button[name=register]')]);
  console.log('register invalid ->', await notices(p), '| still on form:', await p.locator('#reg_first_name').count() === 1);
  await p.fill('#reg_first_name', 'Test'); await p.fill('#reg_last_name', 'Shopper'); await p.fill('#reg_email', email); await p.fill('#reg_password', 'a-long-local-test-pass');
  await Promise.all([p.waitForNavigation(), p.click('button[name=register]')]);
  console.log('register ok -> url', p.url().replace(base, ''), '| greeting:', (await p.locator('.flow__intro').first().textContent()).trim());

  await p.goto(base + '/my-account/edit-address/shipping/', { waitUntil: 'networkidle' });
  for (const [k, v] of [['shipping_first_name', 'Test'], ['shipping_last_name', 'Shopper'], ['shipping_address_1', 'Jl. Contoh 1'], ['shipping_city', 'Jakarta'], ['shipping_postcode', '12345'], ['shipping_phone', '0812000000']]) await p.fill('#' + k, v);
  await p.selectOption('#shipping_state', 'JK');
  await Promise.all([p.waitForNavigation(), p.click('button[name=save_address]')]);
  console.log('address save ->', await notices(p), '| list shows:', (await p.locator('.flow__section .flow__copy').nth(1).innerText()).replace(/\n/g, ', '));

  await p.goto(base + '/my-account/edit-account/', { waitUntil: 'networkidle' });
  await p.fill('#account_display_name', 'Test S.');
  await Promise.all([p.waitForNavigation(), p.click('button[name=save_account_details]')]);
  console.log('details save ->', await notices(p));

  await p.goto(base + '/my-account/customer-logout/?_wpnonce=' + (await p.getAttribute('a[href*="customer-logout"]', 'href')).split('_wpnonce=')[1], { waitUntil: 'networkidle' });
  await p.goto(base + '/my-account/lost-password/', { waitUntil: 'networkidle' });
  await p.fill('#user_login', email);
  await Promise.all([p.waitForNavigation(), p.click('.lost_reset_password button[type=submit]')]);
  console.log('lost password ->', p.url().replace(base, ''), '|', await notices(p), '| title:', await p.textContent('.flow__title'));
  console.log('js errors:', errors.length ? errors : 'none');
  await b.close();
})().catch(e => { console.error(e); process.exit(1); });
