import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { i18nVue } from 'laravel-vue-i18n';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { initializeTheme } from './composables/useAppearance';
import VueSweetalert2 from 'vue-sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'
import ToastPlugin from 'vue-toast-notification'
import 'vue-toast-notification/dist/theme-sugar.css'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
	title: (title) => (title ? `${title} - ${appName}` : appName),
	resolve: (name) =>
		resolvePageComponent(
			`./pages/${name}.vue`,
			import.meta.glob<DefineComponent>('./pages/**/*.vue'),
		),
	setup({ el, App, props, plugin }) {
		const app = createApp({ render: () => h(App, props) })
            .use(VueSweetalert2)
            .use(ToastPlugin, {
                position: 'left-right',
                duration: 4000,
                dismissible: true,
            })
			.use(plugin)
			.use(ZiggyVue)
			.use(i18nVue as any, {
				resolve: async (lang: any) => {
					const langs = import.meta.glob('../../lang/*.json');
					return await langs[`../../lang/${lang}.json`]();
				},
				onLoad: () => {
					/**
					 * Fix: https://github.com/xiCO2k/laravel-vue-i18n/issues/93#issuecomment-1425683737
					 * Mounted here so translations are loaded before vue.
					 */
					app.mount(el);
				},
			});

		return app;
	},
	progress: {
		color: '#4B5563',
	},
});

// This will set light / dark mode on page load...
initializeTheme();
