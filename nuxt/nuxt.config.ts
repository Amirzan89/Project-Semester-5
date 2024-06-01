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
        },
    },
    ssr: false,
    css: [
        '~/assets/css/tailwind.css',
        '~/assets/styles.scss',
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