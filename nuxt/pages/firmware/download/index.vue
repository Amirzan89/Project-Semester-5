<template>
    <div>
        <div>
            iki donwload index
        </div>
        <ul class="container">
            <!-- <template v-for="(item, index) in local.fetchedViewData" :key="index">
                <li>
                    <div></div>
                </li>
            </template> -->
        </ul>
    </div>
</template>
<style scoped lang="scss"></style>
<script setup lang="ts">
import { eventBus } from '~/app/eventBus';
import { DownloadFirmware } from '~/composables/api/firmware';
import { useFetchDataStore } from '~/store/FetchData';
const route = useRoute();
const publicConfig = useRuntimeConfig().public;
definePageMeta({
    name: 'FirmwareDownload',
    layout: 'home',
});
useHead({
    title:`FirmwareDownload| ${publicConfig.appName}`
});
const local = reactive({
    fetchedViewData: null,
});
const input = reactive({
    id_device: '',
    device: '',
});
const inpDeviceId: Ref = ref(null);
const inpDevice: Ref = ref(null);
useAsyncData(async () => {
    const res = await useFetchDataStore().fetchData();
    local.fetchedViewData = res.data.viewData;
});
onMounted(() => {
    
});
const downloadForm = async (event: Event) => {
    event.preventDefault();
    let errMessage = '';
    if(input.device === null || input.device === ''){
        inpDevice.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpDevice.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if (!errMessage) errMessage = 'Device Firmware Harus diisi !';
    }
    if(errMessage != ''){
        eventBus.emit('showRedPopup', errMessage);
        return;
    }
    eventBus.emit('showLoading');
    let res = await DownloadFirmware({ id_device: input.id_device, device: input.device });
    if(res.status === 'success'){
        eventBus.emit('closeLoading');
        eventBus.emit('showGreenPopup', res.message);
    }else if(res.status === 'error'){
        eventBus.emit('closeLoading');
        eventBus.emit('showRedPopup', res.message);
    }
}
</script>