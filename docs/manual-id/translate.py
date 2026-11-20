from lxml import html, etree
from pathlib import Path
from urllib.request import urlopen,Request
from urllib.parse import urlencode
from concurrent.futures import ThreadPoolExecutor, as_completed
import re,json,time,html as htmlmod
src=Path('docs/manual/content.html').read_text(encoding='utf8')
root=html.fragment_fromstring(src,create_parent='main')
nodes=root.xpath('.//p|.//th|.//h2|.//h3|.//li[not(descendant::p) and not(descendant::li)]')
entries=[]
for e in nodes:
 if 'sec__n' in (e.get('class') or ''): continue
 raw=(e.text or '')+''.join(etree.tostring(c,encoding='unicode',method='html',with_tail=True) for c in e)
 if not re.search('[A-Za-z]',re.sub('<[^>]+>','',raw)): continue
 pieces=[]
 def stash(m):
  pieces.append(m.group()); return f'QZP{len(pieces)-1:03d}QZ'
 # Preserve paths, UI labels and terms used by the real interface.
 protected=re.sub(r'<span\b[^>]*>.*?</span>|<a\b[^>]*>.*?</a>|<[^>]+>|\{[a-zA-Z_]+\}|%\d*\$?[sd]|https?://[^\s<]+|\b[\w.-]+@[\w.-]+\b|(?:[\w.-]+\.)+(?:id|com)(?:/[\w/-]*)?|(?<![\w])/[a-zA-Z][\w/-]*|\b(?:staging|live|WooCommerce|WordPress|Elementor|Miroir Currency|Miroir Legacy Redirects|Miroir WhatsApp Order|Shop manager|Administrator)\b',stash,raw,flags=re.S|re.I)
 entries.append((e,raw,protected,pieces))
print('entries',len(entries),flush=True)
def run_one(item):
 i,(_,raw,q,pieces)=item
 # Keep interface menu paths, role names and product names exactly as displayed.
 if ('→' in q and len(q)<115 and not re.search(r'\b(open|press|click|choose|check|change|go to|select|type|save|ask|send|enter)\b',q,re.I)) or q.strip() in {'Live','Staging','Local','Administrator','Shop manager','Editor','Customer','WordPress','WooCommerce','Hostinger hPanel','Miroir Legacy Redirects','Miroir WhatsApp Order','TikTok for Business'}:
  return i,q
 url='https://translate.googleapis.com/translate_a/single?'+urlencode({'client':'gtx','sl':'en','tl':'id','dt':'t','q':q})
 for attempt in range(6):
  try:
   with urlopen(Request(url,headers={'User-Agent':'Mozilla/5.0'}),timeout=30) as r: data=json.load(r)
   return i,''.join(x[0] for x in data[0] if x[0])
  except Exception:
   if attempt==5: raise
   time.sleep(1+attempt*2)
translations={}
with ThreadPoolExecutor(max_workers=16) as pool:
 for n,f in enumerate(as_completed([pool.submit(run_one,(i,e)) for i,e in enumerate(entries)]),1):
  i,v=f.result();translations[i]=v
  if n%100==0: print('translated',n,'/',len(entries),flush=True)
for i,(e,raw,protected,pieces) in enumerate(entries):
 s=translations[i]
 for j,p in enumerate(pieces):
  pat=r'QZP\s*0*'+str(j)+r'\s*QZ'
  s,n=re.subn(pat,lambda m:p,s,flags=re.I)
  if n!=1: raise ValueError(f'placeholder {i} {j} count {n}: {s}')
 if re.search(r'QZP\s*\d+',s,re.I):raise ValueError('extra placeholder')
 # Replace inner markup without changing node attributes.
 parsed=html.fragment_fromstring('<div>'+s+'</div>')
 e.text=parsed.text
 for c in list(e):e.remove(c)
 for c in list(parsed):e.append(c)
out='\n'.join(etree.tostring(c,encoding='unicode',method='html',with_tail=False) for c in root)
Path('docs/manual-id/content.html').write_text(out,encoding='utf8')
Path('docs/manual-id/translation-review.json').write_text(json.dumps([{'en':x[1],'id':translations[i]} for i,x in enumerate(entries)],ensure_ascii=False,indent=2),encoding='utf8')
print('saved',flush=True)
