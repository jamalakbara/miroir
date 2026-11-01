// node secs.js <url> <width> [selector]  -> y+height of each direct child of <main> plus header/footer
const { chromium } = require('playwright-core');
(async () => {
  const [url, width, sel] = process.argv.slice(2);
  const b = await chromium.launch({ channel: 'msedge' }); const p = await b.newPage({ viewport: { width: +width, height: 900 } });
  await p.goto(url, { waitUntil: 'networkidle' }); await p.evaluate(() => document.fonts.ready);
  console.log((await p.evaluate((sel) => [document.querySelector('.site-header'), ...document.querySelectorAll(sel || 'main > *'), document.querySelector('.site-footer')]
    .map(e => { const r = e.getBoundingClientRect(); return `${(e.className || e.tagName).toString().split(' ')[0]}@${+(r.y + scrollY).toFixed(1)}+${+r.height.toFixed(1)}`; }), sel)).join('  '));
  await b.close();
})();
