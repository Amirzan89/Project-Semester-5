<template>
    <template v-if="local.isDoneFetch">
        <div>
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
                    <button @click.prevent="deleteForm">Delete</button>
                </div>
                <div>
                    <button @click.prevent="editForm">Edit</button>
                </div>
            </form>
        </div>
    </template>
    <template v-else>
        <div>iki detaill</div>
    </template>
</template>
<style scoped lang="scss"></style>
<script setup lang="ts">
import { ref, reactive, onMounted, type Ref } from "vue";
import { useFetchDataStore } from '~/store/FetchData';
import { eventBus } from '~/app/eventBus';
import { EditFirmware, DeleteFirmware } from '~/composables/api/firmware';
import { useNotFoundStore } from '~/store/NotFound';
const route = useRoute();
const publicConfig = useRuntimeConfig().public;
definePageMeta({
    name: 'FirmwareDetail', 
    layout: 'default',
    validate: async(route) => { 
        if(route.params.id === '/'){
            navigateTo('/firmware');
        }else{
            return true;
        }
    }
});
useHead({
    title:`Firmware Detail | ${publicConfig.appName}`
});
const local = reactive({
    isRequestInProgress: false,
    isDoneFetch: false,
    isChanging: false,
    isUpdated:false,
    fetchedUserAuth: null,
    fetchedViewData: null as any,
});
const input = reactive({
    name: '',
    description: '',
    version: '',
    release_date: '',
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
    if(res.status == 'error'){
        if(res.code === 404){
            return useNotFoundStore().setIsNotFound(true, '/firmware','Detail Firmware not found');
        }
    }
    local.fetchedViewData = res.data.other;
});
onBeforeRouteUpdate(() => {
    if(local.isUpdated){
        const answer = window.confirm(
            'Do you really want to leave? you have unsaved changes!'
        )
        // cancel the navigation and stay on the same page
        if (!answer) return false
    }
    useFetchDataStore().resetFetchData();
});
type InputKeys = 'name' | 'description' | 'version' | 'release_date' | 'device' | 'file';
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
    local.isUpdated = isFilled;
};
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
const editForm = async (event: Event) => {
    event.preventDefault();
    if(local.isRequestInProgress) return;
    let errMessage = '';
    if (input.name === local.fetchedViewData.name && input.description === local.fetchedViewData.description && input.version === local.fetchedViewData.version && input.release_date === local.fetchedViewData.release_date && input.device === local.fetchedViewData.device) if(errMessage == '') errMessage = 'Data belum diubah !';
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
    // let res = await EditFirmware({ id_firmware: route.params.id,  name: input.name, description: input.description, version: input.version, release_date: input.release_date, checksum: enc.checksum, device: input.device, file: enc.file });
    // if(res.status === 'success'){
    //     local.isRequestInProgress = false;
    //         eventBus.emit('closeLoading');
    //         local.isUpdated = false;
    //         eventBus.emit('showGreenPopup', res.message);
    //     }else if(res.status === 'error'){
    //     local.isRequestInProgress = false;
    //     eventBus.emit('closeLoading');
    //     eventBus.emit('showRedPopup', res.message);
    // }
}
const deleteForm = async (event: Event) => {
    event.preventDefault();
    eventBus.emit('showLoading');
    let res = await DeleteFirmware({ id_firmware: route.params.id });
    if(res.status === 'success'){
        eventBus.emit('closeLoading');
        eventBus.emit('showGreenPopup', res.message);
    }else if(res.status === 'error'){
        eventBus.emit('closeLoading');
        eventBus.emit('showRedPopup', res.message);
    }
}
</script>