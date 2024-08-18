import createAxios from './axios';
const { reqData } = createAxios();
const prefix = '/device'
export async function TambahDevice(data: { name: string, device_id: string, token: string, activated: string }){
    return reqData(`${prefix}/create`, 'post', {
        name: data.name,
        device_id: data.device_id,
        token: data.token,
        password_confirm: data.activated,
    }, true);
}
export async function EditDevice(data: { id_device: string, name: string, device_id: string, token: string, activated: string }){
    return reqData(`${prefix}/update`, 'put',{
        name: data.name,
        device_id: data.device_id,
        token: data.token,
        password_confirm: data.activated,
    }, true);
}
export async function DeleteDevice(data: { id_device: string }){
    return reqData(`${prefix}/delete`, 'delete',{
        id_device: data.id_device,
    }, true);
}