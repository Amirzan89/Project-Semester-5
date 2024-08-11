import { defineStore } from "pinia";
import createAxios from "~/composables/api/axios";
interface Response{
    status: string,
    code?: number,
    message?: string,
    data?: any,
}
const { axios } = createAxios();
const fetchCsrfToken = async () => {
    return await axios.get('/sanctum/csrf-cookie');
}
export const useFetchDataStore = defineStore('fetchData', {
    state: () => ({
        processFetch: { isDone: 'loading' as string, message: '' as string},
        cache: {
            device: [],
            admin: [],
            random: [],
        } as { [key: string]: Array<{url: string, [key: string]: any}> },
        retryCount: 0 as number,
    }),
    actions: {
        async fetchData(link?: string): Promise<Response> {
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
                const res: Record<string, []> = await axios.get(`${routePath}?_=${Date.now()}`, {
                    headers: {
                        'Accept': 'application/json',
                    }
                });
                this.processFetch = { isDone: 'success', message: ''}
                //delete old cache
                if(lenghtK >= 3){
                    this.cache[keyC].pop();
                }
                (this.cache[keyC]).push({ url: routePath, data: res.data });
                return { status:'success', data: res.data};
            }catch(err: any){
                if (err.response){
                    if(err.response.status === 404){
                        this.processFetch = { isDone: 'error', message: 'not found'};
                        return { status:'error', message: 'not found', code: 404 };
                    }
                    if(err.response.status === 419) {
                        if (this.retryCount <= 3) {
                            this.retryCount++;
                            await fetchCsrfToken();
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
    },
});