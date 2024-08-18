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
                    <input type="password" ref="inpPasswordRepeat" v-model="input.passwordRepeat" @change="inpChange('password_repeat')">
                </div>
                <button @click.prevent="sendForm">Kirim</button>
            </form>
        </div>
    </template>
</template>
<style scoped lang="scss">
</style>
<script setup lang="ts">
import { eventBus } from '~/app/eventBus';
import { LoginGoogle } from '~/composables/api/auth';
// import { useFetchDataStore } from '~/store/FetchData';
const route = useRoute();
const publicConfig = useRuntimeConfig().public;
definePageMeta({
    name: 'FirmwareTambah',
    layout: 'authenticated',
    validate: async(route) => { 
        if(route.params.link === '/'){
            navigateTo('/');
        }else{
            return true;
        }
    }
});
useHead({
    title:`Verify Password | ${publicConfig.appName}`
});
const local = reactive({
    isDoneFetch: false,
});
const input = reactive({
    password:'',
    passwordRepeat:'',
});
const inpPassword: Ref = ref(null);
const inpPasswordRepeat: Ref = ref(null);
useAsyncData(async () => {
    // const res = await useFetchDataStore().fetchData();
    // if(res.status == 'error'){
    //     console.log(res);
    // }
    local.isDoneFetch = true;
});
onMounted(() => {
    console.log('hasil', (window as any).__INITIAL_STATE__);
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
    const res = await LoginGoogle({email: '', password: ''});
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