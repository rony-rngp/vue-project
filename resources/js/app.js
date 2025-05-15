import './bootstrap';

import { createApp, h } from 'vue'
import {createInertiaApp, Link} from '@inertiajs/vue3'
import Layout from "./Pages/layouts/Layout.vue";
import AdminLayout from "./Pages/layouts/backend/AdminLayout.vue";
import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-bootstrap.css';
import  {ZiggyVue} from '../../vendor/tightenco/ziggy';



createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        let page =  pages[`./Pages/${name}.vue`];
        //page.default.layout = page.default.layout || Layout

        // Apply default layout based on folder
        if (!page.default.layout) {
            if (name.startsWith('admin/')) {
               // console.log(name)
                page.default.layout = AdminLayout;
            } else {
                page.default.layout = Layout;
            }
        }

        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            // Add Inertia plugin
            .use(plugin)
            .use(VueToast)
            .use(ZiggyVue)
            // Register Inertia link globally
            .component('Link', Link)
            .mount(el)
    },
})
