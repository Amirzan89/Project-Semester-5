<template>
    <div>
        iki delok + edit device
        <form>
            <div>
                <label for="name">Name</label>
                <input type="text" v-model="input.name" @input="inpChange('name')">
            </div>
            <div>
                <label for="name">Name</label>
                <input type="text" v-model="input.device_id" @input="inpChange('device_id')">
            </div>
            <div>
                <label for="name">Name</label>
                <input type="text" v-model="input.token" @input="inpChange('token')">
            </div>
            <div>
                <label for="name">Active</label>
                <input type="radio" value="active" v-model="input.activated" @input="inpChange('activated')">
                <input type="radio" value="nonactive" v-model="input.activated" @input="inpChange('activated')">
            </div>
            <button @click.prevent="deleteForm">Hapus</button>
            <button @click.prevent="updateForm">Edit</button>
        </form>
    </div>
</template>
<script setup lang="ts">
import { ref } from "vue";
import { useNotFoundStore } from '~/store/NotFound';
import { useFetchDataStore } from "~/store/FetchData";
import { DeleteDevice, EditDevice } from "~/composables/api/device";
import { eventBus } from "~/app/eventBus";
const publicConfig = useRuntimeConfig().public;
const route = useRoute();
definePageMeta({
    name: 'DeviceDetail',
    layout: 'home',
    validate: async(route) => {
        return true;
        // if(route.params.link === ''){
        //     navigateTo('/projects');
        // }else{
        //     return true;
        // }
    }
});
useHead({
    title: `Detail Device | ${publicConfig.appName}`
});
const local = reactive({
    isDoneFetch: false,
    isChanging: false,
    isUpdated: false,
    fetchedViewData: null as any,
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
    if(res.status == 'error'){
        if(res.code === 404){
            return useNotFoundStore().setIsNotFound(true, '/firmware','Detail Device not found');
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
useLazyAsyncData(async () => {
    const res = await useFetchDataStore().fetchData();
    if(res.status == 'success'){
        local.fetchedViewData = res.data;
    }else{
        useNotFoundStore().setIsNotFound(true, '/device','Data Device not found');
        return;
    }
});
onMounted(() => {
    //
})
watch(() => local.fetchedViewData, () => {
    if (local?.fetchedViewData !== undefined && local.fetchedViewData !== null && typeof local.fetchedViewData === 'object' && !Array.isArray(local.fetchedViewData) && Object.keys(local.fetchedViewData).length > 0) {
        nextTick(() => {
            // local.carouselSlide = new CarouselSlide(carouselRef.value, caItemRef.value, slideRef.value);
            // local.carouselSlide.initCarousel();
        });
    }
}, { immediate:true });
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
    local.isUpdated = isFilled;
};
const updateForm = async(event: Event) => {
    event.preventDefault();
    let errMessage = '';
    if (input.name === local.fetchedViewData.name && input.device_id === local.fetchedViewData.device_id && input.token === local.fetchedViewData.token && input.activated === local.fetchedViewData.activated) if(errMessage == '') errMessage = 'Data belum diubah !';
    let res = await EditDevice({ id_device: route.params.id,  name: input.name, device_id: input.device_id, token: input.token, activated: input.activated });
    if(res.status === 'success'){
        eventBus.emit('closeLoading');
        local.isUpdated = false;
        eventBus.emit('showGreenPopup', res.message);
    }else if(res.status === 'error'){
        eventBus.emit('closeLoading');
        eventBus.emit('showRedPopup', res.message);
    }
}
const deleteForm = async (event: Event) => {
    event.preventDefault();
    eventBus.emit('showLoading');
    let res = await DeleteDevice({ id_device: route.params.id });
    if(res.status === 'success'){
        eventBus.emit('closeLoading');
        eventBus.emit('showGreenPopup', res.message);
    }else if(res.status === 'error'){
        eventBus.emit('closeLoading');
        eventBus.emit('showRedPopup', res.message);
    }
}
</script>