// Builds manual-guide.html (A4 pages) from content.html: crops screenshots, measures blocks in Chrome, paginates.
// Run: node docs/manual/build.mjs
import { readFileSync, writeFileSync, mkdirSync, existsSync } from 'node:fs';
import { launch } from './cdp.mjs';

const DIR = new URL('.', import.meta.url).pathname;
const SHOTS = DIR + '../design/miroir-assets/manual-guide/shots/';
const CROP = SHOTS + 'crop/'; mkdirSync(CROP, { recursive: true });
const W = 698;
const VERSION = '1.0', DATE = '18 November 2026', GUIDE = 'GUIDE-2026-001';

let content = readFileSync(DIR + 'content.html', 'utf8').replace(/<!--[\s\S]*?-->/g, '');

const { cdp, ev, sleep, close } = await launch(9355);
await cdp('Page.enable');
// 1. Crop screenshots for each figure (Chrome clip) and size the figure to the crop ratio.
let fi = 0; const jobs = [];
content = content.replace(/<figure class="shot" data-src="([^"]+)" data-crop="([^"]+)">/g, (m, src, crop) => {
  const [x, y, w, h] = crop.split(',').map(Number);
  const name = `${src}-${++fi}.png`; jobs.push({ src, x, y, w, h, name });
  const fh = Math.round(W * h / w);
  return `<figure class="shot shot--img" style="height:${fh}px" data-file="miroir-assets/manual-guide/shots/crop/${name}"><img class="shot__img" src="${CROP + name}" alt="">`;
});
for (const j of jobs) {
  await cdp('Emulation.setDeviceMetricsOverride', { width: 2000, height: 3000, deviceScaleFactor: 1, mobile: false });
  writeFileSync(DIR + '_crop.html', `<html><body style="margin:0"><img src="${SHOTS + j.src}.png" style="display:block"></body></html>`);
  await cdp('Page.navigate', { url: 'file://' + DIR + '_crop.html' }); await sleep(250);
  const s = await cdp('Page.captureScreenshot', { format: 'png', captureBeyondViewport: true, clip: { x: j.x, y: j.y, width: j.w, height: j.h, scale: 1 } });
  writeFileSync(CROP + j.name, Buffer.from(s.data, 'base64'));
}

const head = `<!doctype html><html lang="en"><head><meta charset="utf-8"><title>Manual Guide — Miroir — ${GUIDE}</title><link rel="stylesheet" href="manual-guide.css"></head><body><main class="sheets">`;
const STAMP = 'file:///Users/chairilakbar/Documents/projects/jamalakbara/public/brand/stamp.svg';
const mastTop = `<div class="mast__top"><div class="tab">JAMALAKBARA.</div><div class="mast__meta"><p>Design &amp; Development<br>Bandung, Indonesia</p><img class="stamp" src="${STAMP}" alt=""></div></div><hr class="rule">`;
const mastRun = `<header class="mast">${mastTop}<div class="running"><span>${GUIDE}</span><span>Miroir / miroir.id</span></div></header>`;
const foot = (n, total) => `<footer class="foot"><div class="foot__l"><p class="h-fact">Built to be used.</p><a href="mailto:hello-im@jamalakbara.com">hello-im@jamalakbara.com</a></div><div class="foot__r"><span>jamalakbara.com</span><span>Manual guide / ${String(n).padStart(2, '0')}—${String(total).padStart(2, '0')}</span></div></footer>`;

// 2. Measure every block.
await cdp('Emulation.setDeviceMetricsOverride', { width: 900, height: 2000, deviceScaleFactor: 1, mobile: false });
writeFileSync(DIR + '_measure.html', `${head}<section class="page" style="height:auto"><div class="page__body" id="m">${mastRun}${content}</div>${foot(1, 1)}</section></main></body></html>`);
await cdp('Page.navigate', { url: 'file://' + DIR + '_measure.html' });
await sleep(1200); await ev('document.fonts.ready.then(()=>true)');
const meas = await ev(`(()=>{const m=document.getElementById('m');const kids=[...m.children];return {blocks:kids.slice(1).map(k=>({h:k.getBoundingClientRect().height,html:k.outerHTML,split:(k.matches('table.table')?{head:k.querySelector('thead').getBoundingClientRect().height,open:k.outerHTML.split('<tbody>')[0]+'<tbody>',close:'</tbody></table>',items:[...k.querySelectorAll('tbody > tr')].map(r=>({h:r.getBoundingClientRect().height,html:r.outerHTML}))}:k.matches('ol.steps,ul.bullets')?{head:0,open:k.outerHTML.match(/^<[^>]+>/)[0],close:k.matches('ol')?'</ol>':'</ul>',gap:k.matches('ul.bullets')?8:0,items:[...k.children].map(r=>({h:r.getBoundingClientRect().height,html:r.outerHTML}))}:null),sec:k.classList.contains('sec'),kwn:k.classList.contains('kwn')||k.classList.contains('sub')})),mast:kids[0].getBoundingClientRect().height,foot:document.querySelector('.foot').getBoundingClientRect().height}})()`);

// 3. Paginate. Body height = 1123 - 40 - 32 - footer - 16 breathing room.
const AVAIL = 1123 - 40 - 32 - meas.foot - 16;
const GAP = 16;
const pages = []; let cur = null;
const open = () => { cur = { blocks: [], h: meas.mast }; pages.push(cur); };
const B = meas.blocks;
for (let i = 0; i < B.length; i++) {
  const b = B[i];
  let need = GAP + b.h;
  for (let j = i; B[j] && B[j].kwn && B[j + 1]; j++) need += GAP + (B[j + 1].split && !B[j + 1].kwn ? Math.min(B[j + 1].h, 180) : Math.min(B[j + 1].h, 520));
  if (!cur || b.sec) open();
  if (cur.h + need > AVAIL && b.split && !b.kwn) {
    const sp = b.split; const g = sp.gap || 0; let items = sp.items;
    while (items.length) {
      let room = AVAIL - cur.h - GAP - sp.head, take = 0, used = 0;
      while (take < items.length && used + items[take].h + (take ? g : 0) <= room) { used += items[take].h + (take ? g : 0); take++; }
      if (take === items.length) { cur.blocks.push({ html: sp.open + items.map(x => x.html).join('') + sp.close }); cur.h += GAP + sp.head + used + 2; break; }
      if (take >= 2) { cur.blocks.push({ html: sp.open + items.slice(0, take).map(x => x.html).join('') + sp.close }); cur.h += GAP + sp.head + used + 2; items = items.slice(take); }
      open();
    }
    continue;
  }
  if (cur.h + need > AVAIL && cur.blocks.length) open();
  if (b.h + meas.mast + GAP > AVAIL) console.log('TOO TALL block', i, Math.round(b.h), b.html.slice(0, 80));
  cur.blocks.push(b); cur.h += GAP + b.h;
}

// 4. Cover + chapter page numbers.
const total = pages.length + 1;
const chapters = [];
pages.forEach((p, pi) => { const s = p.blocks.find(b => b.sec); if (s) { const m = s.html.match(/sec__n">(\d+)<\/span><h2 class="h-sec">([^<]+)</); chapters.push([m[1], m[2].replace(/\.$/, '').replace(/&amp;/g, '&'), pi + 2]); } });
const half = Math.ceil(chapters.length / 2);
const tocOrder = []; for (let i = 0; i < half; i++) { tocOrder.push(chapters[i]); if (chapters[i + half]) tocOrder.push(chapters[i + half]); }
const toc = tocOrder.map(([n, t, p]) => `<li><span>${n}</span>${t.replace(/&/g, '&amp;')}<span class="muted" style="float:right">p. ${String(p).padStart(2, '0')}</span></li>`).join('');
const cover = `<section class="page"><div class="page__body">
<header class="mast">${mastTop}<div class="titleblock"><div class="titleblock__left"><h1 class="display">Manual<br>guide.</h1><p class="label">User manual / WordPress store</p></div>
<div class="docmeta"><div class="full"><p class="label">Guide no.</p><p class="v">${GUIDE}</p></div><div><p class="label">Version</p><p class="v">${VERSION}</p></div><div><p class="label">Last updated</p><p class="v">${DATE}</p></div></div></div></header>
<div class="cols"><div class="party"><p class="label">Prepared for</p><p class="h-name">Miroir</p><p class="small">miroir.id</p><p class="small">Prepared by: Jamal Akbar Alam</p><p class="small">UI/UX Designer &amp; Web Developer</p></div>
<div class="party"><p class="label">Application</p><p class="h-name">miroir.id store</p><p class="small">miroir.id/wp-admin</p><p class="small">Platform: WordPress + WooCommerce + Elementor</p><p class="small">Orders: WhatsApp, saved in WooCommerce</p></div></div>
<div class="band"><div><p class="label">Admin address</p><p class="h-fact">/wp-admin</p></div><div><p class="label">Who it's for</p><p class="h-fact">Admin, CS &amp; content</p></div><div><p class="label">Support until</p><p class="h-fact accent">20 December 2026</p></div></div>
<div class="gap-8"><p class="label">In this guide</p><ul class="toc">${toc}</ul></div>
<p class="small muted">Screens in this guide come from the development copy of the store with sample data. Live shows your real products and orders; labels and menus are the same.</p>
</div>${foot(1, total)}</section>`;

const body = pages.map((p, i) => `<section class="page"><div class="page__body">${mastRun}${p.blocks.map(b => b.html).join('\n')}</div>${foot(i + 2, total)}</section>`).join('\n');
writeFileSync(DIR + 'manual-guide.html', `${head}${cover}\n${body}</main></body></html>`);

// 5. Verify: no page body overflows.
await cdp('Page.navigate', { url: 'file://' + DIR + 'manual-guide.html' });
await sleep(1500); await ev('document.fonts.ready.then(()=>true)');
const over = await ev(`[...document.querySelectorAll('.page')].map((p,i)=>{const b=p.querySelector('.page__body'), f=p.querySelector('.foot'); return [i+1, Math.round(b.getBoundingClientRect().bottom - f.getBoundingClientRect().top)]}).filter(x=>x[1]>-8)`);
console.log('pages', total, 'avail', Math.round(AVAIL), 'overflow', JSON.stringify(over));
close(); process.exit(0);
