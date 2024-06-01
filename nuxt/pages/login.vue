<template>
    <div></div>
</template>
<script setup>
import { ref, reactive } from "vue";
import { eventBus } from '~/app/eventBus';
import { Login } from '../../composition/Auth';
const publicConfig = useRuntimeConfig().public;
useHead({
    title:'Login | TOkoKU'
});
useAsyncData(async () => {
});
const errMessage = ref('');
const input = reactive({
    email:'',
    password:'',
    isPasswordShow:false,
});
const popup = ref(null);
const inpEmail = ref(null);
const inpPassword = ref(null);
const showPass = () => {
    if(input.isPasswordShow){
        inpPassword.value.type = 'password';
        input.isPasswordShow = false;
    }else{
        inpPassword.value.type = 'text';
        input.isPasswordShow = true;
    }
};
const inpChange = (div) => {
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
const loginForm = async (event) => {
    event.preventDefault();
    if(input.email === null || input.email === ''){
        inpEmail.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpEmail.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        errMessage.value = 'Email Harus diisi !';
    }
    if(input.password === null || input.password === ''){
        inpPassword.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpPassword.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        errMessage.value = 'Password Harus diisi !';
    }
    if(errMessage.value != ''){
        popup.value.classList.remove('invisible');
        return;
    }
    eventBus.emit('showLoading');
    let login = await Login({email: input.email, password: input.password});
    console.log(login);
    return
    if(login.status === 'success'){
        eventBus.emit('closeLoading');
        eventBus.emit('showGreenPopup', login.message);
        setTimeout(function(){
            navigateTo('/dashboard');
        }, 1500);
    }else if(login.status === 'error'){
        eventBus.emit('closeLoading');
        popup.value.classList.remove('invisible');
        errMessage.value = login.message;
    }
};
</script>