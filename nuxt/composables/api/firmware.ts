import createAxios from './axios';
const { axiosJson } = createAxios();
const prefix = '/firmware';
export async function TambahFirmware(data: { name: string, description: string, version: string, release_date: string, checksum: string, device: string, file: File }){
    try{
        const response = await axiosJson.post(`${prefix}/create`, {
            name: data.name,
            description: data.description,
            version: data.version,
            release_date: data.release_date,
            checksum: data.checksum,
            device: data.device,
            file: data.file,
        });
        return { status:'success', message: response.data.message};
    }catch(err: any){
        return { status:'error', message: err.response.data.message };
    }
}
export async function EditFirmware(data: { id_firmware:string, name: string, description: string, version: string, release_date: string, checksum: string, device: string, file: File }){
    try{
        const response = await axiosJson.put(`${prefix}/update`,{
            id_firmware: data.id_firmware,
            name: data.name,
            description: data.description,
            version: data.version,
            release_date: data.release_date,
            checksum: data.checksum,
            device: data.device,
            file: data.file,
        });
        return { status:'success', message: response.data.message, data: response.data.data};
    }catch(err: any){
        return { status:'error', message: err.response.data.message };
    }
}
export async function DeleteFirmware(data: { id_firmware:string }){
    try{
        const response = await axiosJson.delete(`${prefix}/delete`,{
            data:{
                id_firmware: data.id_firmware,
            }
        });
        return { status:'success', message: response.data.message, data: response.data.data };
    }catch(err: any){
        return { status:'error', message: err.response.data.message };
    }
}