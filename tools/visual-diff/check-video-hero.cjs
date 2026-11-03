// Dependency-free checks of the actual campaign-video initializer.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const source = fs.readFileSync(path.resolve(__dirname, '../../wp-content/themes/miroir-theme/assets/js/theme.js'), 'utf8');
const start = source.indexOf("\tdocument.querySelectorAll( '[data-video-hero]' )");
const end = source.indexOf('\n\t/* Keep native disclosure', start);
assert.ok(start > 0 && end > start);
const initializer = source.slice(start, end);

function harness({ reduced = false, mobile = false, saveData = false, blocked = false, missing = false, hidden = false } = {}) {
  const classes = new Set();
  const hero = { classList: {
    add: name => classes.add(name), remove: name => classes.delete(name),
    toggle: (name, enabled) => enabled ? classes.add(name) : classes.delete(name),
  }};
  const video = new EventTarget();
  Object.assign(video, { dataset: { src: '/campaign.mp4', mobileSrc: '/portrait.webm' }, paused: true, src: '', plays: 0 });
  video.play = () => {
    video.plays++;
    if (blocked) return Promise.reject(new Error('Autoplay blocked'));
    video.paused = false; video.dispatchEvent(new Event('playing')); return Promise.resolve();
  };
  video.pause = () => { video.paused = true; video.dispatchEvent(new Event('pause')); };
  video.load = () => {};
  video.removeAttribute = key => { if (key === 'src') video.src = ''; };
  hero.querySelector = selector => {
    assert.equal(selector, 'video', 'Playback must not depend on a play/pause button');
    return missing ? null : video;
  };
  const motion = new EventTarget(); motion.matches = reduced;
  const compact = new EventTarget(); compact.matches = mobile;
  const connection = new EventTarget(); connection.saveData = saveData;
  const document = new EventTarget(); document.hidden = hidden;
  document.querySelectorAll = () => [hero];
  let observer;
  class IntersectionObserver { constructor(callback) { observer = callback; } observe() {} }
  const window = { IntersectionObserver, matchMedia: query => query.includes('reduced-motion') ? motion : compact };
  vm.runInNewContext(initializer, { document, window, navigator: { connection }, IntersectionObserver });
  return { video, classes, document, motion, compact, connection, view: isIntersecting => observer([{ isIntersecting }]) };
}

(async () => {
  const a = harness();
  assert.equal(a.video.src, '/campaign.mp4');
  assert.equal(a.video.muted, true);
  assert.equal(a.video.paused, false);
  assert.equal(a.classes.has('has-video-frame'), true);
  a.view(false); assert.equal(a.video.paused, true);
  a.view(true); assert.equal(a.video.paused, false);
  a.document.hidden = true; a.document.dispatchEvent(new Event('visibilitychange'));
  assert.equal(a.video.paused, true);
  a.document.hidden = false; a.document.dispatchEvent(new Event('visibilitychange'));
  assert.equal(a.video.paused, false);
  console.log('PASS muted playback without controls and offscreen/tab suspension');

  a.compact.matches = true; a.compact.dispatchEvent(new Event('change'));
  assert.equal(a.video.src, '/portrait.webm');
  console.log('PASS responsive video source');

  for (const setting of [{ reduced: true }, { saveData: true }]) {
    const b = harness(setting);
    assert.equal(b.video.src, ''); assert.equal(b.video.plays, 0);
    assert.equal(b.classes.has('has-video-frame'), false);
  }
  a.motion.matches = true; a.motion.dispatchEvent(new Event('change'));
  assert.equal(a.video.src, ''); assert.equal(a.classes.has('has-video-frame'), false);
  a.motion.matches = false; a.motion.dispatchEvent(new Event('change'));
  assert.equal(a.video.paused, false);
  a.connection.saveData = true; a.connection.dispatchEvent(new Event('change'));
  assert.equal(a.video.src, ''); assert.equal(a.classes.has('has-video-frame'), false);
  console.log('PASS reduced-motion and data-saver poster-only fallback, including preference changes');

  const c = harness({ blocked: true });
  await Promise.resolve(); await Promise.resolve();
  assert.equal(c.video.paused, true); assert.equal(c.video.plays, 1);
  assert.equal(c.classes.has('has-video-frame'), false, 'Blocked autoplay keeps the poster visible');
  const d = harness(); d.video.dispatchEvent(new Event('error'));
  assert.equal(d.video.paused, true); assert.equal(d.classes.has('has-video-frame'), false);
  const plays = d.video.plays; d.view(false); d.view(true); assert.equal(d.video.plays, plays);
  harness({ missing: true });
  const e = harness({ hidden: true }); assert.equal(e.video.plays, 0);
  console.log('PASS blocked autoplay, video failure, missing video, hidden-tab initialization');
})().catch(error => { console.error(error); process.exitCode = 1; });
