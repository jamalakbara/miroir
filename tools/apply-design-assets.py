"""Copy authorized fonts and preserve the exact exported WhatsApp vectors."""
from pathlib import Path
import re, shutil, xml.etree.ElementTree as ET
root = Path(__file__).resolve().parents[1]
theme = root / 'wp-content/themes/miroir-theme'
for name in ['Avenir-Roman.ttf', 'Avenir-Medium.ttf']:
    shutil.copy2(root / 'docs/design/fonts' / name, theme / 'assets/fonts' / name)
css = theme / 'assets/css/fonts.css'
text = css.read_text(encoding='utf-8')
text += '\n@font-face{font-family:"Avenir";font-style:normal;font-weight:400;font-display:swap;src:url(../fonts/Avenir-Roman.ttf) format("truetype")}\n@font-face{font-family:"Avenir";font-style:normal;font-weight:500;font-display:swap;src:url(../fonts/Avenir-Medium.ttf) format("truetype")}\n' if '@font-face{font-family:"Avenir"' not in text else ''
css.write_text(text, encoding='utf-8')
for f in (theme / 'assets/css').glob('*.css'):
    t = f.read_text(encoding='utf-8').replace('font-weight: 300', 'font-weight: 400')
    if f.name == 'tokens.css':
        t = t.replace('"Inter Tight", system-ui', '"Avenir", "Inter Tight", system-ui').replace('--similar-cols-d: 7', '--similar-cols-d: 4')
    f.write_text(t, encoding='utf-8')
for name in ['theme.json', 'inc/elementor/kit-tab.php']:
    f = theme / name
    f.write_text(f.read_text(encoding='utf-8').replace('Inter Tight', 'Avenir'), encoding='utf-8')
f = theme / 'inc/customizer/registry.php'
t = f.read_text(encoding='utf-8')
t = t.replace("'default' => 7, 'min' => 0", "'default' => 4, 'min' => 0").replace("'Similar products columns · desktop', 'miroir' ), 7", "'Similar products columns · desktop', 'miroir' ), 4")
t = t.replace("'type' => 'checkbox', 'default' => false ),\n\t\t'card_hover_image'", "'type' => 'checkbox', 'default' => true ),\n\t\t'card_hover_image'")
f.write_text(t, encoding='utf-8')
html = (root / 'wp-content/uploads/miroir-design/whatsapp.html').read_text(encoding='utf-8')
ET.register_namespace('', 'http://www.w3.org/2000/svg')
svg = ET.Element('{http://www.w3.org/2000/svg}svg', {'width': '24', 'height': '24', 'viewBox': '0 0 24 24', 'aria-hidden': 'true', 'focusable': 'false', 'class': 'icon icon-whatsapp'})
for raw in re.findall(r'<svg[\s\S]*?</svg>', html):
    child = ET.fromstring(raw)
    style = dict(s.strip().split(':', 1) for s in child.attrib.pop('style').split(';') if ':' in s)
    for css_prop, attr in [('left', 'x'), ('top', 'y'), ('width', 'width'), ('height', 'height')]:
        child.set(attr, style[css_prop].strip().removesuffix('px'))
    child.attrib.pop('data-pencil-name', None)
    svg.append(child)
folder = theme / 'assets/images'
folder.mkdir(exist_ok=True)
(folder / 'whatsapp.svg').write_text(ET.tostring(svg, encoding='unicode'), encoding='utf-8')
for name in ['template-parts/product-buy.php', 'inc/template-tags.php']:
    f = theme / name
    f.write_text(f.read_text(encoding='utf-8').replace("miroir_icon( 'message-circle'", "miroir_icon( 'whatsapp'"), encoding='utf-8')
print('Applied Avenir and exact WhatsApp vectors')
