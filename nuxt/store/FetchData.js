import { defineStore } from "pinia";
import axios from "~/composables/api/axios";
const fetchCsrfToken = async () => {
    return await axios.get('/sanctum/csrf-cookie');
}
export const useFetchDataStore = defineStore('fetchData', {
    state: () => ({
        processFetch: { isDone: 'loading'},
        cache: {
            device:[],
            admin:[],
            random:[],
        },
        retryCount: 0,
    }),
    actions: {
        async fetchData() {
            try{
                const routePath = useRoute().fullPath;
                //search cache
                const sp = routePath.split('/');
                let keyC = sp.length > 1 ? Object.keys(this.cache).find(key => key == sp[1]) || 'random' : 'random';
                let lenghtK = this.cache[keyC].length;
                if(this.cache[keyC] != [] && lenghtK > 0){
                    let data = this.cache[keyC].find(item => item.url == routePath);
                    if(data) return { status: 'success', data: data }
                }
                const res = await axios.get(`${routePath}?_=${Date.now()}`, {
                    headers: {
                        'Accept': 'application/json',
                    }
                });
                this.processFetch = { isDone: 'success', message: ''}
                //delete old cache
                if(lenghtK >= 3){
                    this.cache[keyC].pop();
                }
                this.cache[keyC].push({ url: routePath, data: res.data });
                return { status:'success', data: res.data};
            }catch(err){
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
                }
                this.processFetch = { isDone: 'error', message: res.message};
                return { status:'error', message: err.response.data.message };
            }
        },
        resetFetchData(cond = false) {
            if(cond) {
                this.cache.forEach((item, index) => {
                    if (item.link === useRoute().fullPath) {
                        this.cache.splice(index, 1);
                    }
                });
            }
            this.processFetch = { isDone:'loading', message: ''};
        },
    },
});