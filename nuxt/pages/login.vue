<template>
    <div class="bg-primary1 h-screen flex items-center justify-center" id="root">
        <form class="relative flex flex-col items-center 2xl:w-250 xl:w-200 lg:w-150 md:w-150 sm:w-30 bg-white rounded-3xl">
            <h1 class="relative w-2/4 top-5 2xl:text-6xl xl:text-4xl lg:text-4xl md:text-2xl sm:text-2xl text-center">Selamat datang</h1>
            <div ref="popup" class="invisible relative flex items-center justify-center bg-popup_error mt-5 md:top-5 xl:w-70 lg:w-80 md:w-50 xl:h-10 lg:h-10 mdh-7 rounded-2xl">
                <p class="text-white text-3xl xl:text-xl lg:text-2xl md:text-xl sm:text-xl">{{ errMessage }}</p>
            </div>
            <div class="row relative mt-5 flex flex-col w-7/8">
                <label class="relative left-3 xl:text-2xl lg:text-xl md:text-md sm:text-lg">Email</label>
                <input ref="inpEmail" type="email" class="relative top-1 border-black lg:border-2 md:border-1 hover:border-4 md:hover:border-2 focus:outline-none rounded-xl w-full xl:h-10 lg:h-8 md:h-7 sm:h-5 pl-3 font-medium 2xl:text-2xl xl:text-2xl lg:text-2xl md:text-xl sm:text-lg" required @input="inpChange('email')" v-model="input.email">
            </div>
            <div class="row relative mt-5 flex flex-col w-7/8">
                <label class="relative left-3 xl:text-2xl lg:text-xl md:text-md sm:text-lg">Password</label>
                <div class="relative flex items-center top-1">
                    <input ref="inpPassword" type="password" class="relative border-black lg:border-2 md:border-1 hover:border-4 md:hover:border-2 focus:outline-none rounded-xl w-full xl:h-10 lg:h-8 md:h-7 sm:h-5 pl-3 font-medium 2xl:text-2xl xl:text-2xl lg:text-2xl md:text-xl sm:text-lg pr-8" required @input="inpChange('password')" v-model="input.password">
                    <div class="eye absolute top-1/2 -translate-y-1/2 right-3 cursor-pointer" @click="showPass">
                        <img src="~assets/icon/eye-slash.svg" alt="" class="xl:!w-8 lg:!w-7" :class="input.password === '' || (input.password !== '' && input.isPasswordShow) ? 'hidden' : ''">
                        <img src="~assets/icon/eye.svg" alt="" class="xl:!w-8 lg:!w-7" :class="input.password === '' || (input.password !== '' && !input.isPasswordShow) ? 'hidden' : ''">
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
                <NuxtLink to="/password/reset" class="absolute right-0 2xl:text-2xl xl:text-xl lg:text-md md:text-sm sm:text-lg hover:text-red-600 font-medium">Lupa Password ?</NuxtLink>
            </div>
            <button class="relative mt-3 2xl:w-80 xl:w-70 md:w-40 sm:w-20 2xl:h-12 xl:h-10 lg:h-8 md:h-7 sm:h-6 bg-bold rounded-2xl 2xl:text-3xl xl:text-2xl lg:text-xl md:text-md sm:text-base text-white bg-black" @click.prevent="loginForm">Masuk</button>
            <a :href="`${publicConfig.baseURL}/auth/redirect`" class="relative mt-3 2xl:w-80 xl:w-70 md:w-40 sm:w-20 2xl:h-12 xl:h-10 lg:h-8 md:h-7 sm:h-6 bg-bold rounded-2xl flex items-center justify-evenly text-white">
                <img src="~assets/icon/google.svg" class="2xl:w-8 xl:!w-7 !lg:w-5 md:!w-4 sm:!w-3">
                <p class="2xl:text-2xl xl:text-xl lg:text-md md:text-xs sm:text-sm">Masuk dengan google</p>
            </a>
            <span class="relative mt-3 2xl:text-3xl xl:text-xl lg:text-lg md:text-base sm:text-base text-gray-900 mb-5">Tidak punya akun ? 
                <NuxtLink to="/register" class="hover:text-red-700 font-medium">Daftar sekarang</NuxtLink>
            </span>
        </form>
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
import Cookies from 'js-cookie'
import { ref, reactive } from "vue";
import { eventBus } from '~/app/eventBus';
import { Login } from '~/composables/api/auth';
import useGoogleLoginTap from "~/composables/GooleLogin";
import useGoogleRecaptcha from '~/composables/recaptcha';
const { executeRecaptcha } = useGoogleRecaptcha();
const publicConfig = useRuntimeConfig().public;
definePageMeta({
    name: 'Login',
    layout: 'default',
});
useHead({
    title:`Login | ${publicConfig.appName}`,
    script:[
        {
            src:'https://accounts.google.com/gsi/client',
            async: true,
            defer: true,
            onload: useGoogleLoginTap().initializeGoogleOneTap,
        },
        {
            src:`https://www.google.com/recaptcha/api.js?render=${publicConfig.recaptchaKey}`,
            onload: () => {
                local.isRRecaptcha = true
            },
        },
    ]
});
const errMessage = ref('');
const local = reactive({
    isRRecaptcha: false,
    isRequestInProgress: false,
});
const input = reactive({
    email:'',
    password:'',
    isPasswordShow:false,
    checkboxValue: '',
});
const popup: Ref = ref(null);
const inpEmail: Ref = ref(null);
const inpPassword: Ref = ref(null);
useAsyncData(async () => {
    let cookieData = Cookies.get('__INITIAL_COSTUM_STATE__');
    if (cookieData) {
        try {
            const decodedData = atob(cookieData);
            const parsedData = JSON.parse(decodedData);
            console.log('isi ', parsedData);
            // eventBus.emit('showGreenPopup', login.);
        } catch (error) {
            console.error('Error parsing cookie data:', error);
        } finally {
            Cookies.remove('__INITIAL_COSTUM_STATE__');
        }
    }
});
const showPass = () => {
    if(input.isPasswordShow){
        inpPassword.value.type = 'password';
        input.isPasswordShow = false;
    }else{
        inpPassword.value.type = 'text';
        input.isPasswordShow = true;
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
    errMessage.value = '';
    if(div == 'email'){
        inpEmail.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        inpEmail.value.classList.add('border-black','hover:border-black','focus:border-black');
    }else if(div == 'password'){
        inpPassword.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        inpPassword.value.classList.add('border-black','hover:border-black','focus:border-black');
    }
};
const isValidEmail = (email: string) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
};
const loginForm = async (event: Event) => {
    try{
        event.preventDefault();
        if(!local.isRRecaptcha) return;
        if(local.isRequestInProgress) return;
        local.isRequestInProgress = true;
        const token: string =  await executeRecaptcha();
        if(input.email === null || input.email === ''){
            inpEmail.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpEmail.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage.value) errMessage.value = 'Email Harus diisi !';
        }else{
            if (!isValidEmail(input.email)) {
                inpEmail.value.classList.remove('border-black','hover:border-black','focus:border-black');
                inpEmail.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
                if(!errMessage.value) errMessage.value = 'Masukkan email dengan benar !';
            }
        }
        if(input.password === null || input.password === ''){
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage.value) errMessage.value = 'Password Harus diisi !';
        }
        if(errMessage.value != ''){
            popup.value.classList.remove('invisible');
            return;
        }
        eventBus.emit('showLoading');
        let login = await Login({email: input.email, password: input.password, recaptcha: token});
        if(login.status === 'success'){
            local.isRequestInProgress = false;
            eventBus.emit('closeLoading');
            eventBus.emit('showGreenPopup', login.message);
            setTimeout(function(){
                navigateTo('/dashboard');
            }, 1500);
        }else if(login.status === 'error'){
            local.isRequestInProgress = false;
            eventBus.emit('closeLoading');
            popup.value.classList.remove('invisible');
            errMessage.value = login.message;
        }
    }catch(err){
        eventBus.emit('closeLoading');
        console.error(err);
    }
};
</script>