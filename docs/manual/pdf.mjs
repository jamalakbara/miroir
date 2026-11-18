// node docs/manual/pdf.mjs — prints manual-guide.html to miroir-manual-guide.pdf (A4, no margins).
import { writeFileSync } from 'node:fs';
import { launch } from './cdp.mjs';
const DIR = new URL('.', import.meta.url).pathname;
const { cdp, ev, sleep, close } = await launch(9388);
await cdp('Page.enable'); await cdp('Page.navigate', { url: 'file://' + DIR + 'manual-guide.html' });
await sleep(1500); await ev('document.fonts.ready.then(()=>true)');
const r = await cdp('Page.printToPDF', { printBackground: true, preferCSSPageSize: true, marginTop: 0, marginBottom: 0, marginLeft: 0, marginRight: 0 });
writeFileSync(DIR + 'miroir-manual-guide.pdf', Buffer.from(r.data, 'base64')); console.log('pdf ok'); close(); process.exit(0);
