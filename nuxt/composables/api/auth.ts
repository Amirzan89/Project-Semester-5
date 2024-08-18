import createAxios from './axios';
const { reqData } = createAxios();
export async function Login(data: { email: string, password: string }){
    return reqData('/users/login', 'post', {
        email: data.email,
        password: data.password,
    }, true);
}
export async function CreateLoginGoogle(data: { email: string, nama: string, password: string, password_confirm: string }){
    return reqData('/users/register/google', 'post', {
        email: data.email,
        nama: data.nama,
        password: data.password,
        password_confirm: data.password_confirm,
    }, true);
}
export async function Register(data: { nama: string, email: string, password: string, ulangiPassword: string }){
    return reqData('/users/register', 'post', {
        nama: data.nama,
        email: data.email,
        password: data.password,
        password_confirm: data.ulangiPassword,
    }, true);
}
export async function ForgotPassword(data: { email:string }){
    return reqData('/verify/create/password', 'post', {
        email: data.email,
    }, true);
}
export async function VerifyChange(data: { nama: string, email: string, code: string, password: string, ulangiPassword: string, description: string }){
    return reqData('/verify/password', 'post', {
        nama: data.nama,
        email: data.email,
        code: data.code,
        password: data.password,
        password_confirm: data.ulangiPassword,
        description: data.description,
    }, true);
}
export async function SendOtp(data: { email: string, link: string, }){
    return reqData(data.link, 'post',{
        email: data.email,
    }, true);
}
export async function VerifyOtp(data: { link: string, email: string, otp:string }){
    return reqData(data.link, 'post', {
        email: data.email,
        otp: data.otp
    }, true);
}
export async function CheckAuth(link: string): Promise<{ status: string, data?: any, message: string, code?: number, link?:string }>{
    return reqData(link, 'get', '', true);
}
export async function updateProfile(data: any){
    return reqData('/users/update/profile', 'put', data, true);
}
export async function updatePassword(data: {password_old: string, password: string, password_ulangi: string}){
    return reqData('/users/update/password', 'put', {
        password_old: data.password_old,
        password: data.password,
        password_ulangi: data.password_ulangi,
    }, true);
}
export async function Logout(data: { email: string, number: string, }){
    return reqData('/users/logout', 'post', {
        email: data.email,
        number: data.number,
    }, true);
}