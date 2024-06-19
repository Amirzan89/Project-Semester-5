import axios from './axios';
export async function fetchData(url){
    try{
        const res = await axios.get(url, {
            headers: {
                'Accept': 'application/json',
            }
        });
        if(res.data){
            return {
                userAuth: res.data.userAuth,
                viewData: res.data.viewData,
            }
        }
    }catch(err){
        if(err.response.status === 404){
            return { status:'error', message: 'not found', code: 404 };
        }
        if (err.response.status === 401) {
            navigateTo('/login')
            // return { status: 'error', message: 'invalid token', code: 302, redirectedUrl: err.response.headers['location'] };
        }
        if (err.response.status === 302) {
            navigateTo(err.response.data.link);
            // return { status: 'error', message: err.response.data.message, code: 302, link: err.response.data.link };
        }
    }
}