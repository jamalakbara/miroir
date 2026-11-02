const { chromium } = require('playwright-core');
const fs = require('fs');
const path = require('path');
const assert = require('node:assert/strict');
const output = path.join(__dirname, 'out/hifi');
fs.mkdirSync(output, { recursive: true });
let browser;
(async () => {
  browser = await chromium.launch({ channel: 'msedge' });
  const results = [];
  for (const width of [1440, 768, 390]) {
    const ctx = await browser.newContext({ viewport: { width, height: 900 }, reducedMotion: 'reduce' });
    const page = await ctx.newPage();
    const errors = [];
    page.on('pageerror', e => errors.push(e.message));
    for (const [name, url] of [['home', '/'], ['shop', '/shop-2/'], ['product', '/product/vibe-stripe-white/']]) {
      const response = await page.goto('http://localhost:8080' + url, { waitUntil: 'networkidle' });
      assert.equal(response.status(), 200, `${width} ${name} status`);
      await page.evaluate(async () => {
        document.querySelectorAll('img').forEach(i => i.loading = 'eager');
        await document.fonts.ready;
        await Promise.all(Array.from(document.images, i => i.decode().catch(() => {})));
      });
      const data = await page.evaluate(() => ({
        overflow: document.documentElement.scrollWidth > innerWidth,
        failedImages: [...document.images].filter(i => !i.naturalWidth).map(i => i.src),
        font: getComputedStyle(document.body).fontFamily,
        avenirLoaded: document.fonts.check('13px Avenir'),
        related: [...document.querySelectorAll('.similar__grid > *')].filter(i => getComputedStyle(i).display !== 'none').length,
        photos: document.querySelectorAll('[data-pdp-slide]').length,
        footer: document.querySelector('.site-footer__copyright')?.textContent,
        bannerWidth: document.querySelector('.home-banner__image')?.getBoundingClientRect().width,
        columns: getComputedStyle(document.querySelector('.plp__grid') || document.body).gridTemplateColumns,
      }));
      assert.equal(data.overflow, false, `${width} ${name} page overflow`);
      assert.deepEqual(data.failedImages, [], `${width} ${name} broken photos`);
      assert.ok(data.font.includes('Avenir') && data.avenirLoaded, `${width} ${name} Avenir`);
      assert.ok(data.footer.includes('Website by jamalakbara.com'));
      if (name === 'home') {
        assert.equal(Math.round(data.bannerWidth), width === 1440 ? 800 : width === 768 ? 560 : 300);
        const track = page.locator('[data-carousel-track]').first();
        await page.locator('[data-carousel-next]').first().click();
        assert.ok(await track.evaluate(n => n.scrollLeft > 0), 'Home carousel next');
        await page.locator('[data-carousel-prev]').first().click();
      }
      if (name === 'shop') {
        assert.equal(data.columns.split(' ').length, width === 1440 ? 4 : width === 768 ? 3 : 2);
        await page.locator('[data-load-more]').scrollIntoViewIfNeeded();
        await page.waitForFunction(() => document.querySelectorAll('.plp__grid > .card').length === 18);
        assert.equal(await page.locator('.plp__grid > .card').count(), 18);
      }
      if (name === 'product') {
        assert.equal(data.related, width === 1440 ? 4 : width === 768 ? 3 : 2);
        assert.equal(data.photos, 5);
        if (width === 390) {
          const track = page.locator('[data-pdp-track]');
          await page.locator('[data-pdp-next]').click();
          await page.waitForFunction(() => document.querySelector('[data-pdp-counter]').textContent.startsWith('2 /'));
          await track.focus();
          await page.keyboard.press('ArrowRight');
          await page.waitForFunction(() => document.querySelector('[data-pdp-counter]').textContent.startsWith('3 /'));
          await track.evaluate(n => n.scrollLeft = n.scrollWidth);
          await page.waitForFunction(() => document.querySelector('[data-pdp-next]').disabled);
          await page.locator('[data-zoom]').click();
          assert.ok(await page.locator('.pdp-lightbox').isVisible());
          await page.keyboard.press('Escape');
          await page.locator('.pdp-lightbox').waitFor({ state: 'detached' });
          assert.equal(await page.locator('.pdp-lightbox').count(), 0);
          await track.evaluate(n => n.scrollLeft = 0);
        }
      }
      await page.screenshot({ path: path.join(output, `${name}-${width}.png`), fullPage: true });
      results.push({ width, name, ...data });
    }
    assert.deepEqual(errors, [], `${width} JS errors`);
    await ctx.close();
  }
  await browser.close();
  fs.writeFileSync(path.join(output, 'results.json'), JSON.stringify(results, null, 2));
  console.log('PASS: nine responsive pages, images, Avenir, grids, carousels, zoom, catalog pagination and footer.');
})().catch(async e => { console.error(e); await browser?.close(); process.exitCode = 1; });
