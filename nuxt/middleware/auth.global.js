import { CheckAuth } from '../composition/Auth';
const publicPage = ['/', '/login', '/register', '/password/reset']
export default defineNuxtRouteMiddleware(async (to, from) => {
    if(publicPage.includes(to.fullPath)){
        const res = await CheckAuth(to.fullPath);
        if(res.status == 'error' && res.link){
            if(to.fullPath == res.link){
                return;
            }
            return useNuxtApp().runWithContext(() => navigateTo(res.link));
        }
    }
});;