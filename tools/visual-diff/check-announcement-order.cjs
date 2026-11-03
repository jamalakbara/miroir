// Dependency-free checks of global announcement ordering and native menu offsets.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const theme = path.resolve(__dirname, '../../wp-content/themes/miroir-theme');
const css = fs.readFileSync(path.join(theme, 'assets/css/header.css'), 'utf8');
const js = fs.readFileSync(path.join(theme, 'assets/js/theme.js'), 'utf8');
const template = fs.readFileSync(path.join(theme, 'parts/header.html'), 'utf8');
assert.match(css, /\.site-header \{[^}]*display: flex;[^}]*flex-direction: column;/);
assert.match(css, /\.announce \{[^}]*order: -1;/);
assert.doesNotMatch(css, /\.announce\s*\{[^}]*order:\s*1\s*;/);
assert.ok(template.indexOf('wp:miroir/announcement') < template.indexOf('wp:miroir/site-nav'));
console.log('PASS announcement precedes navbar globally, with no compact reorder');

const start = js.indexOf('\tfunction updateDrawerViewport()');
const end = js.indexOf("\n\twindow.addEventListener( 'resize', updateDrawerViewport );", start);
assert.ok(start > 0 && end > start);
const code = js.slice(start, end) + '\nupdateDrawerViewport();';
function offset({ kind = 'panel--drawer', announcement = { width: 390, height: 40, bottom: 40 }, toolbar = null } = {}) {
  const styles = new Map();
  const activeModal = { classList: { contains: name => name === kind }, style: { setProperty: (key, value) => styles.set(key, value) } };
  const document = {
    getElementById: () => toolbar && { getBoundingClientRect: () => toolbar },
    querySelector: () => announcement && { getBoundingClientRect: () => announcement },
  };
  vm.runInNewContext(code, { activeModal, document, window: { innerHeight: 844 } });
  return styles.get('--panel-toolbar-offset');
}
assert.equal(offset(), '40px');
assert.equal(offset({ toolbar: { width: 390, height: 32, bottom: 32 }, announcement: { width: 390, height: 40, bottom: 72 } }), '72px');
assert.equal(offset({ toolbar: { width: 390, height: 46, bottom: 46 }, announcement: { width: 390, height: 40, bottom: 86 } }), '86px');
assert.equal(offset({ announcement: { width: 390, height: 40, bottom: 18 } }), '18px');
assert.equal(offset({ announcement: { width: 390, height: 40, bottom: -10 } }), '0px');
assert.equal(offset({ announcement: null }), '0px');
assert.equal(offset({ announcement: { width: 0, height: 0, bottom: 40 } }), '0px');
assert.equal(offset({ kind: 'panel--bag', toolbar: { width: 390, height: 32, bottom: 32 } }), '32px');
console.log('PASS all-page menu offsets: top, admin bars, partially/fully scrolled and hidden announcement; bag unchanged');
