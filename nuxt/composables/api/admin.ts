import createAxios from './axios';
const { reqData } = createAxios();
export async function TambahAdmin(data: { nama_lengkap: string, jenis_kelamin: string, no_telpon: string, role: string, email: string, password: string, foto: File }){
    const formData = new FormData();
    formData.append('nama_lengkap', data.nama_lengkap);
    formData.append('jenis_kelamin', data.jenis_kelamin);
    formData.append('no_telpon', data.no_telpon);
    formData.append('role', data.role);
    formData.append('email', data.email);
    formData.append('password', data.password);
    if(data.foto != null){
        formData.append('foto', data.foto);
    }
    return reqData('/admin/create', 'post', formData, true);
}
export async function EditAdmin(data: { id_admin: string, nama_lengkap: string, jenis_kelamin: string, no_telpon: string, email_old: string, email?: string, foto?: File }){
    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('id_admin', data.id_admin);
    formData.append('nama_lengkap', data.nama_lengkap);
    formData.append('jenis_kelamin', data.jenis_kelamin);
    formData.append('no_telpon', data.no_telpon);
    formData.append('email_admin_lama', data.email_old);
    if(data.email){
        formData.append('email', data.email);
    }
    if(data.foto){
        formData.append('foto', data.foto);
    }
    return reqData('/admin/update', 'post', formData, true);
}
export async function DeleteAdmin(data: { id_admin: string}){
    return reqData('/admin/delete', 'delete', {
        id_admin: data.id_admin,
    }, true);
}