// node zoom.js <designId> <siteName> x y w h [scale]  -> zoom/<siteName>.png : design (top) / site (bottom)
const fs = require('fs'), path = require('path'); const { PNG } = require('pngjs');
const root = process.env.VDIFF_DIR || path.join(__dirname, 'out'); const [id, name, x, y, w, h, sc] = process.argv.slice(2); const k = +(sc || 4);
const A = PNG.sync.read(fs.readFileSync(path.join(root, 'design', id + '.png'))), B = PNG.sync.read(fs.readFileSync(path.join(root, 'site', name + '.png')));
const W = +w * k, H = +h * k, out = new PNG({ width: W, height: H * 2 + 4 });
out.data.fill(255);
[[A, 0], [B, H + 4]].forEach(([P, oy]) => { for (let j = 0; j < H; j++) for (let i = 0; i < W; i++) {
  const sx = +x + Math.floor(i / k), sy = +y + Math.floor(j / k); const o = ((oy + j) * W + i) * 4;
  if (sx < P.width && sy < P.height) { const s = (sy * P.width + sx) * 4; out.data[o] = P.data[s]; out.data[o + 1] = P.data[s + 1]; out.data[o + 2] = P.data[s + 2]; out.data[o + 3] = 255; } } });
for (let i = 0; i < W; i++) { const o = ((H + 1) * W + i) * 4; out.data[o] = 255; out.data[o + 1] = 0; out.data[o + 2] = 0; }
fs.mkdirSync(path.join(root, 'zoom'), { recursive: true }); fs.writeFileSync(path.join(root, 'zoom', name + '.png'), PNG.sync.write(out));
