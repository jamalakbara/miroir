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
        editorial: (() => {
          const row = document.querySelector('.home-editorial__row');
          if (!row) return null;
          const box = selector => row.parentElement.querySelector(selector).getBoundingClientRect().toJSON();
          const section = row.parentElement;
          return {
            row: row.getBoundingClientRect().toJSON(), large: box('.home-editorial__large'), small: box('.home-editorial__small'), caption: box('.home-caption'),
            section: section.getBoundingClientRect().toJSON(), gutter: parseFloat(getComputedStyle(section).paddingLeft),
            paddingTop: parseFloat(getComputedStyle(section).paddingTop), paddingBottom: parseFloat(getComputedStyle(section).paddingBottom),
            bannerCaption: document.querySelector('.home-banner > .home-caption').getBoundingClientRect().toJSON(),
            bannerStage: document.querySelector('.home-banner > a').getBoundingClientRect().toJSON(),
            bannerImage: document.querySelector('.home-banner__image').getBoundingClientRect().toJSON(),
          };
        })(),
        columns: getComputedStyle(document.querySelector('.plp__grid') || document.body).gridTemplateColumns,
      }));
      assert.equal(data.overflow, false, `${width} ${name} page overflow`);
      assert.deepEqual(data.failedImages, [], `${width} ${name} broken photos`);
      assert.ok(data.font.includes('Avenir') && data.avenirLoaded, `${width} ${name} Avenir`);
      assert.ok(data.footer.includes('Website by jamalakbara.com'));
      if (name === 'home') {
        const { row, large, small, caption, section, gutter, bannerCaption, bannerStage, bannerImage, paddingTop, paddingBottom } = data.editorial;
        const near = (actual, expected, label, tolerance = 1) => assert.ok(Math.abs(actual - expected) < tolerance, `${width} editorial ${label}: ${actual} vs ${expected}`);
        near(caption.x, section.x + gutter, 'caption page-gutter alignment');
        near(caption.width, section.width - gutter * 2, 'caption content width');
        near(caption.x, bannerCaption.x, 'caption aligns with sale banner');
        near(caption.y - row.bottom, 18, 'caption vertical spacing');
        const stageWidth = Math.min(width - gutter * 2, 1280);
        near(row.width, stageWidth, 'width-led campaign canvas');
        near(row.height, stageWidth * (width < 600 ? 7 / 5 : 5 / 8), 'campaign canvas proportions');
        near(bannerStage.width, stageWidth, 'matching banner canvas width');
        near(bannerStage.height, stageWidth * (width < 600 ? 3 / 2 : 5 / 8), 'banner canvas proportions');
        near(bannerImage.width, stageWidth * (width < 600 ? 1 : 0.625), 'banner artwork scale');
        near(bannerImage.width / bannerImage.height, 2049 / 1152, 'uncropped triptych ratio', 0.001);
        near(paddingTop, width < 600 ? 112 : 64, 'section leading space');
        near(paddingBottom, width < 600 ? 48 : 32, 'section trailing space');
        near(small.x + small.width / 2, width < 600 ? width / 2 : large.right + large.width / 2, 'small image center');
        near(small.width / small.height, 3 / 4, 'small image ratio', 0.001);
        if (width >= 600) {
          near(large.width / large.height, 4 / 5, 'dominant portrait ratio', 0.001);
          near(row.width, large.width * 2, 'bounded pair width');
          near(small.width, large.width / 2, 'small image scale');
          near(small.left - large.right, large.width / 4, 'proportional image gap');
          near(small.y + small.height / 2, large.y + large.height / 2, 'vertical center');
          near(row.x, (width - row.width) / 2, 'centered composition');
        } else {
          assert.equal(large.width, 0, 'Mobile retains the smaller image only');
          near(small.width, row.width * 2 / 3, 'mobile portrait scale');
          near(small.y + small.height / 2, row.y + row.height / 2, 'mobile portrait vertical center');
          near(bannerImage.y - bannerStage.y, bannerStage.width / 4, 'mobile triptych placement');
        }
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
