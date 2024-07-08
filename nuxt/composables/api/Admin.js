import axios from '../axios';
export async function TambahAdmin(data){
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
export async function EditAdmin(data){
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
export async function DeleteAdmin(data){
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