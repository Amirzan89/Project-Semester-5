import createAxios from './axios';
const { axiosJson } = createAxios();
const prefix = '/firmware';
export async function getFirmware(data: { email: string, password: string }){
    try{
        const response = await axiosJson.post('/firmware', {
            email: data.email,
            password: data.password,
        });
        return { status:'success', message: response.data.message};
    }catch(err: any){
        return { status:'error', message: err.response.data.message };
    }
}
export async function TambahFirmware(data: { email: string, password: string,  }){
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
export async function EditFirmware(data: { nama: string, email: string, password: string, ulangiPassword: string }){
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