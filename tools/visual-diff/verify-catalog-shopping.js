const { chromium } = require('playwright-core');
const assert = require('node:assert/strict');
(async () => {
  const browser = await chromium.launch({ channel: 'msedge' });
  try {
    const page = await browser.newPage({ viewport: { width: 390, height: 900 }, reducedMotion: 'reduce' });
    await page.goto('http://localhost:8080/shop-2/?filter_color=green', { waitUntil: 'networkidle' });
    const names = await page.locator('.plp__grid .card__name').allTextContents();
    assert.equal(names.length, 4, 'Four green products');
    assert.ok(names.every(n => n.includes('Green')));
    await page.goto('http://localhost:8080/product/vibe-stripe-white/', { waitUntil: 'networkidle' });
    await page.locator('[data-qty-step="1"]').click();
    await page.locator('[data-qty-step="1"]').click();
    await page.locator('.single_add_to_cart_button').click();
    await page.waitForFunction(() => document.querySelector('#added-panel[open]'));
    assert.ok((await page.locator('#added-panel').innerText()).includes('Vibe Stripe White'));
    const quantities = await page.locator('#added-panel input[type="number"]').inputValue();
    assert.equal(quantities, '3');
    await page.keyboard.press('Escape');
    await page.goto('http://localhost:8080/cart/', { waitUntil: 'networkidle' });
    const text = await page.locator('main').innerText();
    assert.ok(text.includes('Vibe Stripe White'));
    assert.ok(text.includes('975,000'), 'Three products at IDR 325,000');
    console.log('PASS: real colour filters, quantity 3, AJAX bag drawer, persisted cart and IDR 975,000 subtotal.');
  } finally { await browser.close(); }
})().catch(e => { console.error(e); process.exitCode = 1; });

