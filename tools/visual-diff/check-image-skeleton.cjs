// Isolated browser checks using the theme's actual CSS and JavaScript.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { chromium } = require('playwright-core');
const theme = path.resolve(__dirname, '../../wp-content/themes/miroir-theme');
const css = fs.readFileSync(path.join(theme, 'assets/css/components.css'), 'utf8');
const script = fs.readFileSync(path.join(theme, 'assets/js/theme.js'), 'utf8');
const photo = '<svg xmlns="http://www.w3.org/2000/svg" width="30" height="40"><rect width="30" height="40" fill="#928777"/></svg>';
const media = (id, src, extra = '') => `<div id="${id}" class="ph ph--filled"><svg class="ph__icon ph__fallback" width="28" height="28" aria-hidden="true"></svg><img alt="Linen shirt" src="${src}">${extra}</div>`;

(async () => {
  const browser = await chromium.launch({ channel: 'msedge' });
  try {
    for (const width of [1440, 768, 375]) {
      const page = await browser.newPage({ viewport: { width, height: 900 } });
      const errors = [];
      page.on('pageerror', error => errors.push(error.message));
      const requests = new Map();
      await page.route('http://skeleton.test/**', route => { requests.set(new URL(route.request().url()).pathname, route); });
      await page.setContent(`<style>:root{--ph:#ededed;--ph-ink:#b8b8b8}body{margin:24px}.ph{width:min(240px,100%);aspect-ratio:3/4}${css}</style><div id="static" class="ph"></div>${media('slow', 'http://skeleton.test/slow.svg', '<img class="ph__hover" aria-hidden="true" alt="" src="http://skeleton.test/hover.svg">')}${media('broken', 'http://skeleton.test/broken.svg')}`, { waitUntil: 'domcontentloaded' });
      await page.addScriptTag({ content: script });
      await page.waitForFunction(() => document.querySelector('#slow').getAttribute('aria-busy') === 'true');
      const before = await page.locator('#slow').boundingBox();
      assert.equal(await page.locator('#static').getAttribute('aria-busy'), null);
      assert.equal(await page.locator('#slow img').first().evaluate(el => getComputedStyle(el).visibility), 'hidden');
      assert.equal(await page.locator('#slow').evaluate(el => getComputedStyle(el, '::after').animationName), 'image-skeleton');
      await page.emulateMedia({ reducedMotion: 'reduce' });
      assert.equal(await page.locator('#slow').evaluate(el => getComputedStyle(el, '::after').animationName), 'none');
      await page.emulateMedia({ reducedMotion: 'no-preference' });
      await requests.get('/slow.svg').fulfill({ contentType: 'image/svg+xml', body: photo });
      await page.waitForFunction(() => !document.querySelector('#slow').hasAttribute('aria-busy'));
      assert.deepEqual(await page.locator('#slow').boundingBox(), before);
      assert.equal(await page.locator('#slow img').first().evaluate(el => getComputedStyle(el).visibility), 'visible');
      await requests.get('/hover.svg').abort();
      await page.waitForFunction(() => document.querySelector('.ph__hover').classList.contains('has-image-error'));
      assert.equal(await page.locator('#slow').getAttribute('class'), 'ph ph--filled');
      await requests.get('/broken.svg').abort();
      await page.waitForFunction(() => document.querySelector('#broken').classList.contains('has-image-error'));
      assert.equal(await page.locator('#broken .ph__fallback').evaluate(el => getComputedStyle(el).display), 'block');
      assert.equal(await page.locator('#broken').getAttribute('aria-busy'), null);
      await page.evaluate(html => document.body.insertAdjacentHTML('beforeend', html), media('dynamic', 'http://skeleton.test/dynamic.svg'));
      await page.waitForFunction(() => document.querySelector('#dynamic').getAttribute('aria-busy') === 'true');
      await requests.get('/dynamic.svg').fulfill({ contentType: 'image/svg+xml', body: photo });
      await page.waitForFunction(() => !document.querySelector('#dynamic').hasAttribute('aria-busy'));
      const cached = `data:image/svg+xml,${encodeURIComponent(photo)}`;
      await page.evaluate(async ({ html, src }) => {
        const preload = new Image(); preload.src = src; await preload.decode();
        document.body.insertAdjacentHTML('beforeend', html);
      }, { html: media('cached', cached), src: cached });
      await page.waitForFunction(() => document.querySelector('#cached img').complete);
      assert.equal(await page.locator('#cached').getAttribute('aria-busy'), null);
      assert.deepEqual(errors, []);
      console.log(`PASS ${width}px: pending, loaded, no layout shift, error fallback, hover failure, dynamic insertion, cached photo, reduced motion`);
      await page.close();
    }
    const page = await browser.newPage({ javaScriptEnabled: false });
    await page.setContent(`<style>${css}</style>${media('no-js', `data:image/svg+xml,${encodeURIComponent(photo)}`)}`);
    assert.equal(await page.locator('#no-js img').evaluate(el => getComputedStyle(el).visibility), 'visible');
    console.log('PASS JavaScript disabled: photo remains visible');
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
