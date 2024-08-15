import createAxios from './axios';
const { reqData } = createAxios();
const prefix = '/device'
export async function TambahDevice(data: { email: string, password: string }){
    return reqData(`${prefix}/create`, 'post', {
        email: data.email,
        password: data.password,
    }, true);
}
export async function EditDevice(data: { nama: string, email: string, password: string, ulangiPassword: string }){
    return reqData(`${prefix}/update`, 'put',{
        nama: data.nama,
        email: data.email,
        password: data.password,
        password_confirm: data.ulangiPassword,
    }, true);
}
export async function DeleteDevice(data: { email: string }){
    return reqData(`${prefix}/delete`, 'delete',{
        email: data.email,
    }, true);
}