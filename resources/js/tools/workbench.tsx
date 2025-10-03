// @ts-nocheck
import React from 'react';
import { createRoot } from 'react-dom/client';
import CoreDatabasesWorkbench from '../../../tools/CoreDatabasesWorkbench';

const mount = document.getElementById('core-databases-workbench');
if (mount) {
  const root = createRoot(mount);
  root.render(<CoreDatabasesWorkbench />);
}
