import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// (optional) add CSRF header if a meta tag exists
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}
