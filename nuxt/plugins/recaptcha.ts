import { VueReCaptcha } from 'vue-recaptcha-v3';

export default defineNuxtPlugin((nuxtApp) => {
    nuxtApp.vueApp.use(VueReCaptcha, {
        siteKey: useRuntimeConfig().public.recaptchaKey,
        loaderOptions: {
            autoHideBadge: false,
            explicitRenderParameters: {
                badge: 'bottomleft',
            },
        },
    });
});