import createAxios from './axios';
const { axiosJson } = createAxios();
const prefix = '/admin'
export async function TambahAdmin(data: { nama:string, email: string, password: string, ulangiPassword: string, role:string, }){
    try{
        const response = await axiosJson.post(`${prefix}/create`, {
            nama: data.nama,
            email: data.email,
            password: data.password,
            password_confirm: data.ulangiPassword,
        });
        return { status:'success', message: response.data.message};
    }catch(err: any){
        return { status:'error', message: err.response.data.message };
    }
}
export async function EditAdmin(data: { nama: string, email: string, password: string, ulangiPassword: string }){
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
export async function DeleteAdmin(data: { email: string }){
    try{
        const response = await axiosJson.delete(`${prefix}/delete`,{
            data:{
                email: data.email,
            }
        });
        return { status:'success', message: response.data.message, data: response.data.data };
    }catch(err: any){
        return { status:'error', message: err.response.data.message };
    }
}