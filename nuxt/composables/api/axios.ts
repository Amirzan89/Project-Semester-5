import axios from 'axios';
import Cookies from 'js-cookie';
export default () => {
    const fetchCsrfToken = async () => {
        return await axios.get('/sanctum/csrf-cookie');
    };
    const createInstance = () => {
        let baseURL = '';
        try {
            baseURL = useRuntimeConfig().public.baseURL;
        } catch (err) {
            baseURL = 'http://localhost:8000';
        }
        const instance = axios.create({
            baseURL: baseURL,
            withCredentials: true
        });
        instance.interceptors.request.use(async (requestConfig) => {
            let token = Cookies.get('XSRF-TOKEN');
            if (!token) {
                await axios.get(`${baseURL}/sanctum/csrf-cookie`, { withCredentials: true });
                token = Cookies.get('XSRF-TOKEN');
            }
            requestConfig.headers['X-XSRF-TOKEN'] = token;
            return requestConfig;
        }, error => {
            return Promise.reject(error);
        });
        return instance;
    };
    const axiosJson = () => {
        const instance = createInstance();
        instance.defaults.headers.common['Accept'] = 'application/json';
        return instance;
    };
    const reqData = async(url: string, method: string, data: any = '', isJson: boolean = false) => {
        let retryCount = 0;
        const headers = isJson ? { Accept: 'application/json' } : {};
        const req = async () => {
            try {
                const response = await createInstance().request({ url, method, data, headers });
                return { status: 'success', message: response.data.message, data: response.data.data };
            } catch (err: any) {
                if (err.response){
                    switch(err.response.status){
                        case 404: return { status:'error', message: 'not found', code: 404 };
                        case 419:
                            if (retryCount <= 3) {
                                retryCount++;
                                await fetchCsrfToken();
                                return req();
                            } else {
                                retryCount = 0;
                                return { status: 'error', message: 'Request failed' };
                            }
                    }
                }
                return { status: 'error', message: err.response ? err.response.data.message : err.message, link: err.response.data.link ?? '' };
            }
        };
        return req();
    }
    return { axios: createInstance(), axiosJson: axiosJson(), createCSRF: fetchCsrfToken, reqData: reqData };
}