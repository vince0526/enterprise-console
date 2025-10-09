import './bootstrap';

import Alpine from 'alpinejs';
import 'prismjs/components/prism-core';
import 'prismjs/components/prism-sql';
import Prism from 'prismjs';

window.Alpine = Alpine;

Alpine.start();

// Expose a stable highlighting function used by Blade view (avoids large Prism payload on window)
window.PrismHighlight = el => {
  try {
    Prism.highlightElement(el);
  } catch (e) {
    /* noop */
  }
};
