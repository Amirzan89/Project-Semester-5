import { resolve } from 'path';
export default defineNuxtConfig({
    devtools: {
        enabled: true,
        timeline: {
            enabled: true,
        },
    },
    runtimeConfig:{
        public: {
            baseURL: process.env.APP_URL,
            appName: process.env.APP_NAME,
        },
    },
    ssr: false,
    css: [
        '~/assets/css/global.css',
        '~/assets/css/tailwind.css',
        '~/assets/styles.scss',
        '@fortawesome/fontawesome-svg-core/styles.css'
    ],
    postcss: {
        plugins: {
            tailwindcss: {},
            autoprefixer: {},
        },
    },
    components: [
        {
            path: '~/components',
            extensions: ['.vue'],
        }
    ],
    modules: [
        '@pinia/nuxt',
    ],
})