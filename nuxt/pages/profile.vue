<template>
    <template v-if="local.isDoneFetch">
        <div class="flex">
            <header id="header" class="w-full h-15 relative border-black flex flex-row gap-5">
                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 bg-slate-500 h-1" style="width:96%"></div>
                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 flex flex-row gap-2" style="width:96%">
                    <div class="w-25 pb-2 before:absolute before:w-25 before:h-1 before:bottom-0 flex justify-center cursor-pointer" :class="{ 'before:bg-red-500': local.profileSection === 'profile'}" @click="changeSide('profile')">
                        <span class="text-xl">Personal</span>
                    </div>
                    <div class="w-40 pb-2 before:absolute before:w-40 before:h-1 before:bottom-0 flex justify-center cursor-pointer" :class="{ 'before:bg-red-500': local.profileSection === 'password'}" @click="changeSide('password')">
                        <span class="text-xl">Ubah Password</span>
                    </div>
                </div>
            </header>
            <form>
                <div class="w-70 h-40 flex flex-col justify-center mx-auto cursor-pointer relative gap-2 rounded-lg" :class="{
                    '' : inputProfile.linkFileProfile !== '',
                    'border-black border-dashed border-3' : inputProfile.linkFileProfile === ''
                }" @dragover.prevent="handleDragOverPersonal" @drop.prevent="handleDropPersonal" @click="handleFormClickPersonal">
                    <img :src="inputProfile.linkFileProfile" alt="" class="w-70 absolute" :class="{ 'hidden': inputProfile.linkFileProfile === ''}">
                    <img :src="publicConfig.baseURL + '/img/icon/drop.svg'" alt="" class="mt-2 h-15 relative top-2 pointer-events-none" :class="{ 'hidden': inputProfile.linkFileProfile !== ''}">
                    <span class="text-center text-lg" :class="{ 'hidden': inputProfile.linkFileProfile !== ''}">Pilih atau jatuhkan file gambar</span>
                    <input type="file" class="hidden" ref="fileInputProfile" @change="handleFileChangePersonal">
                </div>
                <div>
                    <label for="name">Name</label>
                    <input type="text" required @input="inpChange('nama')" v-model="inputProfile.nama">
                </div>
                <div>
                    <label for="name">email</label>
                    <input type="text" required @input="inpChange('email')" v-model="inputProfile.email">
                </div>
                <div class="w-full flex flex-col gap-1">
                    <label for="" class="text-2xl">Jenis Kelamin</label>
                    <select @click="inpChange('jenis_kelamin')" ref="inpJenisKelamin" v-model="inputProfile.jenis_kelamin" class="outline-none rounded-lg h-10 border-2 border-black text-xl pl-1">
                        <option value="" :disabled="inputProfile.jenis_kelamin !== ''">Pilih jenis kelamin</option>
                        <option value="laki-laki">Laki-Laki</option>
                        <option value="perempuan">Perempuan</option>
                    </select>
                </div>
                <button class="mt-2 w-55 h-12 bg-green-500 relative ml-auto rounded-lg flex items-center justify-center text-2xl text-white font-semibold" @click.prevent="updateProfileForm">Update Password</button>
            </form>
            <form action="" class="mt-15" :class="{ 'hidden': local.profileSection !== 'password'}">
                <div class="mt-10 w-11/12 flex left-1/2 mx-auto gap-1">
                    <label for="" class="text-2xl w-7/8">Password Lama</label>
                    <div class="relative w-7/8">
                        <input ref="inpPassLama" type="password" class="w-full outline-none rounded-lg h-10 border-2 border-black text-xl pl-1" @input="inpChange('pass_lama')" v-model="inputResetPass.pass_lama">
                        <div class="eye absolute top-1/2 -translate-y-1/2 right-3 cursor-pointer" @click="showPass('lama')">
                            <img :src="publicConfig.baseURL + '/img/login/eye-slash.svg'" alt="" class="xl:w-8 lg:w-7" :class="inputResetPass.pass_lama === '' || (inputResetPass.pass_lama !== '' && inputResetPass.isPasswordLamaShow === true) ? 'hidden' : ''">
                            <img :src="publicConfig.baseURL + '/img/login/eye.svg'" alt="" class="xl:w-8 lg:w-7" :class="inputResetPass.pass_lama === '' || (inputResetPass.pass_lama !== '' && inputResetPass.isPasswordLamaShow === false) ? 'hidden' : ''">
                        </div>
                    </div>
                </div>
                <div class="mt-10 w-11/12 flex left-1/2 mx-auto gap-1">
                    <label for="" class="text-2xl w-7/8">Password Baru</label>
                    <div class="relative w-7/8">
                        <input ref="inpPassBaru" type="password" class="w-full outline-none rounded-lg h-10 border-2 border-black text-xl pl-1" @input="inpChange('pass_baru')" v-model="inputResetPass.pass_baru">
                        <div class="eye absolute top-1/2 -translate-y-1/2 right-3 cursor-pointer" @click="showPass('baru')">
                            <img :src="publicConfig.baseURL + '/img/login/eye-slash.svg'" alt="" class="xl:w-8 lg:w-7" :class="inputResetPass.pass_baru === '' || (inputResetPass.pass_baru !== '' && inputResetPass.isPasswordBaruShow === true) ? 'hidden' : ''">
                            <img :src="publicConfig.baseURL + '/img/login/eye.svg'" alt="" class="xl:w-8 lg:w-7" :class="inputResetPass.pass_baru === '' || (inputResetPass.pass_baru !== '' && inputResetPass.isPasswordBaruShow === false) ? 'hidden' : ''">
                        </div>
                    </div>
                </div>
                <div class="mt-10 w-11/12 flex left-1/2 mx-auto gap-1">
                    <label for="" class="text-2xl w-7/8">Masukkan Kembali Password Baru</label>
                    <div class="relative w-7/8">
                        <input ref="inpPassBaruUlangi" type="password" class="w-full outline-none rounded-lg h-10 border-2 border-black text-xl pl-1" @input="inpChange('pass_baru_ulangi')" v-model="inputResetPass.pass_baru_ulangi">
                        <div class="eye absolute top-1/2 -translate-y-1/2 right-3 cursor-pointer" @click="showPass('ulangi')">
                            <img :src="publicConfig.baseURL + '/img/login/eye-slash.svg'" alt="" class="xl:w-8 lg:w-7" :class="inputResetPass.pass_baru_ulangi === '' || (inputResetPass.pass_baru_ulangi !== '' && inputResetPass.isPasswordBaruUlangiShow === true) ? 'hidden' : ''">
                            <img :src="publicConfig.baseURL + '/img/login/eye.svg'" alt="" class="xl:w-8 lg:w-7" :class="inputResetPass.pass_baru_ulangi === '' || (inputResetPass.pass_baru_ulangi !== '' && inputResetPass.isPasswordBaruUlangiShow === false) ? 'hidden' : ''">
                        </div>
                    </div>
                </div>
                <div class="mt-10 w-11/12 flex flex-row left-1/2 mx-auto gap-5 mb-5">
                    <button class="mt-2 w-55 h-12 bg-green-500 relative ml-auto rounded-lg flex items-center justify-center text-2xl text-white font-semibold" @click.prevent="updatePasswordForm">Update Password</button>
                </div>
            </form>
        </div>
    </template>
    <template v-else>
        <div>
        </div>
    </template>
</template>
<style scoped lang="scss"></style>
<script setup lang="ts">
import { ref, reactive, onMounted, type Ref } from "vue";
import { eventBus } from '~/app/eventBus';
import { updatePassword, updateProfile } from "~/composables/api/auth";
import { useFetchDataStore } from '~/store/FetchData';
const publicConfig = useRuntimeConfig().public;
definePageMeta({
    name: 'Profile',
    layout: 'default',
});
useHead({
    title:`Profile | ${publicConfig.appName}`
});
const local = reactive({
    isRequestInProgress: false,
    isDoneFetch: false,
    fetchedUserAuth: null as any,
    fetchedViewData: null as any,
    profileSection:'profile',
});
const inputProfile = reactive({
    nama:'',
    email:'',
    jenis_kelamin:'',
    role:'',
    linkFileProfile: '',
    fileProfile: null as File | null,
});
const inputResetPass = reactive({
    pass_lama: '',
    pass_baru: '',
    pass_baru_ulangi: '',
    isPasswordLamaShow:false,
    isPasswordBaruShow:false,
    isPasswordBaruUlangiShow:false,
});
const inpNama: Ref = ref(null);
const inpEmail: Ref = ref(null);
const inpJenisKelamin: Ref = ref(null);
const inpPassLama: Ref = ref(null);
const inpPassBaru: Ref = ref(null);
const inpPassBaruUlangi: Ref = ref(null);
const fileInputProfile: Ref = ref(null);
useAsyncData(async() => {
    const res = await useFetchDataStore().fetchData();
    if(res ==  undefined || res.status == 'error'){
        return;
    }else{
        local.isDoneFetch = true;
        local.fetchedViewData = res.data.other;
    }
});
watch(() => local.fetchedUserAuth, () => {
    if (local?.fetchedUserAuth !== undefined && local.fetchedUserAuth !== null && typeof local.fetchedUserAuth === 'object' && !Array.isArray(local.fetchedUserAuth) && Object.keys(local.fetchedUserAuth).length > 0) {
        if(local.fetchedUserAuth.foto !== null && local.fetchedUserAuth.foto !== ''){
            inputProfile.linkFileProfile = publicConfig.baseURL + '/img/user/' + local.fetchedUserAuth.foto;
        }else{
            if(local.fetchedUserAuth.jenis_kelamin === 'laki-laki'){
                inputProfile.linkFileProfile = '/img/user/default_boy.jpg';
            }else if(local.fetchedUserAuth.jenis_kelamin === 'perempuan'){
                inputProfile.linkFileProfile = '/img/user/default_girl.png';
            }
        }
        let userData: any = local.fetchedUserAuth;
        inputProfile.nama = userData.nama;
        inputProfile.email = userData.email;
    }
}, { immediate:true });
const changeSide = (cond: string) => {
    if(cond === 'profile'){
        local.profileSection = 'profile';
    }else if(cond === 'password'){
        local.profileSection = 'password';
    }
};
const showPass = (cond: string) => {
    if(cond === 'lama'){
        if(inputResetPass.isPasswordLamaShow){
            inpPassLama.value.type = 'password';
            inputResetPass.isPasswordLamaShow = false;
        }else{
            inpPassLama.value.type = 'text';
            inputResetPass.isPasswordLamaShow = true;
        }
    }else if(cond === 'baru'){
        if(inputResetPass.isPasswordBaruShow){
            inpPassBaru.value.type = 'password';
            inputResetPass.isPasswordBaruShow = false;
        }else{
            inpPassBaru.value.type = 'text';
            inputResetPass.isPasswordBaruShow = true;
        }
    }else if(cond === 'ulangi'){
        if(inputResetPass.isPasswordBaruUlangiShow){
            inpPassBaruUlangi.value.type = 'password';
            inputResetPass.isPasswordBaruUlangiShow = false;
        }else{
            inpPassBaruUlangi.value.type = 'text';
            inputResetPass.isPasswordBaruUlangiShow = true;
        }
    }
};
const handleFormClickPersonal = () => {
    fileInputProfile.value.click();
};
const handleFileChangePersonal = (event: any) => {
    handleFiles(event.target.files);
};
const handleDragOverPersonal = (event: Event) => {
    event.preventDefault();
};
const handleDropPersonal = (event: any) => {
    event.preventDefault();
    handleFiles(event.dataTransfer.files);
};
const handleFiles = async(files: FileList) => {
    if (files.length !== 1) {
        throw new Error('Please select only one file.');
    }
    const file = files[0];
    if (!file.type.startsWith('image/')) {
        throw new Error('Please select an image file.');
    }
    if (file.size > 2 * 1024 * 1024) {
        throw new Error('File size exceeds the maximum limit of 2 MB.');
    }
    inputProfile.fileProfile = file;
    inputProfile.linkFileProfile = URL.createObjectURL(file);
};
const inpChange = (div: string) => {
    switch(div){
        case 'name':
            inpNama.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpNama.value.classList.add('border-black','hover:border-black','focus:border-black');
        break;
        case 'email':
            inpEmail.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpEmail.value.classList.add('border-black','hover:border-black','focus:border-black');
        break;
        case 'pass_lama':
            inpPassLama.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpPassLama.value.classList.add('border-black','hover:border-black','focus:border-black');
        break;
        case 'pass_baru':
            inpPassBaru.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpPassBaru.value.classList.add('border-black','hover:border-black','focus:border-black');
        break;
        case 'pass_baru_ulangi':
            inpPassBaruUlangi.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpPassBaruUlangi.value.classList.add('border-black','hover:border-black','focus:border-black');
        break;
    }
};
const isValidEmail = (email: string) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
};
const updateProfileForm = async (event: Event) => {
    event.preventDefault();
    if(local.isRequestInProgress) return;
    let errMessage = '';
    if (inputProfile.nama === local.fetchedUserAuth.nama_lengkap && inputProfile.email === local.fetchedUserAuth.email && inputProfile.jenis_kelamin === local.fetchedUserAuth.jenis_kelamin && inputProfile.fileProfile === null) if(errMessage == '') errMessage = 'Data belum diubah !';
    if(inputProfile.nama === null || inputProfile.nama === ''){
        inpNama.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpNama.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if(!errMessage) errMessage = 'Nama lengkap Harus diisi !';
    }
    if(inputProfile.email === null || inputProfile.email === ''){
        inpEmail.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpEmail.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if(!errMessage) errMessage = 'Email Harus diisi !';
    }else{
        if (!isValidEmail(inputProfile.email)) {
            inpEmail.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpEmail.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Masukkan email dengan benar !';
        }
    }
    if(inputProfile.jenis_kelamin === null || inputProfile.jenis_kelamin === ''){
        inpJenisKelamin.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpJenisKelamin.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if(!errMessage) errMessage = 'Jenis Kelamin Harus diisi !';
    }
    if(errMessage != ''){
        eventBus.emit('showRedPopup', errMessage);
        return;
    }
    local.isRequestInProgress = true;
    eventBus.emit('showLoading');
    const formData = new FormData();
    formData.append('_method', 'PUT');
    if(inputProfile.email !== local.fetchedUserAuth.email){
        formData.append('email_new', inputProfile.email)
    }
    formData.append('nama_lengkap', inputProfile.nama);
    formData.append('jenis_kelamin', inputProfile.jenis_kelamin);
    if(inputProfile.fileProfile !== null){
        formData.append('foto', inputProfile.fileProfile);
    }
    let res = await updateProfile(formData);
    if(res.status === 'success'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        eventBus.emit('showGreenPopup', res.message);
    }else if(res.status === 'error'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        eventBus.emit('showRedPopup', res.message);
    }
}
const updatePasswordForm = async() => {
    if(local.isRequestInProgress) return;
    let errMessage = '';
    if(inputResetPass.pass_baru === null || inputResetPass.pass_baru === ''){
        inpPassBaru.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpPassBaru.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if(!errMessage) errMessage = 'Password Harus diisi !';
    }else{
        if (inputResetPass.pass_baru.length < 8) {
            inpPassBaru.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassBaru.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if (!errMessage) errMessage = 'Password minimal 8 karakter !';
        }
        if (!/[A-Z]/.test(inputResetPass.pass_baru)) {
            inpPassBaru.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassBaru.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password minimal ada 1 huruf kapital !';
        }
        if (!/[a-z]/.test(inputResetPass.pass_baru)) {
            inpPassBaru.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassBaru.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password minimal ada 1 huruf kecil !';
        }
        if (!/\d/.test(inputResetPass.pass_baru)) {
            inpPassBaru.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassBaru.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password minimal ada 1 angka !';
        }
        if (!/[!@#$%^&*]/.test(inputResetPass.pass_baru)) {
            inpPassBaru.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassBaru.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password minimal ada 1 karakter unik !';
        }
    }
    if(inputResetPass.pass_baru_ulangi === null || inputResetPass.pass_baru_ulangi === ''){
        inpPassBaruUlangi.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpPassBaruUlangi.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if (!errMessage) errMessage = 'Ulangi Password Harus diisi !';
    }else{
        if (inputResetPass.pass_baru_ulangi.length < 8) {
            inpPassBaruUlangi.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassBaruUlangi.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password konfirmasi minimal 8 karakter !';
        }
        if (!/[A-Z]/.test(inputResetPass.pass_baru_ulangi)) {
            inpPassBaruUlangi.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassBaruUlangi.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password konfirmasi minimal ada 1 huruf kapital !';
        }
        if (!/[a-z]/.test(inputResetPass.pass_baru_ulangi)) {
            inpPassBaruUlangi.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassBaruUlangi.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password konfirmasi minimal ada 1 huruf kecil !';
        }
        if (!/\d/.test(inputResetPass.pass_baru_ulangi)) {
            inpPassBaruUlangi.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassBaruUlangi.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password konfirmasi minimal ada 1 angka !';
        }
        if (!/[!@#$%^&*]/.test(inputResetPass.pass_baru_ulangi)) {
            if(!errMessage) errMessage = 'Password konfirmasi minimal ada 1 karakter unik !';
        }
    }
    if(!(inputResetPass.pass_baru === null || inputResetPass.pass_baru === '') && !(inputResetPass.pass_baru === null || inputResetPass.pass_baru === '')){
        if(inputResetPass.pass_baru != inputResetPass.pass_baru_ulangi){
            inpPassBaru.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassBaru.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpPassBaruUlangi.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassBaruUlangi.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password harus sama !';
        }
    }
    local.isRequestInProgress = true;
    eventBus.emit('showLoading');
    let res = await updatePassword({password_old:inputResetPass.pass_lama, password: inputResetPass.pass_baru, password_ulangi: inputResetPass.pass_baru_ulangi});
    if(res.status === 'success'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        eventBus.emit('showGreenPopup', res.message);
        setTimeout(function(){
            inputResetPass.pass_lama = '';
            inputResetPass.pass_baru = '';
            inputResetPass.pass_baru_ulangi = '';
        }, 1000);
    }else if(res.status === 'error'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        eventBus.emit('showRedPopup', res.message);
    }
}
</script>