import createAxios from './axios';
const { reqData } = createAxios();
const prefix = '/admin'
export async function TambahAdmin(data: { nama:string, email: string, password: string, ulangiPassword: string, role:string, }){
    return reqData(`${prefix}/create`, 'post', {
        nama: data.nama,
        email: data.email,
        password: data.password,
        password_confirm: data.ulangiPassword,
    }, true);
}
export async function EditAdmin(data: { nama: string, email: string, password: string, ulangiPassword: string }){
    return reqData(`${prefix}/update`, 'put', {
        nama: data.nama,
        email: data.email,
        password: data.password,
        password_confirm: data.ulangiPassword,
    }, true);
}
export async function DeleteAdmin(data: { email: string }){
    return reqData(`${prefix}/delete`, 'delete', {
        email: data.email,
    }, true);
}