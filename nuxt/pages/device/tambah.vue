<template>
    <template v-if="local.isDoneFetch">
        <div>
            iki tambah device
            <form>
                <div>
                    <label for="name">Name</label>
                    <input type="text" v-model="input.name" @input="inpChange('name')">
                </div>
                <div>
                    <label for="name">Device id</label>
                    <input type="text" v-model="input.device_id" @input="inpChange('device_id')">
                </div>
                <div>
                    <label for="name">Token</label>
                    <input type="text" v-model="input.token" @input="inpChange('token')">
                </div>
                <div>
                    <label for="name">Active</label>
                    <input type="radio" value="active" v-model="input.activated" @input="inpChange('activated')">
                    <input type="radio" value="nonactive" v-model="input.activated" @input="inpChange('activated')">
                </div>
                <button @click.prevent="tambahForm">tambah</button>
            </form>
        </div>
    </template>
</template>
<script setup lang="ts">
import { eventBus } from '~/app/eventBus';
import { TambahDevice } from '~/composables/api/device';
import { useFetchDataStore } from '~/store/FetchData';
const publicConfig = useRuntimeConfig().public;
definePageMeta({
    name: 'DeviceTambah',
    layout: 'home',
});
useHead({
    title:`Tambah Device | ${publicConfig.appName}`
});
const local = reactive({
    isRequestInProgress: false,
    isDoneFetch: false,
    isTambah: false,
    fetchedViewData: null,
});
const input = reactive({
    name: '',
    device_id: '',
    token: '',
    activated: '',
});
const inpName: Ref = ref(null);
const inpDeviceId: Ref = ref(null);
const inpToken: Ref = ref(null);
const inpActivated: Ref = ref(null);
useAsyncData(async () => {
    const res = await useFetchDataStore().fetchData();
    if(res ==  undefined || res.status == 'error'){
        return;
    }
    local.isDoneFetch = true;
    local.fetchedViewData = res.data.other;
});
onBeforeRouteUpdate(() => {
    if(local.isTambah){
        const answer = window.confirm(
            'Do you really want to leave? you have unsaved changes!'
        )
        // cancel the navigation and stay on the same page
        if (!answer) return false
    }
    useFetchDataStore().resetFetchData();
});
const inpChange_ = (cond: string) => {
    switch(cond){
        case 'name':
            inpName.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpName.value.classList.add('border-orange-500', 'hover:border-orange-500', 'focus:border-orange-500', 'dark:border-blue-600', 'dark:hover:border-blue-600', 'dark:focus:border-blue-600');
        break;
        case 'device_id':
            inpDeviceId.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpDeviceId.value.classList.add('border-orange-500', 'hover:border-orange-500', 'focus:border-orange-500', 'dark:border-blue-600', 'dark:hover:border-blue-600', 'dark:focus:border-blue-600');
        break;
        case 'token':
            inpToken.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpToken.value.classList.add('border-orange-500', 'hover:border-orange-500', 'focus:border-orange-500', 'dark:border-blue-600', 'dark:hover:border-blue-600', 'dark:focus:border-blue-600');
        break;
        case 'activated':
            inpActivated.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpActivated.value.classList.add('border-orange-500', 'hover:border-orange-500', 'focus:border-orange-500', 'dark:border-blue-600', 'dark:hover:border-blue-600', 'dark:focus:border-blue-600');
        break;
    }
    let isFilled = true; 
    if(input.name === null || input.name === ''){
        isFilled = false;
        inpName.value.classList.remove('border-orange-500', 'dark:border-blue-600');
    }
    if(input.device_id === null || input.device_id === ''){
        isFilled = false;
        inpDeviceId.value.classList.remove('border-orange-500', 'dark:border-blue-600');
    }
    if(input.token === null || input.token === ''){
        isFilled = false;
        inpToken.value.classList.remove('border-orange-500', 'dark:border-blue-600');
    }
    if(input.activated === null || input.activated === ''){
        isFilled = false;
        inpActivated.value.classList.remove('border-orange-500', 'dark:border-blue-600');
    }
    local.isTambah = isFilled;
};
type InputKeys = 'name' | 'device_id' | 'token' | 'activated';
const inpChange = (div: string) => {
    const inputs: any = {
        name: inpName,
        device_id: inpDeviceId,
        token: inpToken,
        activated: inpActivated,
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
const tambahForm = async(event: Event) => {
    event.preventDefault();
    if(local.isRequestInProgress) return;
    let errMessage = '';
    if(input.name === null || input.name === ''){
        inpName.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpName.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if (!errMessage) errMessage = 'Name Firmware Harus diisi !';
    }
    if(input.device_id === null || input.device_id === ''){
        inpDeviceId.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpDeviceId.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if (!errMessage) errMessage = 'Description Firmware Harus diisi !';
    }
    if(input.token === null || input.token === ''){
        inpToken.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpToken.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if (!errMessage) errMessage = 'Version Firmware Harus diisi !';
    }
    if(input.activated === null || input.activated === ''){
        inpActivated.value.classList.remove('border-black','hover:border-black','focus:border-black');
        inpActivated.value.classList.add('border-popup_error','hover:border-popup_error','focus:border-popup_error');
        if (!errMessage) errMessage = 'Release Date Firmware Harus diisi !';
    }
    if(errMessage != ''){
        eventBus.emit('showRedPopup', errMessage);
        return;
    }
    local.isRequestInProgress = true;
    eventBus.emit('showLoading');
    let res = await TambahDevice({ name: input.name, device_id: input.device_id, token: input.token, activated: input.activated });
    if(res.status === 'success'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        eventBus.emit('showGreenPopup', res.message);
        local.isTambah = false;
        setTimeout(function(){
            navigateTo('/firmware');
        }, 1500);
    }else if(res.status === 'error'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        eventBus.emit('showRedPopup', res.message);
    }
}
</script>