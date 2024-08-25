import { defineStore } from "pinia";
import createAxios from "~/composables/api/axios";
const { axiosJson, createCSRF } = createAxios();
interface Response{
    status: string,
    code?: number,
    message?: string,
    data?: any,
}
export const useFetchDataStore = defineStore('fetchData', {
    state: () => ({
        processFetch: { isDone: 'loading' as string, message: '' as string},
        cacheAuth: {},
        cache: {
            firmware: [],
            device: [],
            admin: [],
            random: [],
        } as { [key: string]: Array<{url: string, [key: string]: any}> },
        retryCount: 0 as number,
    }),
    actions: {
        async fetchData(): Promise<Response> {
            try{
                const routePath = useRoute().fullPath;
                //search cache
                const sp = routePath.split('/');
                let keyC = sp.length > 1 ? Object.keys(this.cache).find(key => key == sp[1]) || 'random' : 'random';
                let lenghtK = this.cache[keyC].length;
                if(lenghtK > 0){
                    let data = (this.cache[keyC] as {url: string}[]).find((item) => item.url == routePath);
                    if(data) return { status: 'success', data: data }
                }
                const res: Record<string, any> = await axiosJson.get(`${routePath}?_=${Date.now()}`);
                this.processFetch = { isDone: 'success', message: ''}
                //check cache auth
                if(Object.keys(this.cacheAuth).length == 0) if(res.data && res.data.auth) this.cacheAuth = res.data.auth;
                //delete old cache
                if(lenghtK >= 3){
                    this.cache[keyC].pop();
                }
                (this.cache[keyC]).push({ url: routePath, data: res.data });
                return { status:'success', data: res.data};
            }catch(err: any){
                if (err.response){
                    if(err.response.status === 401){
                        navigateTo('/login');
                        // useNuxtApp().runWithContext(() => navigateTo('/login'));
                    }
                    if(err.response.status === 404){
                        this.processFetch = { isDone: 'error', message: 'not found'};
                        return { status:'error', message: 'not found', code: 404 };
                    }
                    if(err.response.status === 419) {
                        if (this.retryCount <= 3) {
                            this.retryCount++;
                            createCSRF();
                            return this.fetchData();
                        } else {
                            this.retryCount = 0;
                            this.processFetch = { isDone: 'error', message: 'Request Failed'};
                            return { status: 'error', message: 'Request failed' };
                        }
                    }
                    this.processFetch = { isDone: 'error', message: err.response.data.message };
                }
                return { status:'error', message: err.response.data.message };
            }
        },
        resetFetchData(cond = false) {
            if(cond) {
                Object.keys(this.cache).forEach(key => {
                    this.cache[key].forEach((item: { url: string}, index: number) => {
                        if (item.url === useRoute().fullPath) {
                            this.cache[key].splice(index, 1);
                        }
                    });
                });
            }
            this.processFetch = { isDone:'loading', message: ''};
        },
        resetCacheAuth(cond = false){
            if(cond){
                this.cacheAuth = {};
            }
            this.processFetch = { isDone:'loading', message: ''};
        }
    },
});