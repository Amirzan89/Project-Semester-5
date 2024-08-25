<template>
    <div>
        <template v-if="local.isDoneFetch">
            <template v-if="local.isValid">
                <div>
                    <form>
                        <div>
                            <label for="">password</label>
                            <input type="password" ref="inpPassword" v-model="input.password" @change="inpChange('password')">
                        </div>
                        <div>
                            <label for="">repeat pasword</label>
                            <input type="password" ref="inpUlangiPassword" v-model="input.ulangiPassword" @change="inpChange('password_repeat')">
                        </div>
                        <button @click.prevent="sendForm">Kirim</button>
                    </form>
                </div>
            </template>
            <template v-else>
                <div>{{ local.message }}</div>
            </template>
        </template>
    </div>
</template>
<style scoped lang="scss">
</style>
<script setup lang="ts">
import { eventBus } from '~/app/eventBus';
import { VerifyChange } from '~/composables/api/auth';
import Cookies from 'js-cookie';
const publicConfig = useRuntimeConfig().public;
definePageMeta({
    name: 'VerifyPassword',
    layout: 'default',
});
useHead({
    title:`Verify Password | ${publicConfig.appName}`
});
const local = reactive({
    isDoneFetch: false,
    isValid: false,
    fetchedUserAuth: null as any,
    message: '',
});
const input = reactive({
    password: '',
    ulangiPassword: '',
    isPasswordShow: false,
    isUlangiPasswordShow: false,
});
const inpPassword: Ref = ref(null);
const inpUlangiPassword: Ref = ref(null);
useAsyncData(async () => {
    let coDat = Cookies.get('__INITIAL_COSTUM_STATE__');
    if(coDat == null || coDat == '') return navigateTo('/login');
    let deC = atob(coDat);
    if(deC == null || deC == '') return navigateTo('/login');
    local.fetchedUserAuth = JSON.parse(deC);
    local.isDoneFetch = true;
    if(local.fetchedUserAuth.status == 'error'){
        local.message = local.fetchedUserAuth.message;
        return;
    }
    local.isValid = true;
    Cookies.remove('__INITIAL_COSTUM_STATE__');
});
const inpChange = (div: string) => {
    if(div == 'password'){
        inpPassword.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        inpPassword.value.classList.add('border-black','hover:border-black','focus:border-black');
    }else if(div == 'password_repeat'){
        inpUlangiPassword.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        inpUlangiPassword.value.classList.add('border-black','hover:border-black','focus:border-black');
    }
};
const sendForm = async (event: Event) => {
    event.preventDefault();
    let errMessage = '';
    if(input.password === null || input.password === ''){
        inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if(!errMessage) errMessage = 'Password Harus diisi !';
    }else{
        if (input.password.length < 8) {
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password minimal 8 karakter !';
        }
        if (!/[A-Z]/.test(input.password)) {
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password minimal ada 1 huruf kapital !';
        }
        if (!/[a-z]/.test(input.password)) {
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password minimal ada 1 huruf kecil !';
        }
        if (!/\d/.test(input.password)) {
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password minimal ada 1 angka !';
        }
        if (!/[!@#$%^&*]/.test(input.password)) {
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password minimal ada 1 karakter unik !';
        }
    }
    if(input.ulangiPassword === null || input.ulangiPassword === ''){
        inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if(!errMessage) errMessage = 'Password baru Harus diisi !';
    }else{
        if (input.ulangiPassword.length < 8) {
            inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password konfirmasi minimal 8 karakter !';
        }
        if (!/[A-Z]/.test(input.ulangiPassword)) {
            inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password konfirmasi minimal ada 1 huruf kapital !';
        }
        if (!/[a-z]/.test(input.ulangiPassword)) {
            inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password konfirmasi minimal ada 1 huruf kecil !';
        }
        if (!/\d/.test(input.ulangiPassword)) {
            inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password konfirmasi minimal ada 1 angka !';
        }
        if (!/[!@#$%^&*]/.test(input.ulangiPassword)) {
            inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password konfirmasi minimal ada 1 karakter unik !';
        }
    }
    if(!(input.password === null || input.password === '') && !(input.password === null || input.password === '')){
        if(input.password != input.ulangiPassword){
            inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpUlangiPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
            inpUlangiPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            if(!errMessage) errMessage = 'Password harus sama !';
        }
    }
    if(errMessage != ''){
        eventBus.emit('showRedPopup', errMessage);
        return;
    }
    eventBus.emit('showLoading');
    let verifyChange = await VerifyChange({email: local.fetchedUserAuth.email, link: local.fetchedUserAuth.link, password: input.password, ulangiPassword:input.ulangiPassword});
    if(verifyChange.status === 'success'){
        eventBus.emit('closeLoading');
        eventBus.emit('showGreenPopup', verifyChange.message);
        setTimeout(function(){
            navigateTo('/login');
        }, 2000);
    }else if(verifyChange.status === 'error'){
        eventBus.emit('closeLoading');
        eventBus.emit('showRedPopup', errMessage);
        errMessage = verifyChange.message;
    }
}
</script>