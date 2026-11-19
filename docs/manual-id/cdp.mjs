import { spawn } from 'node:child_process';
import { tmpdir } from 'node:os';

export async function launch(port = 9344) {
  const chrome = spawn('C:/Program Files/Google/Chrome/Application/chrome.exe', [
    '--headless=new', '--disable-gpu', '--hide-scrollbars', '--allow-file-access-from-files',
    `--remote-debugging-port=${port}`, `--user-data-dir=${tmpdir()}/miroir-manual-id-${port}-${process.pid}`, 'about:blank'], { stdio: 'ignore' });
  const sleep = ms => new Promise(r => setTimeout(r, ms));
  let target;
  for (let i = 0; i < 200 && !target; i++) { await sleep(200); try { target = (await (await fetch(`http://127.0.0.1:${port}/json/list`)).json()).find(t => t.type === 'page'); } catch {} }
  const ws = new WebSocket(target.webSocketDebuggerUrl);
  await new Promise(r => ws.onopen = r);
  let id = 0; const pend = new Map();
  ws.onmessage = e => { const m = JSON.parse(e.data); if (m.id && pend.has(m.id)) { pend.get(m.id)(m); pend.delete(m.id); } };
  const cdp = (method, params = {}) => new Promise(r => { const i = ++id; pend.set(i, r); ws.send(JSON.stringify({ id: i, method, params })); }).then(m => { if (m.error) throw new Error(method + ' ' + JSON.stringify(m.error)); return m.result; });
  const ev = async expr => { const r = await cdp('Runtime.evaluate', { expression: expr, awaitPromise: true, returnByValue: true }); if (r.exceptionDetails) throw new Error('eval: ' + JSON.stringify(r.exceptionDetails).slice(0, 400)); return r.result.value; };
  const close = () => { ws.close(); chrome.kill(); };
  return { cdp, ev, sleep, close };
}
