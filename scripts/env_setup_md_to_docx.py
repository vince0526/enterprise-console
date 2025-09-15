#!/usr/bin/env python3
"""Convert docs/environment-setup.md to docs/environment-setup.docx.
Requires: python-docx
Usage: python scripts/env_setup_md_to_docx.py
"""
from pathlib import Path
import sys

try:
    from docx import Document
    from docx.shared import Pt
except Exception:
    print("Missing dependency 'python-docx'. Install with: pip install python-docx")
    sys.exit(1)

ROOT = Path(__file__).resolve().parents[1]
MD_FILE = ROOT / 'docs' / 'environment-setup.md'
OUT_FILE = ROOT / 'docs' / 'environment-setup.docx'

if not MD_FILE.exists():
    print(f"Markdown file not found: {MD_FILE}")
    sys.exit(1)

text = MD_FILE.read_text(encoding='utf-8')
lines = text.splitlines()

doc = Document()
# Basic normal style tweak
try:
    normal = doc.styles['Normal']
    normal.font.name = 'Calibri'
    normal.font.size = Pt(11)
except Exception:
    pass

for line in lines:
    raw = line.rstrip('\n')
    stripped = raw.strip()
    if not stripped:
        doc.add_paragraph('')
        continue
    if stripped.startswith('# '):
        doc.add_heading(stripped[2:].strip(), level=1); continue
    if stripped.startswith('## '):
        doc.add_heading(stripped[3:].strip(), level=2); continue
    if stripped.startswith('### '):
        doc.add_heading(stripped[4:].strip(), level=3); continue
    if stripped.startswith('- '):
        doc.add_paragraph(stripped[2:].strip(), style='List Bullet'); continue
    if stripped.startswith('  - '):
        doc.add_paragraph(stripped[4:].strip(), style='List Bullet 2'); continue
    # Ordered list pattern "1) text" or "1. text"
    if (len(stripped) > 3 and stripped[0].isdigit() and (stripped[1:3] == ') ' or stripped[1] == '.' )):
        doc.add_paragraph(stripped[stripped.find(' ')+1:].strip(), style='List Number'); continue
    # Table lines: keep monospace-ish by just adding paragraph
    if stripped.startswith('|') and stripped.endswith('|'):
        doc.add_paragraph(stripped)
        continue
    doc.add_paragraph(stripped)

OUT_FILE.parent.mkdir(exist_ok=True, parents=True)
doc.save(OUT_FILE)
print(f"Written: {OUT_FILE}")
