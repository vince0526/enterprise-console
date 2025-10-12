// @ts-nocheck
import React from 'react';
import { createRoot } from 'react-dom/client';
import CoreDatabasesWorkbench from '../../../tools/CoreDatabasesWorkbench';

const mount = document.getElementById('core-databases-workbench');
if (mount) {
  try {
    console.log('[Workbench] Mounting…');
    const root = createRoot(mount);
    root.render(<CoreDatabasesWorkbench />);
    const dbg = document.getElementById('wb-debug');
    if (dbg) {
      dbg.style.display = 'block';
      dbg.textContent = '[Workbench] Mounted OK';
    }
  } catch (err) {
    console.error('[Workbench] Mount error', err);
    const dbg = document.getElementById('wb-debug');
    if (dbg) {
      dbg.style.display = 'block';
      dbg.textContent = String(err);
    }
  }
} else {
  console.error('[Workbench] Mount element not found');
}
