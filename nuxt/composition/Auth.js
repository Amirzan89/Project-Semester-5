import axios from "axios";
const publicConfig = useRuntimeConfig().public;
const csrfToken = null;
export async function Login(data){
    try{
        const response = await axios.post(publicConfig.baseURL + '/users/login', {
            email: data.email,
            password: data.password,
        },{
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            }
        });
        return { status:'success', message: response.data.message};
    }catch(err){
        return { status:'error', message: err.response.data.message };
    }
}
export async function Register(data){
    try{
        const response = await axios.post(publicConfig.baseURL + '/users/register',{
            nama: data.nama,
            email: data.email,
            password: data.password,
            password_confirm: data.ulangiPassword,
        },{
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            }
        });
        return { status:'success', message: response.data.message};
    }catch(err){
        return { status:'error', message: err.response.data.message };
    }
}
export async function ForgotPassword(data){
    try{
        const response = await axios.post(publicConfig.baseURL + '/verify/create/password',{
            email: data.email,
        },{
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            }
        });
        return { status:'success', message: response.data.message, data: response.data.data };
    }catch(err){
        return { status:'error', message: err.response.data.message };
    }
}
export async function VerifyChange(data){
    try{
        const response = await axios.post(publicConfig.baseURL + '/verify/password',{
            nama: data.nama,
            email: data.email,
            password: data.password,
            password_confirm: data.ulangiPassword,
            description: data.description,
        },{
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            }
        });
        return { status:'success', message: response.data.message, data: response.data.data};
    }catch(err){
        return { status:'error', message: err.response.data.message };
    }
}
export async function SendOtp(data){
    try{
        const response = await axios.post(publicConfig.baseURL + data.link,{
            email: data.email,
        },{
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            }
        });
        return { status:'success', message: response.data.message, data: response.data.data };
    }catch(err){
        return { status:'error', message: err.response.data.message };
    }
}
export async function VerifyOtp(data){
    try{
        const response = await axios.post(publicConfig.baseURL + data.link,{
            email: data.email,
            otp: data.otp
        },{
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            }
        });
        return { status:'success', message: response.data.message};
    }catch(err){
        return { status:'error', message: err.response.data.message };
    }
}
export async function Logout(data){
    try{
        const response = await axios.post(publicConfig.baseURL + '/users/logout',{
            email: data.email,
            number: data.number,
        },{
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            }
        });
        return { status:'success', message: response.data.message};
    }catch(err){
        return { status:'error', message: err.response.data.message };
    }
}