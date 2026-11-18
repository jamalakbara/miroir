import { readFile, writeFile } from 'node:fs/promises';
import { resolve } from 'node:path';
import { launch } from './cdp.mjs';

const root = resolve(import.meta.dirname, '../..');
const envText = await readFile(resolve(root, '.env'), 'utf8');
const env = Object.fromEntries(
  envText
    .split(/\r?\n/)
    .map((line) => line.match(/^([A-Za-z_][A-Za-z0-9_]*)=(.*)$/))
    .filter(Boolean)
    .map((match) => {
      let value = match[2].trim();
      if ((value.startsWith('"') && value.endsWith('"')) || (value.startsWith("'") && value.endsWith("'"))) {
        value = value.slice(1, -1);
      }
      return [match[1], value];
    }),
);

const user = env.WP_ADMIN_USER;
const password = env.WP_ADMIN_PASSWORD;
if (!user || !password) throw new Error('WP_ADMIN_USER and WP_ADMIN_PASSWORD are required in .env');

const browser = await launch(9393);
const { cdp, ev, sleep, close } = browser;

try {
  await cdp('Page.enable');
  await cdp('Emulation.setDeviceMetricsOverride', {
    width: 1440,
    height: 1400,
    deviceScaleFactor: 1,
    mobile: false,
  });

  await cdp('Page.navigate', { url: 'http://localhost:8080/wp-login.php' });
  await sleep(1200);
  await ev(`(() => {
    const login = document.querySelector('#user_login');
    const pass = document.querySelector('#user_pass');
    const form = document.querySelector('#loginform');
    if (!login || !pass || !form) throw new Error('WordPress login form not found');
    login.value = ${JSON.stringify(user)};
    pass.value = ${JSON.stringify(password)};
    form.submit();
    return true;
  })()`);
  await sleep(2200);

  await cdp('Page.navigate', {
    url: 'http://localhost:8080/wp-admin/admin.php?page=wc-settings&tab=miroir_wa',
  });
  await sleep(3200);

  await ev(`(() => {
    const style = document.createElement('style');
    style.textContent = [
      '#adminmenumain { display: none !important; }',
      '#wpcontent, #wpfooter { margin-left: 120px !important; }',
      '.notice, .updated, .error { display: none !important; }',
    ].join('\\n');
    document.head.append(style);

    const replacements = {
      miroir_wa_number: '62812…',
      miroir_wa_paypal: 'paypal@example.com',
    };
    for (const [id, value] of Object.entries(replacements)) {
      const input = document.querySelector('#' + id) || document.querySelector('[name="' + id + '"]');
      if (input) input.value = value;
    }
    document.activeElement?.blur();
    window.scrollTo(0, 0);
    return document.title;
  })()`);
  await sleep(500);

  const shot = await cdp('Page.captureScreenshot', {
    format: 'png',
    captureBeyondViewport: true,
    clip: { x: 0, y: 0, width: 1440, height: 1400, scale: 1 },
  });
  await writeFile(
    resolve(root, 'docs/design/miroir-assets/manual-guide/shots/wa-settings.png'),
    Buffer.from(shot.data, 'base64'),
  );
} finally {
  close();
}
