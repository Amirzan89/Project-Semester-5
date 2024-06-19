import axios from 'axios';
import Cookies from 'js-cookie';
let baseURL = '';
try{
    baseURL = useRuntimeConfig().public.baseURL;
}catch(err){
    baseURL = 'http://localhost:7000';
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
export default instance;