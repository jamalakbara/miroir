// Navbar stays live while its thin hamburger morphs and the drawer slides below it.
const assert = require('node:assert/strict');
const { chromium } = require('playwright-core');
(async () => {
  const browser = await chromium.launch({ channel: 'msedge' });
  try {
    const page = await browser.newPage();
    const errors = [];
    page.on('pageerror', e => errors.push(e.message));
    const drawer = page.locator('#nav-panel');
    const toggle = page.locator('.site-nav__toggle');
    const content = page.locator('#nav-panel > .flow__col');
    async function close() {
      await page.keyboard.press('Escape');
      await drawer.waitFor({ state: 'hidden' });
      await page.waitForFunction(() => document.querySelector('.site-header .site-nav'));
    }
    async function open() {
      await page.evaluate(() => document.querySelector('.site-nav__toggle').click());
      await drawer.waitFor({ state: 'visible' });
    }
    for (const [width, height] of [[390, 844], [768, 1024], [1440, 900], [390, 300], [320, 568]]) {
      await page.setViewportSize({ width, height });
      await page.goto('http://localhost:8080/', { waitUntil: 'networkidle' });
      await page.evaluate(() => { window.originalToggle = document.querySelector('.site-nav__toggle'); });
      const before = width < 1024 ? await toggle.boundingBox() : null;
      await open();
      assert(await drawer.evaluate(el => el.matches(':modal')));
      assert(await toggle.evaluate(el => el === window.originalToggle));
      if (before) assert.deepEqual(await toggle.boundingBox(), before);
      assert.equal(await toggle.getAttribute('aria-expanded'), 'true');
      assert.equal(await toggle.getAttribute('aria-label'), 'Close menu');
      assert.equal(await page.locator('#nav-panel .flow__title, #nav-panel .panel__close').count(), 0);
      const rect = await drawer.boundingBox();
      const navRect = await page.locator('#nav-panel > .site-nav').boundingBox();
      const contentRect = await content.boundingBox();
      assert.equal(rect.y, 0); assert.equal(rect.height, height);
      assert.equal(contentRect.y, navRect.y + navRect.height);
      assert.equal(contentRect.height, height - navRect.height);
      assert.equal(await content.locator('svg, [data-panel-open="search"]').count(), 0);
      assert.equal(await content.locator('a').filter({ hasText: /^bag/ }).count(), 0);
      const currency = content.locator('.panel__currency');
      assert.equal((await currency.textContent()).trim(), 'IDR');
      assert.equal(await currency.evaluate(el => getComputedStyle(el).textDecorationLine), 'underline');
      if (height >= 568) {
        const currencyRect = await currency.boundingBox();
        assert.equal(currencyRect.y + currencyRect.height, height - 24);
      }
      if (width < 1024) {
        const searchRect = await page.locator('.site-nav__search-compact').boundingBox();
        const bagRect = await page.locator('.site-nav__bag').boundingBox();
        assert.equal(searchRect.width, 44); assert.equal(searchRect.height, 44);
        assert.equal(searchRect.x + searchRect.width, bagRect.x);
        assert.equal(await page.evaluate(() => document.documentElement.scrollWidth), width);
      }
      assert.deepEqual(await page.locator('.panel__links a').evaluateAll(els => els.map(el => [el.textContent, el.href])),
        await page.locator('.site-nav__menu a').evaluateAll(els => els.map(el => [el.textContent, el.href])));
      assert.equal(await content.evaluate(el => getComputedStyle(el).animationName), 'drawer-down');
      await page.waitForTimeout(320);
      assert.match(await content.evaluate(el => getComputedStyle(el).clipPath), /^inset\(0(?:px)?(?: 0(?:px|%)?)*\)$/);
      assert.equal(await toggle.locator('.site-nav__menu-mark > span').evaluate(el => getComputedStyle(el).opacity), '0');
      await page.mouse.move(width / 2, height - 50);
      await page.mouse.wheel(0, 400);
      await page.waitForTimeout(100);
      assert.equal(await page.evaluate(() => window.scrollY), 0);
      if (height === 300) assert(await content.evaluate(el => el.scrollTop > 0));
      await content.evaluate(el => { el.scrollTop = 0; });
      await page.screenshot({ path: `out/site/navigation-${width}-${height}.png` });
      await toggle.click();
      assert(await drawer.evaluate(el => el.classList.contains('is-closing')));
      assert.equal(await content.evaluate(el => getComputedStyle(el).animationName), 'drawer-up');
      await drawer.waitFor({ state: 'hidden' });
      await page.waitForFunction(() => document.querySelector('.site-header .site-nav'));
      assert.equal(await toggle.getAttribute('aria-expanded'), 'false');
      if (width < 1024) assert(await toggle.evaluate(el => el === document.activeElement));
      assert.equal(await page.locator('.site-nav').count(), 1);
      if (width < 1024) assert.deepEqual(await toggle.boundingBox(), before);
      for (const name of ['search', 'currency']) {
        await toggle.focus();
        await open();
        const opener = name === 'currency' ? '#nav-panel .panel__currency' : width < 1024 ? '#nav-panel .site-nav__search-compact' : '#nav-panel .site-nav__search';
        await page.locator(opener).click();
        assert(!(await drawer.isVisible()));
        assert(await page.locator(`#${name}-panel`).evaluate(el => el.matches(':modal')));
        assert.equal(await page.locator('.site-header .site-nav').count(), 1);
        await page.keyboard.press('Escape');
        await page.locator(`#${name}-panel`).waitFor({ state: 'hidden' });
        if (width < 1024) assert(await toggle.evaluate(el => el === document.activeElement));
      }
      await open();
      await page.locator('#nav-panel .site-nav__logo').focus();
      await page.keyboard.press('Shift+Tab');
      assert(await drawer.evaluate(el => el.contains(document.activeElement)));
      await close();
      if (width < 1024) {
        await page.locator('.site-nav__search-compact').click();
        assert(await page.locator('#search-panel').evaluate(el => el.matches(':modal')));
        assert(!(await drawer.isVisible()));
        await page.keyboard.press('Escape');
        await page.locator('#search-panel').waitFor({ state: 'hidden' });
        assert(await page.locator('.site-nav__search-compact').evaluate(el => el === document.activeElement));
      }
      console.log(`PASS persistent navbar + drawer ${width}x${height}`);
    }
    // Logged-in layout fixture uses WordPress's actual toolbar CSS and margin rules.
    await page.goto('http://localhost:8080/', { waitUntil: 'networkidle' });
    await page.addStyleTag({ url: '/wp-includes/css/admin-bar.min.css' });
    await page.addStyleTag({ content: 'html { margin-top: 32px !important; } @media (max-width: 782px) { html { margin-top: 46px !important; } }' });
    await page.evaluate(() => {
      const bar = document.createElement('div'); bar.id = 'wpadminbar';
      bar.innerHTML = '<div class="quicklinks">WordPress toolbar</div>';
      document.body.appendChild(bar);
    });
    for (const [width, height] of [[390, 844], [768, 1024], [1440, 900]]) {
      await page.setViewportSize({ width, height });
      for (const scroll of [0, 20, 300]) {
        await page.evaluate(y => window.scrollTo(0, y), scroll);
        await page.waitForTimeout(100);
        const locked = await page.evaluate(() => window.scrollY);
        const offset = await page.locator('#wpadminbar').evaluate(el => Math.max(0, el.getBoundingClientRect().bottom));
        const before = width < 1024 && scroll === 0 ? await toggle.boundingBox() : null;
        await open();
        const rect = await drawer.boundingBox();
        assert.equal(rect.y, offset); assert.equal(rect.height, height - offset);
        if (before) assert.deepEqual(await toggle.boundingBox(), before);
        await page.waitForTimeout(300);
        if (scroll === 0) await page.screenshot({ path: `out/site/navigation-toolbar-${width}.png` });
        await close();
        assert.equal(await page.evaluate(() => window.scrollY), locked);
      }
      console.log(`PASS toolbar and scroll ${width}`);
    }
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto('http://localhost:8080/?panel=nav', { waitUntil: 'networkidle' });
    assert(await drawer.evaluate(el => el.matches(':modal')));
    await close(); assert(!page.url().includes('panel='));
    await page.emulateMedia({ reducedMotion: 'reduce' });
    await open();
    assert.equal(await content.evaluate(el => getComputedStyle(el).animationName), 'none');
    await close();
    assert.deepEqual(errors, []);
    console.log('PASS URL fallback, focus trapping and reduced motion');
  } finally { await browser.close(); }
})().catch(e => { console.error(e); process.exitCode = 1; });
