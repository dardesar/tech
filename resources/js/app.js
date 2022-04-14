import { loadLanguage } from "./Functions/Language";

require('./bootstrap');

// Import modules...
import Vue from 'vue';
import VueTailwind from 'vue-tailwind'
import VueClipboard from 'vue-clipboard2'
import VueToast from 'vue-toast-notification'
import VueI18n from 'vue-i18n'

import { App as InertiaApp, plugin as InertiaPlugin } from '@inertiajs/inertia-vue';
import PortalVue from 'portal-vue';
import VueFileAgent from 'vue-file-agent';
import VueFileAgentStyles from 'vue-file-agent/dist/vue-file-agent.css';
import VueCookies from 'vue-cookies'
import vuescroll from 'vuescroll';

import LoadScript from 'vue-plugin-load-script';
import {vueComponentSettings} from "./components";
import PopperVue from '@soldeplata/popper-vue';
import VueSanitize from "vue-sanitize";

require('./icons');

// Register components

Vue.mixin({ methods: { route } });
Vue.use(InertiaPlugin);
Vue.use(PortalVue);
Vue.use(VueToast);
Vue.use(VueFileAgent);
Vue.use(VueCookies);
Vue.use(VueClipboard);
Vue.use(VueI18n);
Vue.use(LoadScript);
Vue.use(PopperVue);
Vue.use(VueSanitize);

Vue.use(vuescroll, {
    ops: {
        vuescroll: {},
        scrollPanel: {},
        rail: {
            'opacity': 1,
        },
        bar: {
            'keepShow': true,
            'opacity': 0.2
        }
    },
});

Vue.use(VueTailwind, vueComponentSettings)

const app = document.getElementById('app');

import store from './Store'

import MathMixin from '@/Mixins/Math/MathMixin';
import translations from '../lang/en.json';

let defaultLanguage = document.querySelector('meta[name="site-language"]').getAttribute('content');
let messages = [];

messages[defaultLanguage] = translations;

let i18n = new VueI18n({
    locale: defaultLanguage,
    messages: messages
})

const VueWorker = new Vue();

Object.defineProperties(Vue.prototype, {
    $worker: { get: () => { return VueWorker } }
})

window.Vue = new Vue({
    i18n: i18n,
    mixins: [MathMixin],
    store,
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
}).$mount(app);
