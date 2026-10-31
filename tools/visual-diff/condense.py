"""Condense a Pencil html-css export into an indented layout outline.

python condense.py <export.html> [--skip "Header,Footer,WhatsApp Float"] [--depth N]
Each line: name | layout props | text. Style values are the export's own CSS.
"""
import re, sys
from html.parser import HTMLParser

KEEP = ['flex-direction', 'gap', 'padding', 'width', 'height', 'justify-content', 'align-items', 'background-color',
        'border', 'border-bottom', 'border-top', 'border-right', 'border-left', 'outline', 'font-family', 'font-size', 'font-weight',
        'letter-spacing', 'line-height', 'color', 'text-align', 'position', 'left', 'top', 'flex', 'flex-grow', 'flex-wrap',
        'border-radius', 'border-width', 'border-color', 'outline-offset', 'opacity', 'text-decoration', 'max-width', 'min-width', 'align-self', 'box-shadow']
DEFAULTS = {'gap': '0px', 'justify-content': 'flex-start', 'align-items': 'flex-start', 'flex-direction': 'row',
            'letter-spacing': '0px', 'line-height': 'normal', 'text-align': 'left', 'height': 'fit-content', 'width': 'fit-content'}

args = sys.argv[1:]
skip = set('Header,Footer,WhatsApp Float'.split(','))
depth_max = 99
if '--skip' in args: skip = set(args[args.index('--skip') + 1].split(','))
if '--depth' in args: depth_max = int(args[args.index('--depth') + 1])

class P(HTMLParser):
    def __init__(s):
        super().__init__(); s.stack = []; s.out = []; s.skipping = None
    def handle_starttag(s, tag, attrs):
        a = dict(attrs)
        if tag in ('path', 'br', 'img', 'meta', 'link', 'circle', 'rect', 'line', 'ellipse'): 
            return
        s.stack.append(tag)
        d = len(s.stack)
        name = a.get('data-pencil-name')
        if s.skipping is not None: return
        if name in skip:
            s.skipping = d; s.out.append('  ' * d + f'[{name}]'); return
        if name is None or d > depth_max: return
        st = {}
        for part in (a.get('style') or '').split(';'):
            if ':' in part:
                k, v = part.split(':', 1); k = k.strip(); v = v.strip()
                if k in KEEP and DEFAULTS.get(k) != v: st[k] = v
        if tag == 'svg':
            st = {k: st[k] for k in ('width', 'height', 'left', 'top') if k in st}
            name += f" <svg {a.get('data-icon-name', '')}>"
        if 'font-family' in st: st['font-family'] = 'mono' if 'Mono' in st['font-family'] else 'sans'
        if st.get('width') == '100%': st['width'] = 'fill'
        s.out.append('  ' * d + name + ' | ' + ' '.join(f'{k}:{v}' for k, v in st.items()))
    def handle_endtag(s, tag):
        if tag in ('path', 'br', 'img', 'meta', 'link', 'circle', 'rect', 'line', 'ellipse'): return
        if s.stack:
            if s.skipping == len(s.stack): s.skipping = None
            s.stack.pop()
    def handle_data(s, data):
        t = ' '.join(data.split())
        if t and s.skipping is None and s.out and s.stack and s.stack[-1] == 'div': s.out[-1] += f'  "{t}"'

p = P(); p.feed(open(args[0], encoding='utf-8').read())
lines = [l for l in p.out if l.strip()]

# Collapse runs of sibling subtrees with identical layout (texts and names ignored): print the first, count the rest.
def ind(l): return len(l) - len(l.lstrip(' '))
def sig(block): return [str(ind(l)) + re.sub(r'  ".*"$', '', l.split(' | ', 1)[-1]) for l in block]
def texts(block): return [m.group(1) for l in block for m in [re.search(r'"(.*)"$', l)] if m]
def subtree(i):
    j = i + 1
    while j < len(lines) and ind(lines[j]) > ind(lines[i]): j += 1
    return j
out, i = [], 0
while i < len(lines):
    j = subtree(i); block = lines[i:j]; extra = []; k = j
    while len(block) > 1 and k < len(lines) and ind(lines[k]) == ind(lines[i]):
        m = subtree(k)
        if sig(lines[k:m]) != sig(block): break
        extra.append(texts(lines[k:m])); k = m
    if extra:
        out.extend(block)
        out.append(' ' * ind(lines[i]) + f'(+{len(extra)} more like above; texts: ' + ' / '.join(', '.join(t) for t in extra) + ')')
        i = k
    else:
        out.append(lines[i]); i += 1
print('\n'.join(out))
