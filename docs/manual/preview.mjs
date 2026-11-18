// node preview.mjs <outDir> [pages e.g. 1,2,5] — PNG preview of pages at 0.6 scale.
import { mkdirSync, writeFileSync } from 'node:fs';
import { launch } from './cdp.mjs';
const DIR = new URL('.', import.meta.url).pathname; const OUT = process.argv[2]; mkdirSync(OUT, { recursive: true });
const want = process.argv[3] ? process.argv[3].split(',').map(Number) : null;
const { cdp, ev, sleep, close } = await launch(9366);
await cdp('Page.enable'); await cdp('Emulation.setDeviceMetricsOverride', { width: 860, height: 1200, deviceScaleFactor: 1, mobile: false });
await cdp('Page.navigate', { url: 'file://' + DIR + 'manual-guide.html' }); await sleep(1500); await ev('document.fonts.ready.then(()=>true)');
const rects = await ev(`[...document.querySelectorAll('.page')].map(p=>{const b=p.getBoundingClientRect();return [b.left+scrollX,b.top+scrollY]})`);
for (const [i, [x, y]] of rects.entries()) { if (want && !want.includes(i + 1)) continue;
  const s = await cdp('Page.captureScreenshot', { format: 'png', captureBeyondViewport: true, clip: { x, y, width: 794, height: 1123, scale: 0.6 } });
  writeFileSync(`${OUT}/p${String(i + 1).padStart(2, '0')}.png`, Buffer.from(s.data, 'base64')); }
console.log('done', rects.length); close(); process.exit(0);
