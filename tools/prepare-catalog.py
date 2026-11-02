"""Extract the supplied catalog workbook into an import manifest."""
import json, re, shutil, zipfile, xml.etree.ElementTree as ET, sys
from pathlib import Path

root = Path(__file__).resolve().parents[1]
source = Path(sys.argv[1])
dest = root / 'wp-content/uploads/miroir-catalog-source'
dest.mkdir(parents=True, exist_ok=True)
for f in source.glob('catalog-*.jpg'):
    shutil.copy2(f, dest / f.name)
for f in (root / 'docs/design/images/hifi').iterdir():
    if f.suffix in ('.jpg', '.webp'):
        shutil.copy2(f, dest / f.name)
z = zipfile.ZipFile(source / 'catalog-detail.xlsx')
ns = {'m': 'http://schemas.openxmlformats.org/spreadsheetml/2006/main'}
strings = [''.join(n.itertext()) for n in ET.fromstring(z.read('xl/sharedStrings.xml'))]
sheet = ET.fromstring(z.read('xl/worksheets/sheet1.xml'))
dns = {'x': 'http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing', 'a': 'http://schemas.openxmlformats.org/drawingml/2006/main', 'r': 'http://schemas.openxmlformats.org/officeDocument/2006/relationships'}
rels = {n.attrib['Id']: n.attrib['Target'].replace('../', 'xl/') for n in ET.fromstring(z.read('xl/drawings/_rels/drawing1.xml.rels'))}
references = {}
for anchor in ET.fromstring(z.read('xl/drawings/drawing1.xml')):
    row = int(anchor.find('x:from/x:row', dns).text) + 1
    rid = anchor.find('.//a:blip', dns).attrib['{' + dns['r'] + '}embed']
    filename = f'workbook-row-{row}.jpg'
    (dest / filename).write_bytes(z.read(rels[rid]))
    references[row] = filename
groups = [[59,60,61,62,63], [64,65,66,67,68], [69,70,71,72,73], [74,75,76,77,78], [79,80,81,82,83,84], [85,86,87,88,89], [90,91,92,93,94,95], [104,105,106,107], [108,109], [], [100,99,101,102,103], [], [], [], [], [], [110,111,112,113], [114,115,116]]
rows = []
for row in sheet.findall('.//m:row', ns):
    cells = {}
    for c in row:
        v = c.find('m:v', ns)
        if v is not None:
            cells[re.sub(r'\d', '', c.attrib['r'])] = strings[int(v.text)] if c.attrib.get('t') == 's' else v.text
    if not cells.get('B') or cells['B'] == 'Nama Produk':
        continue
    i = len(rows)
    details = cells['K'].replace('Thight', 'Thigh')
    if '962 cm' in details:
        details = ' | '.join(s.strip() for s in details.split('|') if '962 cm' not in s)
    source_row = int(row.attrib['r'])
    photos = [f'catalog-{n}.jpg' for n in groups[i]]
    if not photos or i in (5, 7):
        photos.insert(0, references[source_row])
    rows.append({'name': cells['B'].strip(), 'price': cells['F'], 'category': cells['G'].lower(), 'description': cells['H'], 'weight_grams': int(re.search(r'\d+', cells['I'])[0]), 'details': details, 'photos': photos, 'source_row': source_row})
(root / 'tools/catalog.json').write_text(json.dumps(rows, indent=2, ensure_ascii=False), encoding='utf-8')
print(f'Prepared {len(rows)} products')
