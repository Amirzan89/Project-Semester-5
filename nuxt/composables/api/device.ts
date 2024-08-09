import createAxios from './axios';
const prefix = '/device'
const { axiosJson } = createAxios();
export async function TambahDevice(data: { email: string, password: string }){
    try{
        const response = await axiosJson.post(`${prefix}/create`, {
            email: data.email,
            password: data.password,
        });
        return { status:'success', message: response.data.message};
    }catch(err: any){
        return { status:'error', message: err.response.data.message };
    }
}
export async function EditDevice(data: { nama: string, email: string, password: string, ulangiPassword: string }){
    try{
        const response = await axiosJson.put(`${prefix}/update`,{
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
export async function DeleteDevice(data: { email: string }){
    try{
        const response = await axiosJson.post(`${prefix}/delete`,{
            email: data.email,
        });
        return { status:'success', message: response.data.message, data: response.data.data };
    }catch(err: any){
        return { status:'error', message: err.response.data.message };
    }
}