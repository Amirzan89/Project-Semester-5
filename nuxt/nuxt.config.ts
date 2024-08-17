import cosRouter from './app/cosRouter'
export default defineNuxtConfig({
    // devServer:{
    //     port: 8000,
    // },
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
            recaptchaKey: process.env.RECAPTCHA_SITE_KEY,
        },
    },
    ssr: false,
    hooks:{
        'pages:extend'(pages){
            pages.push(...cosRouter);
        }
    },
    css: [
        '~/assets/style/global.scss',
        '~/assets/style/tailwind.scss',
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
    modules: ['@pinia/nuxt', '@hypernym/nuxt-gsap', "@nuxt/image", ],
    gsap:{
        extraPlugins:{
            scrollTrigger: true,
            draggable:true,
        }
    },
    image:{
        providers:{
            
        },
        dir: 'assets/images',
    }
})