import cosRouter from './app/cosRouter'
export default defineNuxtConfig({
    devServer:{
        port: 4000,
    },

    devtools: {
        enabled: true,
        timeline: {
            enabled: true,
        },
    },

    runtimeConfig:{
        public: {
            // baseURL: process.env.APP_URL || 'http://localhost:8000',
            baseURL: process.env.APP_URL || 'https://certani.amirzanfikri.my.id',
            appName: process.env.APP_NAME,
            GoogleId: process.env.APP_GOOGLE_TAP,
            recaptchaKey: process.env.RECAPTCHA_SITE_KEY,
        },
    },

    ssr: false,

    // hooks:{
    //     'pages:extend'(pages){
    //         pages.push(...cosRouter);
    //     }
    // },

    css: [
        '~/assets/style/tailwind.scss',
        '~/assets/style/global.scss',
        '~/assets/primevue.scss',
        '~/assets/style/theme.scss',
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

    modules: ['@pinia/nuxt', '@hypernym/nuxt-gsap', 'nuxt-primevue',"nuxt-svgo"],
    gsap:{
        extraPlugins:{
            scrollTrigger: true,
            draggable:true,
        }
    },
    primevue: {
        options: {
            ripple: true,
        }
    },
    svgo: {
        defaultImport: 'component',
    },
    // image:{
    //     providers:{
            
    //     },
    //     dir: 'assets/images',
    // },
})