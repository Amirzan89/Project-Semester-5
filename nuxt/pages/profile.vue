<template>
    <template v-if="local.isDoneFetch">
        <div>
            <div>
                //
            </div>
            <div>
                //
            </div>
            <div>
                //
            </div>
        </div>
    </template>
    <template v-else>
        <div>
        </div>
    </template>
</template>
<style scoped lang="scss"></style>
<script setup lang="ts">
import { eventBus } from '~/app/eventBus';
import { useFetchDataStore } from '~/store/FetchData';
const publicConfig = useRuntimeConfig().public;
definePageMeta({
    name: 'Profile',
    layout: 'authenticated',
});
useHead({
    title:`Profile | ${publicConfig.appName}`
});
const local = reactive({
    isDoneFetch: false,
    fetchedUserAuth: null,
    fetchedViewData: null,
});
const input = reactive({
    nama_profile:'',
    email:'',
    jenis_kelamin:'',
    no_telpon:'',
    kode_pos:'',
    kota:'',
    provinsi:'',
    negara:'',
    alamat:'',
    linkFileProfile: '',
    fileProfile:null,
});
const inputResetPass = reactive({
    pass_lama:'',
    pass_baru:'',
    pass_baru_ulangi:'',
    isPasswordLamaShow:false,
    isPasswordBaruShow:false,
    isPasswordBaruUlangiShow:false,
});
const inpNamaProfile = ref(null);
const inpEmail = ref(null);
const inpJenisKelamin = ref(null);
const inpNoTelpon = ref(null);
const inpKodePos = ref(null);
const inpKota = ref(null);
const inpProvinsi = ref(null);
const inpNegara = ref(null);
const inpAlamat = ref(null);
const inpPassLama = ref(null);
const inpPassBaru = ref(null);
const inpPassBaruUlangi = ref(null);
const fileInputProfile = ref(null);
const fileInputShop = ref(null);
useAsyncData(async() => {
    const res = await useFetchDataStore().fetchData();
    if(res.status == 'error'){
        //
    }
});
const inpChange = (div: string) => {
    switch(div){
        case 'email':
            inpEmail.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpEmail.value.classList.add('border-black','hover:border-black','focus:border-black');
        break;
        case 'description':
            inpDescription.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpDescription.value.classList.add('border-black','hover:border-black','focus:border-black');
        break;
        case 'version':
            inpVersion.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpVersion.value.classList.add('border-black','hover:border-black','focus:border-black');
        break;
        case 'release_date':
            inpReleaseDate.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpReleaseDate.value.classList.add('border-black','hover:border-black','focus:border-black');
        break;
        case 'device':
            inpDevice.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpDevice.value.classList.add('border-black','hover:border-black','focus:border-black');
        break;
    }
};
const updateProfile = async (event: Event) => {
    event.preventDefault();
    let errMessage = '';
    if(input.nama === null || input.nama === ''){
        inpNama.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpNama.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        errMessage = 'Nama lengkap Harus diisi !';
    }
    if(input.email === null || input.email === ''){
        inpEmail.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpEmail.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        errMessage = 'Email Harus diisi !';
    }else{
        if (!isValidEmail(input.email)) {
            inpEmail.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpEmail.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            errMessage = 'Masukkan email dengan benar !';
        }
    }
    if(input.password === null || input.password === ''){
        inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        errMessage = 'Password Harus diisi !';
    }else{
        if (input.password.length < 8) {
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            errMessage = 'Password minimal 8 karakter !';
        }
        if (!/[A-Z]/.test(input.password)) {
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            errMessage = 'Password minimal ada 1 huruf kapital !';
        }
        if (!/[a-z]/.test(input.password)) {
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            errMessage = 'Password minimal ada 1 huruf kecil !';
        }
        if (!/\d/.test(input.password)) {
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            errMessage = 'Password minimal ada 1 angka !';
        }
        if (!/[!@#$%^&*]/.test(input.password)) {
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            errMessage = 'Password minimal ada 1 karakter unik !';
        }
    }
    if(input.ulangiPassword === null || input.ulangiPassword === ''){
        inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        errMessage = 'Ulangi Password Harus diisi !';
    }else{
        if (input.ulangiPassword.length < 8) {
            inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            errMessage = 'Password konfirmasi minimal 8 karakter !';
        }
        if (!/[A-Z]/.test(input.ulangiPassword)) {
            inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            errMessage = 'Password konfirmasi minimal ada 1 huruf kapital !';
        }
        if (!/[a-z]/.test(input.ulangiPassword)) {
            inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            errMessage = 'Password konfirmasi minimal ada 1 huruf kecil !';
        }
        if (!/\d/.test(input.ulangiPassword)) {
            inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            errMessage = 'Password konfirmasi minimal ada 1 angka !';
        }
        if (!/[!@#$%^&*]/.test(input.ulangiPassword)) {
            errMessage = 'Password konfirmasi minimal ada 1 karakter unik !';
        }
    }
    if(!(input.password === null || input.password === '') && !(input.password === null || input.password === '')){
        if(input.password != input.ulangiPassword){
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            errMessage = 'Password harus sama !';
        }
    }
    if(errMessage != ''){
        popup.value.classList.remove('invisible');
        return;
    }
    eventBus.emit('showLoading');
    let register = await updateP({nama:input.nama, email: input.email, password: input.password, ulangiPassword:input.ulangiPassword});
    if(register.status === 'success'){
        eventBus.emit('closeLoading');
        eventBus.emit('showGreenPopup', register.message);
    }else if(register.status === 'error'){
        eventBus.emit('closeLoading');
        eventBus.emit('showRedPopup', res.message);
    }
}
</script>