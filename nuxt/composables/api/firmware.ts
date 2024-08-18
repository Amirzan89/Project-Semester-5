import createAxios from './axios';
const { reqData } = createAxios();
const prefix = '/firmware';
export async function DownloadFirmware(data: { id_device: string, device: string }){
    return reqData(`${prefix}/download`, 'post', {
        id_device: data.id_device,
        device: data.device,
    }, true);
}
export async function TambahFirmware(data: { name: string, description: string, version: string, release_date: string, checksum: string, device: string, file: File }){
    return reqData(`${prefix}/create`, 'post', {
        name: data.name,
        description: data.description,
        version: data.version,
        release_date: data.release_date,
        checksum: data.checksum,
        device: data.device,
        file: data.file,
    }, true);
}
export async function EditFirmware(data: { id_firmware:string, name: string, description: string, version: string, release_date: string, checksum: string, device: string, file: File }){
    return reqData(`${prefix}/update`, 'put',{
        id_firmware: data.id_firmware,
        name: data.name,
        description: data.description,
        version: data.version,
        release_date: data.release_date,
        checksum: data.checksum,
        device: data.device,
        file: data.file,
    }, true);
}
export async function DeleteFirmware(data: { id_firmware:string }){
    return reqData(`${prefix}/delete` , 'delete', {
        id_firmware: data.id_firmware,
    }, true);
}