// node diff.js <pen id> <site name>  -> diffs out/design/<id>.png with out/site/<name>.png
const fs = require('fs'), path = require('path'); const { PNG } = require('pngjs'); const pixelmatch = require('pixelmatch');
const root = process.env.VDIFF_DIR || path.join(__dirname, 'out');
function diff(id, name) {
  const A = PNG.sync.read(fs.readFileSync(path.join(root, 'design', id + '.png'))), B = PNG.sync.read(fs.readFileSync(path.join(root, 'site', name + '.png')));
  const w = Math.min(A.width, B.width), h = Math.min(A.height, B.height);
  const crop = (P) => { const o = new PNG({ width: w, height: h }); PNG.bitblt(P, o, 0, 0, w, h, 0, 0); return o; };
  const d = new PNG({ width: w, height: h }); const n = pixelmatch(crop(A).data, crop(B).data, d.data, w, h, { threshold: 0.1 });
  fs.mkdirSync(path.join(root, 'diff'), { recursive: true }); fs.writeFileSync(path.join(root, 'diff', name + '.png'), PNG.sync.write(d));
  console.log(`${name.padEnd(22)} design ${A.width}x${A.height}  site ${B.width}x${B.height}  diff ${(100 * n / (w * h)).toFixed(2)}%`);
}
module.exports = diff;
if (require.main === module) diff(process.argv[2], process.argv[3]);
