// Dependency-free checks of the actual optional homepage-header initializer.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const js = fs.readFileSync(path.resolve(__dirname, '../../wp-content/themes/miroir-theme/assets/js/theme.js'), 'utf8');
const css = fs.readFileSync(path.resolve(__dirname, '../../wp-content/themes/miroir-theme/assets/css/header.css'), 'utf8');
const start = js.indexOf('\t/* Homepage-only transparent navigation');
const end = js.indexOf('\n\t/* Campaign films', start);
assert.ok(start > 0 && end > start);
const initializer = js.slice(start, end);

function harness({ overlay = true, editor = false, hero = true, announcement = true, scroll = 0, toolbar = null } = {}) {
  const classes = new Set(overlay ? ['home-header-overlay'] : []);
  if (editor) classes.add('elementor-editor-active');
  const styles = new Map();
  const body = { classList: { contains: n => classes.has(n), add: n => classes.add(n), toggle: (n, yes) => yes ? classes.add(n) : classes.delete(n) }, style: { setProperty: (n, value) => styles.set(n, value) } };
  const banner = { getBoundingClientRect: () => ({ height: 40 }) };
  const header = { querySelector: () => announcement ? banner : null, getBoundingClientRect: () => ({ height: 166 }) };
  const document = { body, documentElement: { style: body.style }, querySelector: selector => selector === '.site-header' ? header : hero ? {} : null, getElementById: () => toolbar && { getBoundingClientRect: () => toolbar } };
  const window = new EventTarget();
  let pending;
  Object.assign(window, { scrollY: scroll, innerHeight: 844, requestAnimationFrame: fn => { pending = fn; } });
  class ResizeObserver { constructor(fn) { this.callback = fn; } observe() {} }
  window.ResizeObserver = ResizeObserver;
  vm.runInNewContext(initializer, { window, document, ResizeObserver });
  return { classes, styles, window, flush: () => { const fn = pending; pending = null; if (fn) fn(); } };
}

const a = harness();
assert.ok(a.classes.has('home-header-ready'));
assert.equal(a.classes.has('home-header-solid'), false);
assert.equal(a.styles.get('--home-announcement-height'), '40px');
assert.equal(a.styles.get('--home-header-clearance'), '174px');
a.window.scrollY = 24; a.window.dispatchEvent(new Event('scroll')); a.flush();
assert.equal(a.classes.has('home-header-solid'), false);
console.log('PASS transparent top stays transparent while the page scrolls');

const b = harness({ scroll: 400, toolbar: { width: 390, height: 46, bottom: 46 } });
assert.equal(b.classes.has('home-header-solid'), false);
assert.equal(b.styles.get('--home-toolbar-offset'), '46px');
const c = harness({ toolbar: { width: 390, height: 46, bottom: -1 } });
assert.equal(c.styles.get('--home-toolbar-offset'), '0px');
assert.equal(harness({ announcement: false }).styles.get('--home-announcement-height'), '0px');
console.log('PASS restored scroll position, visible/hidden WordPress toolbar, disabled announcement');

for (const options of [{ overlay: false }, { hero: false }, { editor: true }]) {
  const d = harness(options);
  assert.equal(d.classes.has('home-header-ready'), false);
  assert.equal(d.styles.size, 0);
}
console.log('PASS solid option, missing-hero fallback and Elementor editor remain untouched');

assert.match(css, /\.home-header-overlay:not\(\.elementor-editor-active\):has\(\.home-hero\) \.site-header \{[^}]*position: absolute;/);
assert.doesNotMatch(css, /\.home-header-overlay:not\(\.elementor-editor-active\):has\(\.home-hero\) \.site-header \{[^}]*position: fixed;/);
assert.doesNotMatch(css, /\.home-header-overlay[^}]*\{[^}]*transition:/);
assert.doesNotMatch(initializer, /addEventListener\( 'scroll'/);
console.log('PASS header stays at the page top without paint transitions or scroll-state listeners');
