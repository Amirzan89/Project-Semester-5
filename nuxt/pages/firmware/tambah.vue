<template>
    <template v-if="local.isDoneFetch">
        <div>
            IKI TAMBAH
            <form>
                <div>
                    <label for="">Nama Firmware</label>
                    <input type="text" ref="inpName" v-model="input.name" @input="inpChange('name')">
                </div>
                <div>
                    <label for="">Deskripsi</label>
                    <input type="text" ref="inpDescription" v-model="input.description" @input="inpChange('description')">
                </div>
                <div>
                    <label for="">Version Firmware</label>
                    <input type="text" ref="inpVersion" v-model="input.version" @input="inpChange('version')">
                </div>
                <div>
                    <label for="">release Firmware</label>
                    <input type="text" ref="inpReleaseDate" v-model="input.release_date" @input="inpChange('release_date')">
                </div>
                <div>
                    <label for="">Device Firmware</label>
                    <input type="text" ref="inpDevice" v-model="input.device" @input="inpChange('device')">
                </div>
                <div>
                    <label for="">Device Firmware</label>
                    <div @dragover.prevent="handleDragOver" @drop.prevent="handleDrop" @click="handleFormClick">
                        <input type="file" ref="inpFile" hidden @change="handleFileChange">
                    </div>
                </div>
                <div>
                    <button @click.prevent="tambahForm">Tambah</button>
                </div>
            </form>
        </div>
    </template>
    <template v-else>
        <div>iki tambah</div>
    </template>
</template>
<style scoped lang="scss"></style>
<script setup lang="ts">
import { ref, reactive } from "vue";
import { eventBus } from '~/app/eventBus';
import { useFetchDataStore } from '~/store/FetchData';
import { encrypt } from '~/composables/encryption';
import { TambahFirmware } from '~/composables/api/firmware';
const publicConfig = useRuntimeConfig().public;
definePageMeta({
    name: 'FirmwareTambah',
    layout: 'default',
});
useHead({
    title:`Firmware Tambah | ${publicConfig.appName}`
});
const local = reactive({
    isRequestInProgress: false,
    isDoneFetch: false,
    isTambah: false,
    fetchedViewData: null,
});
const input = reactive({
    name: '',
    description: '',
    version: '',
    release_date: '',
    checksum: '',
    device: '',
    file: null as File | null,
});
const inpName: Ref = ref(null);
const inpDescription: Ref = ref(null);
const inpVersion: Ref = ref(null);
const inpReleaseDate: Ref = ref(null);
const inpDevice: Ref = ref(null);
const inpFile: Ref = ref(null);
useAsyncData(async () => {
    const res = await useFetchDataStore().fetchData();
    if(res ==  undefined || res.status == 'error'){
        return;
    }else{
        local.isDoneFetch = true;
        local.fetchedViewData = res.data.other;
    }
});
onBeforeRouteUpdate(() => {
    if(local.isTambah){
        const answer = window.confirm(
            'Do you really want to leave? you have unsaved changes!'
        )
        if (!answer) return false
    }
    useFetchDataStore().resetFetchData();
});
const handleFormClick = () => {
    inpFile.value.click();
}
const handleFileChange = (event: any) => {
    handleFiles(event.target.files);
}
const handleDragOver = (event: Event) => {
    event.preventDefault();
}
const handleDrop = (event: any) => {
    event.preventDefault();
    handleFiles(event.dataTransfer.files);
}
const handleFiles = (file: File) => {
    if(file.name.split('.').pop()?.toLowerCase() !== 'bin'){
        eventBus.emit('closeLoading');
        eventBus.emit('showRedPopup', 'Invalid file format. Only .bin files are allowed.');
        return;
    }
    if(file.size > 1 * (1024 * 1024)){
        eventBus.emit('closeLoading');
        eventBus.emit('showRedPopup', 'File size exceeds the maximum limit of 1MB.');
        return;
    }
    input.file = file;
}
const inpChange_ = (div: string) => {
    switch(div){
        case 'name':
            inpName.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpName.value.classList.add('border-black','hover:border-black','focus:border-black');
        break;
        case 'description':
            inpDescription.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpDescription.value.classList.add('border-black','hover:border-black','focus:border-black');
        break;
        case 'version':
            inpVersion.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpVersion.value.classList.add('border-black','hover:border-black','focus:border-black');
        break;
        case 'release_date':
            inpReleaseDate.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpReleaseDate.value.classList.add('border-black','hover:border-black','focus:border-black');
        break;
        case 'device':
            inpDevice.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpDevice.value.classList.add('border-black','hover:border-black','focus:border-black');
        break;
    }
    let isFilled = true; 
    if(input.name === null || input.name === ''){
        isFilled = false;
        inpName.value.classList.remove('border-orange-500', 'dark:border-blue-600');
    }
    if(input.description === null || input.description === ''){
        isFilled = false;
        inpDescription.value.classList.remove('border-orange-500', 'dark:border-blue-600');
    }
    if(input.version === null || input.version === ''){
        isFilled = false;
        inpVersion.value.classList.remove('border-orange-500', 'dark:border-blue-600');
    }
    if(input.release_date === null || input.release_date === ''){
        isFilled = false;
        inpReleaseDate.value.classList.remove('border-orange-500', 'dark:border-blue-600');
    }
    local.isTambah = isFilled;
};
type InputKeys = 'name' | 'description' | 'version' | 'release_date' | 'checksum' | 'device' | 'file';
const inpChange = (div: string) => {
    const inputs: any = {
        name: inpName,
        description: inpDescription,
        version: inpVersion,
        release_date: inpReleaseDate,
        device: inpDevice,
    };
    inputs[div].value?.classList.remove('border-popup_error', 'hover:border-popup_error', 'focus:border-popup_error');
    inputs[div].value?.classList.add('border-black', 'hover:border-black', 'focus:border-black');
    let isFilled = true;
    for (const key of Object.keys(inputs)) {
        if (input[key as InputKeys] === null || input[key as InputKeys] === '') {
            isFilled = false;
            inputs[key].value.classList.remove('border-orange-500', 'dark:border-blue-600');
        }
    }
    local.isTambah = isFilled;
};
const tambahForm = async (event: Event) => {
    if(local.isRequestInProgress) return;
    event.preventDefault();
    let errMessage = '';
    if(input.name === null || input.name === ''){
        inpName.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpName.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if (!errMessage) errMessage = 'Name Firmware Harus diisi !';
    }
    if(input.description === null || input.description === ''){
        inpDescription.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpDescription.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if (!errMessage) errMessage = 'Description Firmware Harus diisi !';
    }
    if(input.version === null || input.version === ''){
        inpVersion.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpVersion.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if (!errMessage) errMessage = 'Version Firmware Harus diisi !';
    }
    if(input.release_date === null || input.release_date === ''){
        inpReleaseDate.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpReleaseDate.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if (!errMessage) errMessage = 'Release Date Firmware Harus diisi !';
    }
    if(input.device === null || input.device === ''){
        inpDevice.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpDevice.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if (!errMessage) errMessage = 'Device Firmware Harus diisi !';
    }
    if(errMessage != ''){
        eventBus.emit('showRedPopup', errMessage);
        return;
    }
    local.isRequestInProgress = true;
    eventBus.emit('showLoading');
    // let enc = await encrypt({file: input.file, name:'', });
    // let res = await TambahFirmware({ name: input.name, description: input.description, version: input.version, release_date: input.release_date, checksum: input.checksum, device: input.device, file: enc.file });
    // if(res.status === 'success'){
    //     local.isRequestInProgress = false;
    //     eventBus.emit('closeLoading');
    //     eventBus.emit('showGreenPopup', res.message);
    //     local.isTambah = false;
    //     setTimeout(function(){
    //         navigateTo('/firmware');
    //     }, 1500);
    // }else if(res.status === 'error'){
    //     local.isRequestInProgress = false;
    //     eventBus.emit('closeLoading');
    //     eventBus.emit('showRedPopup', res.message);
    // }
}
</script>