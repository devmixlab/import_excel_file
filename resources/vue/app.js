import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import "../scss/app.scss";
// import 'bootstrap/dist/js/bootstrap.bundle.js';

import bootstrap from 'bootstrap/dist/js/bootstrap.bundle.js';
window.bootstrap = bootstrap;

import { createApp, h, DefineComponent } from 'vue';
import Router from './Components/Router/index.js';

import App from './Components/App.vue';

// createApp({})
createApp({ render: () => h(App, {}) })
    .use(Router)
    .mount(document.getElementById('app'));