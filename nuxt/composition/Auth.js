import axios from './axios';
export async function Login(data){
    try{
        const response = await axios.post('/users/login', {
            email: data.email,
            password: data.password,
        });
        return { status:'success', message: response.data.message};
    }catch(err){
        return { status:'error', message: err.response.data.message };
    }
}
export async function Register(data){
    try{
        const response = await axios.post('/users/register',{
            nama: data.nama,
            email: data.email,
            password: data.password,
            password_confirm: data.ulangiPassword,
        });
        return { status:'success', message: response.data.message, data: response.data.data};
    }catch(err){
        return { status:'error', message: err.response.data.message };
    }
}
export async function ForgotPassword(data){
    try{
        const response = await axios.post('/verify/create/password',{
            email: data.email,
        });
        return { status:'success', message: response.data.message, data: response.data.data };
    }catch(err){
        return { status:'error', message: err.response.data.message };
    }
}
export async function VerifyChange(data){
    try{
        const response = await axios.post('/verify/password',{
            nama: data.nama,
            email: data.email,
            password: data.password,
            password_confirm: data.ulangiPassword,
            description: data.description,
        });
        return { status:'success', message: response.data.message, data: response.data.data};
    }catch(err){
        return { status:'error', message: err.response.data.message };
    }
}
export async function SendOtp(data){
    try{
        const response = await axios.post(data.link,{
            email: data.email,
        });
        return { status:'success', message: response.data.message, data: response.data.data };
    }catch(err){
        return { status:'error', message: err.response.data.message };
    }
}
export async function VerifyOtp(data){
    try{
        const response = await axios.post(data.link,{
            email: data.email,
            otp: data.otp
        });
        return { status:'success', message: response.data.message};
    }catch(err){
        return { status:'error', message: err.response.data.message };
    }
}
export async function CheckAuth(link){
    try{
        const response = await axios.get(link, {
            headers: {
                'Accept': 'application/json',
            },
        });
        return { status:'success', message: response.data.message};
    }catch(err){
        if (err.response.status === 401) {
            return { status: 'error', message: err.response.data.message, code: 401, link: '/login' };
        }
        if (err.response.status === 302) {
            return { status: 'error', message: err.response.data.message, code: 302, link: err.response.data.link };
        }
        return { status:'error', message: err.response.data.message };
    }
}
export async function Logout(data){
    try{
        const response = await axios.post('/users/logout',{
            email: data.email,
            number: data.number,
        });
        return { status:'success', message: response.data.message};
    }catch(err){
        return { status:'error', message: err.response.data.message };
    }
}