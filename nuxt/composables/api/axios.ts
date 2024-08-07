import axios from 'axios';
// import Cookies from 'js-cookie';
export default () => {
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
        // instance.interceptors.request.use(async (requestConfig) => {
        //     let token = Cookies.get('XSRF-TOKEN');
        //     if (!token) {
        //         await axios.get(`${baseURL}/sanctum/csrf-cookie`, { withCredentials: true });
        //         token = Cookies.get('XSRF-TOKEN');
        //     }
        //     requestConfig.headers['X-XSRF-TOKEN'] = token;
        //     return requestConfig;
        // }, error => {
        //     return Promise.reject(error);
        // });
        return instance;
    };
    const axiosJson = () => {
        const instance = createInstance();
        instance.defaults.headers.common['Accept'] = 'application/json';
        return instance;
    };
    return { axios: createInstance(), axiosJson: axiosJson() };
}