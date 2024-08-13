<template>
    <div>
        f
    </div>
</template>
<style lang="scss">

</style>
<script setup lang="ts">
import { useFetchDataStore } from '~/store/FetchData';
import { useNotFoundStore } from '~/store/NotFound';
const publicConfig = useRuntimeConfig().public;
definePageMeta({
    name: 'FirmwareDownloadDetail',
    layout: 'home',
    validate: async(route) => { 
        if(route.params.link === '/'){
            navigateTo('/firmware/download');
        }else{
            return true;
        }
    }
});
useHead({
    title:`FirmwareDownloadDetail | ${publicConfig.appName}`
});
const local = reactive({
    fetchedViewData: null,
});
useAsyncData(async () => {
    const res = await useFetchDataStore().fetchData();
    if(res.status == 'error'){
        return useNotFoundStore().setIsNotFound(true, '/projects','Projects not found');
    }
    local.fetchedViewData = res.data.other;
});
onMounted(() => {
    //
});
</script>