<template>
    <div class="bg-primary1 h-screen flex items-center justify-center">
        <section class="relative flex flex-col items-center 2xl:w-250 xl:w-200 lg:w-150 md:w-150 sm:w-30 bg-white rounded-3xl">
            <h1 class="relative w-2/4 top-5 2xl:text-6xl xl:text-4xl lg:text-4xl md:text-2xl sm:text-2xl text-center">Belanja jadi mudah juga dimana saja </h1>
            <div ref="popup" class="popup invisible relative flex items-center justify-center bg-popup_error mt-5 md:top-5 xl:w-70 lg:w-80 md:w-50 xl:h-10 lg:h-10 mdh-7 rounded-2xl">
                <p class="text-white text-3xl xl:text-xl lg:text-2xl md:text-xl sm:text-xl">{{ local.errMessage }}</p>
            </div>
            <form v-if="local.conOTP === 'lupa_password'" class="w-full relative flex flex-col justify-center items-center mb-5">
                <div class="row relative mt-4 flex flex-col w-1/2">
                    <label class="relative left-3 xl:text-2xl lg:text-xl md:text-md sm:text-lg font-semibold">Email</label>
                    <input ref="inpEmail" type="text" class="relative top-1  border-black lg:border-2 md:border-1 hover:border-4 md:hover:border-2 focus:outline-none rounded-xl w-full xl:h-9 lg:h-8 md:h-7 sm:h-5 pl-5 font-medium 2xl:text-2xl xl:text-xl lg:text-2xl md:text-xl sm:text-lg" @input="inpChange('email')" v-model="input.email">
                </div>
                <div class="row relative mt-4 flex flex-row items-center justify-center w-7/8 text-3xl xl:text-2xl lg:text-xl md:text-lg sm:text-base text-white">
                    <button class="relative mt-3 2xl:w-80 xl:w-75 md:w-40 sm:w-20 2xl:h-12 xl:h-9 lg:h-8 md:h-7 sm:h-6 bg-bold rounded-2xl" @click.prevent="forgotPassForm">Kirim Lupa Password</button>
                </div>
                <span class="relative mt-3 2xl:text-3xl xl:text-xl lg:text-lg md:text-base sm:text-base text-gray-900">Sudah punya akun ? 
                    <router-link to="/login" class="hover:text-red-700 font-medium">Masuk sekarang</router-link>
                </span>
            </form>
            <!-- @change-popup="inpChangeOTP" -->
            <OTPComponent v-if="local.conOTP === 'verify'" 
                :data="getConOTP()"
                :timer="getTimer()"
                @red-popup="showOTPRedPopup"
                @green-popup="successOTP"
                @countdown="startCountdown">
            </OTPComponent>
            <form v-if="local.conOTP === 'ganti_password'" class="w-full relative flex flex-col justify-center items-center">
                <div class="row relative mt-4 flex flex-col w-7/8">
                    <label class="relative left-3 xl:text-xl lg:text-xl md:text-md sm:text-lg">Password</label>
                    <div class="relative flex items-center top-1">
                        <input ref="inpPassword" type="password" class="relative border-black lg:border-2 md:border-1 hover:border-4 md:hover:border-2 focus:outline-none rounded-xl w-full xl:h-9 lg:h-8 md:h-7 sm:h-5 pl-5 font-medium 2xl:text-2xl xl:text-xl lg:text-2xl md:text-xl sm:text-lg" @input="inpChange('password')" v-model="input.password">
                        <div class="eye absolute top-1/2 -translate-y-1/2 right-3 cursor-pointer" @click="showPass">
                            <img :src="publicConfig.baseURL + '/img/login/eye-slash.svg'" alt="" class="xl:w-8 lg:w-7" :class="input.password === '' || (input.password !== '' && input.isPasswordShow === true) ? 'hidden' : ''">
                            <img :src="publicConfig.baseURL + '/img/login/eye.svg'" alt="" class="xl:w-8 lg:w-7" :class="input.password === '' || (input.password !== '' && input.isPasswordShow === false) ? 'hidden' : ''">
                        </div>
                    </div>
                </div>
                <div class="row relative mt-4 flex flex-col w-7/8">
                    <label class="relative left-3 xl:text-xl lg:text-xl md:text-md sm:text-lg">Ulangi Password</label>
                    <div class="relative flex items-center top-1">
                        <input ref="inpUlangiPassword" type="password" class="relative border-black lg:border-2 md:border-1 hover:border-4 md:hover:border-2 focus:outline-none rounded-xl w-full xl:h-9 lg:h-8 md:h-7 sm:h-5 pl-5 font-medium 2xl:text-2xl xl:text-xl lg:text-2xl md:text-xl sm:text-lg" @input="inpChange('password_ulangi')" v-model="input.ulangiPassword">
                        <div class="eye absolute top-1/2 -translate-y-1/2 right-3 cursor-pointer" @click="showUlangiPass">
                            <img :src="publicConfig.baseURL + '/img/login/eye-slash.svg'" alt="" class="xl:w-8 lg:w-7" :class="input.ulangiPassword === '' || (input.ulangiPassword !== '' && input.isUlangiPasswordShow === true) ? 'hidden' : ''">
                            <img :src="publicConfig.baseURL + '/img/login/eye.svg'" alt="" class="xl:w-8 lg:w-7" :class="input.ulangiPassword === '' || (input.ulangiPassword !== '' && input.isUlangiPasswordShow === false) ? 'hidden' : ''">
                        </div>
                    </div>
                </div>
                <div class="row relative mt-4 flex flex-row items-center justify-center w-7/8 text-3xl xl:text-2xl lg:text-xl md:text-lg sm:text-base text-white">
                    <button class="relative mt-3 2xl:w-60 xl:w-70 md:w-40 sm:w-20 2xl:h-12 xl:h-9 lg:h-8 md:h-7 sm:h-6 bg-bold rounded-2xl" @click.prevent="verifyChangeForm">
                        Ganti Password
                    </button>
                </div>
            </form>
        </section>
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
import { CreateForgotPassword, VerifyChange  } from '~/composables/api/auth';
const publicConfig = useRuntimeConfig().public;
useHead({
    title:`Reset Password | ${publicConfig.appName}`
});
definePageMeta({
    layout:'default',
});
const local = reactive({
    isRequestInProgress: false,
    conOTP: 'lupa_password',
    errMessage: '',
    timer: null as NodeJS.Timeout | null,
    timerMenit: 0,
    timerDetik: 0,
});
const input = reactive({
    email: '',
    nama: '',
    password: '',
    otp: '',
    ulangiPassword: '',
    isPasswordShow: false,
    isUlangiPasswordShow: false,
});
const popup: Ref = ref(null);
const inpEmail: Ref = ref(null);
const inpPassword: Ref = ref(null);
const inpUlangiPassword: Ref = ref(null);
const getConOTP = () => {
    return {'email':input.email,'condition':'password'};
};
const showOTPRedPopup = (message: string) => {
    popup.value.classList.remove('invisible');
    local.errMessage = message;
};
const successOTP = (data: {otp?: string, cond: string}) => {
    if(data.cond == 'refPassword' && data.otp){
        local.conOTP = 'ganti_password';
        input.otp = data.otp;
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
    if(div == 'email'){
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
const getTimer = () => {
    return {
        timer: local.timer,
        timerMenit: local.timerMenit,
        timerDetik: local.timerDetik,
    };
};
const startCountdown = (waktu: number) => {
    local.timer = setInterval(function(){
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
//create forgot password
const forgotPassForm = async (event: Event) => {
    event.preventDefault();
    if(local.isRequestInProgress)  return;
    if(input.email === null || input.email === ''){
        inpEmail.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpEmail.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        popup.value.classList.remove('invisible');
        if(!local.errMessage) local.errMessage = 'Email Harus diisi !';
        return;
    }
    local.isRequestInProgress = true;
    eventBus.emit('showLoading');
    let forgotPass = await CreateForgotPassword({email: input.email});
    if(forgotPass.status === 'success'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        startCountdown(new Date(forgotPass.data.waktu).getTime());
        eventBus.emit('showGreenPopup', forgotPass.message);
        local.conOTP = 'verify';
    }else if(forgotPass.status === 'error'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        popup.value.classList.remove('invisible');
        local.errMessage = forgotPass.message;
    }
};
//change password
const verifyChangeForm = async (event: Event) => {
    event.preventDefault();
    if(local.isRequestInProgress) return;
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
        if(!local.errMessage) local.errMessage = 'Password baru Harus diisi !';
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
            inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
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
    let verifyChange = await VerifyChange({nama:input.nama, email: input.email, code: input.otp, password: input.password, ulangiPassword:input.ulangiPassword});
    if(verifyChange.status === 'success'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        eventBus.emit('showGreenPopup', verifyChange.message);
        setTimeout(function(){
            navigateTo('/login');
        }, 2000);
    }else if(verifyChange.status === 'error'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        popup.value.classList.remove('invisible');
        local.errMessage = verifyChange.message;
    }
};
</script>