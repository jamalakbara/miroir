// node run.js <spec.json>   spec: [{name, design:"<pen id>", url, width, selector?, pre?: [urls visited first, e.g. add-to-cart], setup?: "<js to run before shot>"}]
// Captures each page in one Edge session and diffs against design/<pen id>.png.
const fs = require('fs'), path = require('path');
const { chromium } = require('playwright-core'); const { PNG } = require('pngjs'); const pixelmatch = require('pixelmatch');
const root = process.env.VDIFF_DIR || path.join(__dirname, 'out');
(async () => {
  const spec = JSON.parse(fs.readFileSync(process.argv[2], 'utf8'));
  const b = await chromium.launch({ channel: 'msedge', args: ['--disable-lcd-text', '--font-render-hinting=none'] });
  for (const s of spec) {
    const ctx = await b.newContext({ viewport: { width: s.width, height: s.height || 900 }, deviceScaleFactor: 1, storageState: s.state });
    const p = await ctx.newPage();
    for (const u of s.pre || []) await p.goto(u, { waitUntil: 'networkidle' });
    await p.goto(s.url, { waitUntil: 'networkidle' }); await p.evaluate(() => document.fonts.ready);
    await p.addStyleTag({ content: '.wa-float{display:none!important}#wpadminbar{display:none!important}html{margin-top:0!important}' });
    if (s.setup) { await p.evaluate(s.setup); await p.waitForTimeout(300); }
    const site = path.join(root, 'site', s.name + '.png'); fs.mkdirSync(path.dirname(site), { recursive: true });
    if (s.selector) await p.locator(s.selector).first().screenshot({ path: site }); else await p.screenshot({ path: site, fullPage: true });
    await ctx.close();
    const A = PNG.sync.read(fs.readFileSync(path.join(root, 'design', s.design + '.png'))), B = PNG.sync.read(fs.readFileSync(site));
    const w = Math.min(A.width, B.width), h = Math.min(A.height, B.height);
    const crop = (P) => { const o = new PNG({ width: w, height: h }); PNG.bitblt(P, o, 0, 0, w, h, 0, 0); return o; };
    const d = new PNG({ width: w, height: h });
    const n = pixelmatch(crop(A).data, crop(B).data, d.data, w, h, { threshold: 0.1 });
    fs.mkdirSync(path.join(root, 'diff'), { recursive: true }); fs.writeFileSync(path.join(root, 'diff', s.name + '.png'), PNG.sync.write(d));
    console.log(`${s.name.padEnd(22)} design ${A.width}x${A.height}  site ${B.width}x${B.height}  diff ${(100 * n / (w * h)).toFixed(2)}%`);
  }
  await b.close();
})().catch(e => { console.error(e); process.exit(1); });
