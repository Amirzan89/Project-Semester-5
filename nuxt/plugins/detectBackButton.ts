import { useNotFoundStore } from '~/store/NotFound';
export default defineNuxtPlugin((nuxtApp) => {
    if (process.client) {
        window.addEventListener('popstate', async () => {
            useNotFoundStore().resetState();
            // await navigateTo(useNotFoundStore().linkBack);
        });
    }
});