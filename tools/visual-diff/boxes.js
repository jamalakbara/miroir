// node boxes.js <url> <width> <selector>  -> prints box of each descendant element (relative to the selector root)
const { chromium } = require('playwright-core');
(async () => {
  const [url, width, sel] = process.argv.slice(2);
  const b = await chromium.launch({ channel: 'msedge', args: ['--disable-lcd-text', '--font-render-hinting=none'] });
  const p = await b.newPage({ viewport: { width: +width, height: 900 } });
  await p.goto(url, { waitUntil: 'networkidle' }); await p.evaluate(() => document.fonts.ready);
  const rows = await p.evaluate((sel) => { const r = document.querySelector(sel); const o = r.getBoundingClientRect(); const out = [];
    const walk = (el, d) => { const q = el.getBoundingClientRect(); const t = el.children.length ? '' : (el.textContent || '').trim().slice(0, 30);
      out.push(`${'  '.repeat(d)}${el.tagName.toLowerCase()}.${[...el.classList].join('.')} ${(q.x - o.x).toFixed(1)},${(q.y - o.y).toFixed(1)} ${q.width.toFixed(1)}x${q.height.toFixed(1)} ${t}`);
      if (d < 5) [...el.children].forEach(c => walk(c, d + 1)); };
    walk(r, 0); return out; }, sel);
  console.log(rows.join('\n')); await b.close();
})();
