import { defineStore } from "pinia";
import createAxios from "~/composables/api/axios";
import ImgBoy from '~/assets/images/profile/default_boy.jpg';
import ImgGirl from '~/assets/images/profile/default_girl.png';
const { axiosJson, createCSRF } = createAxios();
interface Response{
    status: 'success' | 'error',
    code?: number,
    message?: string,
    data?: any,
}
export const useFetchDataStore = defineStore('fetchDataStore', {
    state: () => ({
        publicPath: ['/', '/login'],
        processFetch: { isDone: 'loading' as 'error' | 'loading' | 'success', message: '' as string},
        cacheAuth: {},
        cache: {
            admin: [],
            any: [],
        } as { [key: string]: Array<{url: string, [key: string]: any}> },
        retryCount: 0 as number,
    }),
    actions: {
        async fetchData(routePath: string, params?: {}, refer?: string): Promise<Response> {
            try{
                if(useRoute().path == '/testing'){
                    this.processFetch = { isDone: 'success', message: ''}
                    return {status:'success'};
                }
                if(refer == 'userAuth'){
                }
                const res: Record<string, any> = await axiosJson.get(routePath, { params: {...params, _: Date.now()}});
                this.processFetch = { isDone: 'success', message: ''}
                return res.data;
            }catch(err: any){
                if (err.response){
                    if([302, 301, 401].includes(err.response.status)){
                        await useNuxtApp().runWithContext(() => navigateTo(this.publicPath.includes(routePath) ? err.response.data.link : '/login'));
                    }
                    if(err.response.status === 404){
                        this.processFetch = { isDone: 'error', message: 'not found'};
                        return { status:'error', message: 'not found', code: 404 };
                    }
                    if(err.response.status === 419) {
                        if (this.retryCount <= 3) {
                            this.retryCount++;
                            createCSRF();
                            return this.fetchData(routePath, params, refer);
                        } else {
                            this.retryCount = 0;
                            this.processFetch = { isDone: 'error', message: 'Request Failed'};
                            return { status: 'error', message: 'Request failed' };
                        }
                    }
                    if(err.response.status === 500){
                        return { status: 'error', message: err.response.data.message };
                    }
                    return { status:'error', message: err.response.data.message, code: err.response.status };
                }
                return { status:'error', message: err };
            }
        },
        async fetchPage(params?: {}, req? :{}): Promise<Response>{
            const routePath: string = useRoute().fullPath;
            //search cache
            const sp = routePath.split('/');
            let keyC = sp.length > 1 ? Object.keys(this.cache).find(key => key == sp[1]) || 'any' : 'any';
            let lenghtK = this.cache[keyC].length;
            if(lenghtK > 0){
                let cacheDat: any = (this.cache[keyC] as {url: string}[]).find((item) => item.url == routePath);
                if(cacheDat) return { status: 'success', data: JSON.parse(JSON.stringify(cacheDat.data)) }
            }
            const res = await this.fetchData(routePath, params);
            if(res.code && res.code != 200) return res;
            //delete old cache
            if(lenghtK >= 3){
                this.cache[keyC].pop();
            }
            const isEmpty = (obj: any) => Array.isArray(obj) ? obj.length === 0 : Object.keys(obj).length === 0;
            if(!this.cache[keyC].some(item => item.url == routePath) && res.data && !isEmpty(res.data)){
                this.cache[keyC].push({ url: routePath, data: res.data });
            }
            return res;
        },
        async fetchAuth(){
            if(Object.keys(this.cacheAuth).length === 0 && !this.publicPath.includes(useRoute().fullPath)){
                const publicConfig = useRuntimeConfig().public;
                const res = await this.fetchData('/profile', { params: { _: Date.now() } }, 'userAuth');
                if(res.code && res.code != 200) return res;
                if(res.data.foto == '' || res.data.foto == null){
                    res.data.foto = res.data.jenis_kelamin == 'laki-laki' ? ImgBoy : ImgGirl;
                }else{
                    if(res.data.role == 'super admin' || res.data.role == 'admin'){
                        res.data.foto = publicConfig.baseURL + '/admin/download/foto';
                    }else{
                        res.data.foto = publicConfig.baseURL + '/download/foto';
                    }
                }
                this.cacheAuth = res.data;
                return res;
            }
            return { status: 'success', 'data': this.cacheAuth };
        },
        resetFetchData() {
            Object.keys(this.cache).forEach(key => {
                this.cache[key].forEach((item: { url: string}, index: number) => {
                    if (item.url === useRoute().fullPath) {
                        this.cache[key].splice(index, 1);
                    }
                });
            });
        },
        resetCacheAuth(){
            this.cacheAuth = {};
        }
    },
});