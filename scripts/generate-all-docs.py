#!/usr/bin/env python3
"""Generate all project DOCX artifacts (modules + environment setup).
Usage: python scripts/generate-all-docs.py
Requires python-docx.
"""
import subprocess
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]

scripts = [
    ROOT / 'scripts' / 'md_to_docx.py',
    ROOT / 'scripts' / 'env_setup_md_to_docx.py'
]

for script in scripts:
    if script.exists():
        print(f"[DOCS] Running {script.name}")
        res = subprocess.run([sys.executable, str(script)])
        if res.returncode != 0:
            print(f"[DOCS] Failed: {script}")
            sys.exit(res.returncode)
    else:
        print(f"[DOCS] Missing script: {script}")

print('[DOCS] All conversions complete.')
