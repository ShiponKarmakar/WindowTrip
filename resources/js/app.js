import '../css/app.css';
import './bootstrap';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import NProgress from 'nprogress';
import '../css/nprogress-custom.css';

const appName = import.meta.env.VITE_APP_NAME || 'Window Trip';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    // Use the custom NProgress bar (below) instead of Inertia's default.
    progress: false,
});

// Custom loading bar
router.on('start', () => NProgress.start());
router.on('finish', () => NProgress.done());
