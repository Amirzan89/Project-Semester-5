<template>
    <div>
        
    </div>
</template>
<style scoped lang="scss">
</style>
<script setup lang="ts">
import { useFetchDataStore } from '~/store/FetchData';
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
useLazyAsyncData(async () => {
    const res = await useFetchDataStore().fetchData();
    if(res.status == 'error'){
        console.log(res);
    }
    local.isDoneFetch = true;
});
onMounted(() => {
    console.log('hasil', (window as any).__INITIAL_STATE__);
});
</script>