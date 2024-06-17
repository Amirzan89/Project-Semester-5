import axios from './axios';
export async function fetchData(url){
    try{
        const res = await axios.get(publicConfig.baseURL + url, {
            headers: {
                'Accept': 'application/json',
            }
        });
        if(res.data){
            return {
                userAuth: res.data.userAuth,
                viewData: res.data.viewData,
            }
        }else{
            console.log('data empty');
            // navigateTo('/login');
        }
    }catch(err){
        if(err.response.status === 404){
            // navigateTo('/login');
        }
        if (err.response.status === 302) {
            // navigateTo('/login');
        }
    }
}