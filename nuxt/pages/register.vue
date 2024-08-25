<template>
    <div class="bg-primary1 h-screen flex items-center justify-center" id="root">
        <template v-if="local.conOTP == 'register'">
            <div class="relative flex flex-col items-center 2xl:w-250 xl:w-200 lg:w-150 md:w-150 sm:w-30 bg-white rounded-3xl">
                <h1 class="relative w-2/4 top-5 2xl:text-6xl xl:text-4xl lg:text-4xl md:text-2xl sm:text-2xl text-center">Selamat datang</h1>
                <div ref="popup" class="invisible relative flex items-center justify-center bg-popup_error mt-5 md:top-5 xl:w-70 lg:w-80 md:w-50 xl:h-10 lg:h-10 mdh-7 rounded-2xl">
                    <p class="text-white text-3xl xl:text-xl lg:text-2xl md:text-xl sm:text-xl">{{ local.errMessage }}</p>
                </div>
                <div class="row relative mt-5 flex flex-col w-7/8">
                    <label class="relative left-3 xl:text-2xl lg:text-xl md:text-md sm:text-lg">Nama</label>
                    <input ref="inpNama" type="text" class="relative top-1 border-black lg:border-2 md:border-1 hover:border-4 md:hover:border-2 focus:outline-none rounded-xl w-full xl:h-10 lg:h-8 md:h-7 sm:h-5 pl-3 font-medium 2xl:text-2xl xl:text-2xl lg:text-2xl md:text-xl sm:text-lg" @input="inpChange('email')" v-model="input.nama">
                </div>
                <div class="row relative mt-5 flex flex-col w-7/8">
                    <label class="relative left-3 xl:text-2xl lg:text-xl md:text-md sm:text-lg">Email</label>
                    <input ref="inpEmail" type="email" class="relative top-1 border-black lg:border-2 md:border-1 hover:border-4 md:hover:border-2 focus:outline-none rounded-xl w-full xl:h-10 lg:h-8 md:h-7 sm:h-5 pl-3 font-medium 2xl:text-2xl xl:text-2xl lg:text-2xl md:text-xl sm:text-lg" @input="inpChange('email')" v-model="input.email">
                </div>
                <div class="row relative mt-5 flex flex-col w-7/8">
                    <label class="relative left-3 xl:text-2xl lg:text-xl md:text-md sm:text-lg">Password</label>
                    <div class="relative flex items-center top-1">
                        <input ref="inpPassword" type="password" class="relative border-black lg:border-2 md:border-1 hover:border-4 md:hover:border-2 focus:outline-none rounded-xl w-full xl:h-10 lg:h-8 md:h-7 sm:h-5 pl-3 font-medium 2xl:text-2xl xl:text-2xl lg:text-2xl md:text-xl sm:text-lg pr-8" @input="inpChange('password')" v-model="input.password">
                        <div class="eye absolute top-1/2 -translate-y-1/2 right-3 cursor-pointer" @click="showPass">
                            <img src="~assets/icon/eye-slash.svg" alt="" class="xl:!w-8 lg:!w-7" :class="input.password === '' || (input.password !== '' && input.isPasswordShow) ? 'hidden' : ''">
                            <img src="~assets/icon/eye.svg" alt="" class="xl:!w-8 lg:!w-7" :class="input.password === '' || (input.password !== '' && !input.isPasswordShow) ? 'hidden' : ''">
                        </div>
                    </div>
                </div>
                <div class="row relative mt-5 flex flex-col w-7/8">
                    <label class="relative left-3 xl:text-2xl lg:text-xl md:text-md sm:text-lg">Ulangi Password</label>
                    <div class="relative flex items-center top-1">
                        <input ref="inpUlangiPassword" type="password" class="relative border-black lg:border-2 md:border-1 hover:border-4 md:hover:border-2 focus:outline-none rounded-xl w-full xl:h-10 lg:h-8 md:h-7 sm:h-5 pl-3 font-medium 2xl:text-2xl xl:text-2xl lg:text-2xl md:text-xl sm:text-lg pr-8" @input="inpChange('password_ulangi')" v-model="input.ulangiPassword">
                        <div class="eye absolute top-1/2 -translate-y-1/2 right-3 cursor-pointer" @click="showUlangiPass">
                            <img src="~assets/icon/eye-slash.svg" alt="" class="xl:!w-8 lg:!w-7" :class="input.ulangiPassword === '' || (input.ulangiPassword !== '' && input.isUlangiPasswordShow) ? 'hidden' : ''">
                            <img src="~assets/icon/eye.svg" alt="" class="xl:!w-8 lg:!w-7" :class="input.ulangiPassword === '' || (input.ulangiPassword !== '' && !input.isUlangiPasswordShow) ? 'hidden' : ''">
                        </div>
                    </div>
                </div>
                <div class="row relative mt-5 flex flex-row items-center w-4/5 text-3xl xl:text-2xl lg:text-xl md:text-lg sm:text-base">
                    <div class="col-12 md:col-4">
                        <div class="field-checkbox mb-0">
                            <Checkbox id="checkOption1" name="option" value="simpan" v-model="input.checkboxValue" />
                            <label for="checkOption1">Simpan</label>
                        </div>
                    </div>
                    <router-link to="/password/reset" class="absolute right-0 2xl:text-2xl xl:text-xl lg:text-md md:text-sm sm:text-lg hover:text-red-600 font-medium">Lupa Password ?</router-link>
                </div>
                <button class="relative mt-3 2xl:w-80 xl:w-70 md:w-40 sm:w-20 2xl:h-12 xl:h-10 lg:h-8 md:h-7 sm:h-6 bg-bold rounded-2xl 2xl:text-3xl xl:text-2xl lg:text-xl md:text-md sm:text-base text-white" @click.prevent="registerForm">Daftar</button>
                <a href="/auth/redirect" class="relative mt-3 2xl:w-80 xl:w-70 md:w-40 sm:w-20 2xl:h-12 xl:h-10 lg:h-8 md:h-7 sm:h-6 bg-bold rounded-2xl flex items-center justify-evenly text-white">
                    <img src="~assets/icon/google.svg" class="2xl:w-8 xl:!w-7 !lg:w-5 md:!w-4 sm:!w-3">
                    <p class="2xl:text-2xl xl:text-xl lg:text-md md:text-xs sm:text-sm">Daftar dengan google</p>
                </a>
                <span class="relative mt-3 2xl:text-3xl xl:text-xl lg:text-lg md:text-base sm:text-base text-gray-900 mb-5">Sudah Punya Akun ? 
                    <router-link to="/login" class="hover:text-red-700 font-medium">Masuk sekarang</router-link>
                </span>
            </div>
        </template>
        <!-- @change-popup="inpChangeOTP" -->
        <OTPComponent v-if="local.conOTP == 'verify'" 
            :data="getConOTP()"
            :timer="getTimer()"
            @red-popup="showOTPRedPopup"
            @green-popup="successOTP"
            @countdown="startCountdown">
        </OTPComponent>
    </div>
</template>
<style scoped lang="scss">
.fade-out{
    animation: fadeOut 0.75s ease forwards;
}
@keyframes fadeOut {
    0% {
        opacity: 1;
    }
    100% {
        opacity: 0;
        display: none;
    }
}
</style>
<script setup lang="ts">
import { ref, reactive } from "vue";
import { eventBus } from '~/app/eventBus';
import OTPComponent from '~/components/OTP.vue';
import { Register } from '~/composables/api/auth';
import useGoogleLoginTap from "~/composables/GooleLogin";
const publicConfig = useRuntimeConfig().public;
definePageMeta({
    name: 'Register',
    layout: 'default',
})
useHead({
    title:`Register | ${publicConfig.appName}`,
    script:[
        {
            src:'https://accounts.google.com/gsi/client',
            async: true,
            defer: true,
            onload: () => {
                if(local.conOTP == 'register') useGoogleLoginTap().initializeGoogleOneTap;
            },
        },
    ]
});
const local = reactive({
    isRequestInProgress: false,
    conOTP: 'register',
    errMessage: '',
    timer: null as NodeJS.Timeout | null,
    timerMenit: 0 as number,
    timerDetik: 0 as number,
});
const input = reactive({
    nama: '',
    email:'',
    password:'',
    ulangiPassword: '',
    isPasswordShow:false,
    isUlangiPasswordShow: false,
    checkboxValue:'',
});
const popup: Ref = ref(null);
const inpNama: Ref = ref(null);
const inpEmail: Ref = ref(null);
const inpPassword:Ref = ref(null);
const inpUlangiPassword: Ref = ref(null);
onMounted(() => {
    // initializeGoogleOneTap();
});
const getConOTP = () => {
    return {'email':input.email,'condition':'email'};
};
const showOTPRedPopup = (message: string) => {
    popup.value.classList.remove('invisible');
    local.errMessage = message;
};
const successOTP = (data: any) => {
    if(data.cond == 'verifyEmail'){
        setTimeout(function(){
            navigateTo('/login');
        }, 2000);
    }
};
// const inpChangeOTP = () => {
//     if(!popup.value.classList.contains('invisible')){
//         popup.value.classList.add('fade-out');
//         setTimeout(function(){
//             popup.value.classList.remove('fade-out');
//             popup.value.classList.add('invisible');
//         }, 750);
//     }
// };
const getTimer = () => {
    return {
        timer:local.timer,
        timerMenit:local.timerMenit,
        timerDetik:local.timerDetik,
    }
};
const startCountdown = (waktu: number) =>{
    local.timer = setInterval(() => {
        var now = new Date().getTime();
        var distance = waktu - now;
        // Calculate time remaining
        local.timerMenit = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        local.timerDetik = Math.floor((distance % (1000 * 60)) / 1000);
        if (distance < 0) {
            if(local.timer !== null){
                clearInterval(local.timer);
                local.timer = null;
            }
        }
    }, 1000);
};
const showPass = () => {
    if(input.isPasswordShow){
        inpPassword.value.type = 'password';
        input.isPasswordShow = false;
    }else{
        inpPassword.value.type = 'text';
        input.isPasswordShow = true;
    }
};
const showUlangiPass = () => {
    if(input.isUlangiPasswordShow){
        inpUlangiPassword.value.type = 'password';
        input.isUlangiPasswordShow = false;
    }else{
        inpUlangiPassword.value.type = 'text';
        input.isUlangiPasswordShow = true;
    }
};
const inpChange = (div: string) => {
    if(!popup.value.classList.contains('invisible')){
        popup.value.classList.add('fade-out');
        setTimeout(function(){
            popup.value.classList.remove('fade-out');
        }, 750);
        popup.value.classList.add('invisible');
    }
    local.errMessage = '';
    if(div == 'nama'){
        inpNama.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        inpNama.value.classList.add('border-black','hover:border-black','focus:border-black');
    }else if(div == 'email'){
        inpEmail.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        inpEmail.value.classList.add('border-black','hover:border-black','focus:border-black');
    }else if(div == 'password'){
        inpPassword.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        inpPassword.value.classList.add('border-black','hover:border-black','focus:border-black');
    }else if(div == 'password_ulangi'){
        inpUlangiPassword.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        inpUlangiPassword.value.classList.add('border-black','hover:border-black','focus:border-black');
    }
};
const isValidEmail = (email: string) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
};
const registerForm = async(event: Event) => {
    event.preventDefault();
    if(local.isRequestInProgress) return;
    if(input.nama === null || input.nama === ''){
        inpNama.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpNama.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if(!local.errMessage) local.errMessage = 'Nama lengkap Harus diisi !';
    }
    if(input.email === null || input.email === ''){
        inpEmail.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpEmail.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if(!local.errMessage) local.errMessage = 'Email Harus diisi !';
    }else{
        if (!isValidEmail(input.email)) {
            inpEmail.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpEmail.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!local.errMessage) local.errMessage = 'Masukkan email dengan benar !';
        }
    }
    if(input.password === null || input.password === ''){
        inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if(!local.errMessage) local.errMessage = 'Password Harus diisi !';
    }else{
        if (input.password.length < 8) {
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!local.errMessage) local.errMessage = 'Password minimal 8 karakter !';
        }
        if (!/[A-Z]/.test(input.password)) {
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!local.errMessage) local.errMessage = 'Password minimal ada 1 huruf kapital !';
        }
        if (!/[a-z]/.test(input.password)) {
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!local.errMessage) local.errMessage = 'Password minimal ada 1 huruf kecil !';
        }
        if (!/\d/.test(input.password)) {
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!local.errMessage) local.errMessage = 'Password minimal ada 1 angka !';
        }
        if (!/[!@#$%^&*]/.test(input.password)) {
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!local.errMessage) local.errMessage = 'Password minimal ada 1 karakter unik !';
        }
    }
    if(input.ulangiPassword === null || input.ulangiPassword === ''){
        inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if(!local.errMessage) local.errMessage = 'Ulangi Password Harus diisi !';
    }else{
        if (input.ulangiPassword.length < 8) {
            inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!local.errMessage) local.errMessage = 'Password konfirmasi minimal 8 karakter !';
        }
        if (!/[A-Z]/.test(input.ulangiPassword)) {
            inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!local.errMessage) local.errMessage = 'Password konfirmasi minimal ada 1 huruf kapital !';
        }
        if (!/[a-z]/.test(input.ulangiPassword)) {
            inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!local.errMessage) local.errMessage = 'Password konfirmasi minimal ada 1 huruf kecil !';
        }
        if (!/\d/.test(input.ulangiPassword)) {
            inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!local.errMessage) local.errMessage = 'Password konfirmasi minimal ada 1 angka !';
        }
        if (!/[!@#$%^&*]/.test(input.ulangiPassword)) {
            if(!local.errMessage) local.errMessage = 'Password konfirmasi minimal ada 1 karakter unik !';
        }
    }
    if(!(input.password === null || input.password === '') && !(input.password === null || input.password === '')){
        if(input.password != input.ulangiPassword){
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!local.errMessage) local.errMessage = 'Password harus sama !';
        }
    }
    if(local.errMessage != ''){
        popup.value.classList.remove('invisible');
        return;
    }
    local.isRequestInProgress = true;
    eventBus.emit('showLoading');
    let register = await Register({nama:input.nama, email: input.email, password: input.password, ulangiPassword:input.ulangiPassword});
    if(register.status === 'success'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        startCountdown(new Date(register.data.waktu).getTime());
        eventBus.emit('showGreenPopup', register.message);
        local.conOTP = 'verify';
    }else if(register.status === 'error'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        popup.value.classList.remove('invisible');
        local.errMessage = register.message;
    }
};
</script>