<template>
    <template v-if="local.isDoneFetch">
        <div>
            <form>
                <div>
                    <label for="">password</label>
                    <input type="password" ref="inpPassword" v-model="input.password" @change="inpChange('password')">
                </div>
                <div>
                    <label for="">repeat pasword</label>
                    <input type="password" ref="inpPasswordRepeat" v-model="input.passwordRepeat" @change="inpChange('password_reset')">
                </div>
                <button @click.prevent="sendForm">Buat Akun</button>
            </form>
        </div>
    </template>
    <template v-else>
        <div></div>
    </template>
</template>
<style scoped lang="scss">
</style>
<script setup lang="ts">
import Cookies from 'js-cookie'
import { eventBus } from '~/app/eventBus';
import { CreateLoginGoogle } from '~/composables/api/auth';
const route = useRoute();
const publicConfig = useRuntimeConfig().public;
definePageMeta({
    name: 'AuthGoogle',
    layout: 'default',
});
useHead({
    title:`Register with Google | ${publicConfig.appName}`
});
const local = reactive({
    isDoneFetch: false,
    fetchedUserAuth: null as any,
});
const input = reactive({
    password:'',
    passwordRepeat:'',
});
const inpPassword: Ref = ref(null);
const inpPasswordRepeat: Ref = ref(null);
useAsyncData(async () => {
    let coDat = Cookies.get('__INITIAL_COSTUM_STATE__');
    if(coDat == null || coDat == '') return navigateTo('/login');
    let deC = atob(coDat);
    if(deC == null || deC == '') return navigateTo('/login');
    local.fetchedUserAuth = JSON.parse(deC);
    local.isDoneFetch = true;
    Cookies.remove('__INITIAL_COSTUM_STATE__');
});
const inpChange = (div: string) => {
    if(div == 'password'){
        inpPassword.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        inpPassword.value.classList.add('border-black','hover:border-black','focus:border-black');
    }else if(div == 'password_repeat'){
        inpPasswordRepeat.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        inpPasswordRepeat.value.classList.add('border-black','hover:border-black','focus:border-black');
    }
};
const sendForm = async () => {
    const res = await CreateLoginGoogle({email: local.fetchedUserAuth.email, nama: local.fetchedUserAuth.nama, password: input.password, password_confirm: input.passwordRepeat, file: local.fetchedUserAuth.file });
    if(res.status === 'success'){
        eventBus.emit('closeLoading');
        eventBus.emit('showGreenPopup', res.message);
        setTimeout(function(){
            navigateTo('/dashboard');
        }, 1500);
    }else if(res.status === 'error'){
        eventBus.emit('closeLoading');
        eventBus.emit('showRedPopup', res.message);
    }
}
</script>