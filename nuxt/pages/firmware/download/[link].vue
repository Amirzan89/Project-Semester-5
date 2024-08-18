<template>
    <div>
        iki detail download
    </div>
</template>
<style lang="scss">
</style>
<script setup lang="ts">
import { eventBus } from '~/app/eventBus';
import { DownloadFirmware } from '~/composables/api/firmware';
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
const input = reactive({
    id_device: '',
    device: '',
});
useAsyncData(async () => {
    const res = await useFetchDataStore().fetchData();
    if(res.status == 'error'){
        return useNotFoundStore().setIsNotFound(true, '/projects','Projects not found');
    }
    local.fetchedViewData = res.data.other;
});
const downloadForm = async (event: Event) => {
    event.preventDefault();
    eventBus.emit('showLoading');
    let res = await DownloadFirmware({ id_device: input.id_device, device: input.device});
    if(res.status === 'success'){
        eventBus.emit('closeLoading');
        eventBus.emit('showGreenPopup', res.message);
    }else if(res.status === 'error'){
        eventBus.emit('closeLoading');
        eventBus.emit('showRedPopup', res.message);
    }
}
</script>