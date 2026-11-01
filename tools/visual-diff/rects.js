// node rects.js <url> <width> <sel1> <sel2> ...  -> absolute page rects (first match each)
// PRE=url1,url2 visits those first (e.g. add-to-cart links).
const { chromium } = require('playwright-core');
(async () => {
  const [url, width, ...sels] = process.argv.slice(2);
  const b = await chromium.launch({ channel: 'msedge' }); const p = await b.newPage({ viewport: { width: +width, height: 900 } });
  for (const u of (process.env.PRE || '').split(',').filter(Boolean)) await p.goto(u, { waitUntil: 'networkidle' });
  await p.goto(url, { waitUntil: 'networkidle' }); await p.evaluate(() => document.fonts.ready);
  const out = await p.evaluate((sels) => sels.map(s => { const e = document.querySelector(s); if (!e) return `${s}: -`;
    const r = e.getBoundingClientRect(); const f = v => Math.round(v * 10) / 10; return `${s}@${f(r.x)},${f(r.y + scrollY)} ${f(r.width)}x${f(r.height)}`; }), sels);
  console.log(out.join(' | ')); await b.close();
})();
