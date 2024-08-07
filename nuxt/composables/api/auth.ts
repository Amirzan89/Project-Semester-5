import createAxios from './axios';
const { axios, axiosJson } = createAxios();
export async function Login(data: { email: string, password: string }){
    try{
        const response = await axiosJson.post('/users/login', {
            email: data.email,
            password: data.password,
        });
        return { status:'success', message: response.data.message};
    }catch(err: any){
        return { status:'error', message: err.response.data.message };
    }
}
export async function Register(data: { nama: string, email: string, password: string, ulangiPassword: string }){
    try{
        const response = await axiosJson.post('/users/register',{
            nama: data.nama,
            email: data.email,
            password: data.password,
            password_confirm: data.ulangiPassword,
        });
        return { status:'success', message: response.data.message, data: response.data.data};
    }catch(err: any){
        return { status:'error', message: err.response.data.message };
    }
}
export async function ForgotPassword(data: { email:string }){
    try{
        const response = await axiosJson.post('/verify/create/password',{
            email: data.email,
        });
        return { status:'success', message: response.data.message, data: response.data.data };
    }catch(err: any){
        return { status:'error', message: err.response.data.message };
    }
}
export async function VerifyChange(data: { nama: string, email: string, password: string, ulangiPassword: string, description: string }){
    try{
        const response = await axiosJson.post('/verify/password',{
            nama: data.nama,
            email: data.email,
            password: data.password,
            password_confirm: data.ulangiPassword,
            description: data.description,
        });
        return { status:'success', message: response.data.message, data: response.data.data};
    }catch(err: any){
        return { status:'error', message: err.response.data.message };
    }
}
export async function SendOtp(data: { email: string, link: string, }){
    try{
        const response = await axiosJson.post(data.link,{
            email: data.email,
        });
        return { status:'success', message: response.data.message, data: response.data.data };
    }catch(err: any){
        return { status:'error', message: err.response.data.message };
    }
}
export async function VerifyOtp(data: { link: string, email: string, otp:string }){
    try{
        const response = await axiosJson.post(data.link,{
            email: data.email,
            otp: data.otp
        });
        return { status:'success', message: response.data.message};
    }catch(err: any){
        return { status:'error', message: err.response.data.message };
    }
}
export async function CheckAuth(link: string): Promise<{ status: string, data?: any, message: string, code?: number, link?:string }>{
    try{
        const response = await axiosJson.get(link);
        return { status:'success', message: response.data.message, data: response.data.data};
    }catch(err: any){
        if (err.response.status === 401) {
            return { status: 'error', message: err.response.data.message, code: 401, link: '/login' };
        }
        if (err.response.status === 302) {
            return { status: 'error', message: err.response.data.message, code: 302, link: err.response.data.link };
        }
        return { status:'error', message: err.response.data.message };
    }
}
export async function Logout(data: { email: string, number: string, }){
    try{
        const response = await axiosJson.post('/users/logout',{
            email: data.email,
            number: data.number,
        });
        return { status:'success', message: response.data.message};
    }catch(err: any){
        return { status:'error', message: err.response.data.message };
    }
}