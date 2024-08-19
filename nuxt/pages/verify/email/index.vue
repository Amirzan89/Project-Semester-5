<template>
    <template v-if="local.isDoneFetch">
        <template v-if="local.isValid">
            <div>
                success verified email
            </div>
        </template>
        <template v-else>
            <div>{{ local.message }}</div>
        </template>
    </template>
    <template v-else>
        <div></div>
    </template>
</template>
<style scoped lang="scss">
</style>
<script setup lang="ts">
import Cookies from 'js-cookie';
const publicConfig = useRuntimeConfig().public;
definePageMeta({
    name: 'VerifyEmail',
    layout: 'default',
});
useHead({
    title:`Verify Email | ${publicConfig.appName}`
});
const local = reactive({
    isDoneFetch: false,
    isValid: false,
    fetchedUserAuth: null as any,
    message: '',
});
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
</script>