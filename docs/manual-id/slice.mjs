import { fileURLToPath } from 'node:url';
// Slices manual-guide.html into one image per block (2×) for docs/design/miroir.pen,
// and writes manual-guide.layout.json with each page's blocks. Run: node docs/manual/slice.mjs
import { writeFileSync, mkdirSync, rmSync } from 'node:fs';
import { launch } from './cdp.mjs';

const DIR = fileURLToPath(new URL('.', import.meta.url)).replaceAll('\\','/');
const OUT = DIR + '../design/miroir-assets/manual-guide-id/pages';
rmSync(OUT, { recursive: true, force: true }); mkdirSync(OUT, { recursive: true });

const { cdp, ev, sleep, close } = await launch(9377);
await cdp('Page.enable');
await cdp('Emulation.setDeviceMetricsOverride', { width: 860, height: 1200, deviceScaleFactor: 1, mobile: false });
await cdp('Page.navigate', { url: 'file:///' + DIR + 'manual-guide.html' });
await sleep(1500); await ev('document.fonts.ready.then(()=>true)');
await ev(`(()=>{const s=document.createElement('style');s.textContent='body,.sheets,.page{background:transparent!important;box-shadow:none!important}';document.head.append(s);return true})()`);
await cdp('Emulation.setDefaultBackgroundColorOverride', { color: { r: 0, g: 0, b: 0, a: 0 } });
await sleep(200);

const pages = await ev(`(() => {
  const txt = el => (el ? el.textContent.replace(/\\s+/g, ' ').trim() : '');
  const name = el => {
    const c = el.classList;
    if (el.matches('header.mast')) return 'Masthead';
    if (el.matches('footer.foot')) return 'Footer';
    if (c.contains('sec')) return 'Section ' + txt(el.querySelector('.sec__n')) + ' / ' + txt(el.querySelector('h2')).replace(/\\.$/, '');
    if (c.contains('sub')) return 'Heading ' + txt(el.querySelector('.sec__n')) + ' / ' + txt(el.querySelector('h3'));
    const fig = el.matches('figure.shot') ? el : el.querySelector('figure.shot');
    if (fig) return 'Screenshot / ' + (fig.dataset.file || '').split('/').pop().replace(/-\\d+\\.png$/, '') + (el.querySelector('.cap') ? ' — ' + txt(el.querySelector('.cap')).slice(0, 40) : '');
    if (el.matches('table')) return 'Table / ' + txt(el.querySelector('tbody td')).slice(0, 40);
    if (c.contains('steps')) return 'Steps / ' + txt(el.querySelector('li')).replace(/^\\d+\\s*/, '').slice(0, 40);
    if (c.contains('note')) return 'Note / ' + txt(el.querySelector('.label'));
    if (c.contains('task')) return 'Task / ' + txt(el.querySelector('h3'));
    if (c.contains('legend')) return 'Screenshot legend';
    if (c.contains('dodont')) return 'Do and avoid';
    if (c.contains('bullets')) return 'List / ' + txt(el.querySelector('li')).slice(0, 40);
    if (c.contains('band')) return 'At a glance';
    if (c.contains('cols')) return 'Client and application';
    if (c.contains('msg')) return 'WhatsApp message';
    if (c.contains('sign')) return 'Signatures';
    if (c.contains('gap-8')) return 'Contents';
    return 'Copy / ' + txt(el).slice(0, 40);
  };
  return [...document.querySelectorAll('.page')].map(pg => {
    const P = pg.getBoundingClientRect();
    const els = [...pg.querySelector('.page__body').children, pg.querySelector('.foot')];
    return { abs: { x: P.left + scrollX, y: P.top + scrollY }, blocks: els.map(el => { const r = el.getBoundingClientRect(); return { name: name(el), x: +(r.left - P.left).toFixed(1), y: +(r.top - P.top).toFixed(1), w: +r.width.toFixed(1), h: +r.height.toFixed(1) }; }) };
  });
})()`);

let n = 0;
for (const [pi, pg] of pages.entries()) {
  await ev(`(()=>{let a=[...document.querySelectorAll('.page')]; a.forEach((p,i)=>p.style.display=i===${pi}?'flex':'none'); window.scrollTo(0,0); return true})()`);
  const pos=await ev(`(()=>{let r=document.querySelectorAll('.page')[${pi}].getBoundingClientRect();return {x:r.x,y:r.y}})()`);
  for (const [bi, b] of pg.blocks.entries()) {
    const x = Math.floor(b.x - 2), y = Math.floor(b.y - 2), w = Math.ceil(b.x + b.w + 2) - x, h = Math.ceil(b.y + b.h + 2) - y;
    Object.assign(b, { x, y, w, h });
    const shot = await cdp('Page.captureScreenshot', { format: 'png', clip: { x: pos.x + x, y: pos.y + y, width: w, height: h, scale: 2 } });
    b.file = `miroir-assets/manual-guide-id/pages/p${String(pi + 1).padStart(2, '0')}-${String(bi + 1).padStart(2, '0')}.png`;
    writeFileSync(DIR + '../design/' + b.file, Buffer.from(shot.data, 'base64')); n++;
  }
  delete pg.abs;
  if ((pi+1)%10===0) console.log('rendered', pi+1, 'pages');
}
writeFileSync(DIR + 'manual-guide.layout.json', JSON.stringify(pages));
console.log('pages', pages.length, 'blocks', n);
close(); process.exit(0);
