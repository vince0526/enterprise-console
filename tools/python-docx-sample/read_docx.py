from docx import Document
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
INPUT = ROOT / 'docs' / 'modules.docx'

if not INPUT.exists():
    print('File not found:', INPUT)
    raise SystemExit(1)

doc = Document(str(INPUT))

def extract_text(document):
    out = []
    for para in document.paragraphs:
        text = para.text.strip()
        if not text:
            continue
        style_name = para.style.name if para.style is not None else ''
        out.append((style_name, text))
    return out

items = extract_text(doc)
for style, text in items:
    print(f'[{style}] {text}')
