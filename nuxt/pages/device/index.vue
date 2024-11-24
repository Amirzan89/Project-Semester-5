<template>
    <template v-if="local.isDoneFetch">
        <div>
            <ul>
                <template v-for="(item, index) in local?.fetchedViewData" :key="index">
                    <li>
                        <div></div>
                    </li>
                </template>
            </ul>
            iki list device
        </div>
    </template>
</template>
<script setup lang="ts">
import { eventBus } from '~/app/eventBus';
import { DeleteDevice } from '~/composables/api/device';
import { useFetchDataStore } from '~/store/FetchData';
const route = useRoute();
const publicConfig = useRuntimeConfig().public;
definePageMeta({
    name: 'Device',
    layout: 'home',
});
useHead({
    title:`List Device | ${publicConfig.appName}`
});
const local = reactive({
    isRequestInProgress: false,
    isDoneFetch: false,
    fetchedViewData: null as any,
});
useAsyncData(async () => {
    const res = await useFetchDataStore().fetchData();
    if(res ==  undefined || res.status == 'error'){
        return;
    }
    local.isDoneFetch = true;
    local.fetchedViewData = res.data.viewData;
});
onBeforeRouteUpdate(() => {
    useFetchDataStore().resetFetchData();
});
watch(() => local.fetchedViewData, () => {
    if (local?.fetchedViewData !== undefined && typeof local.fetchedViewData === 'object' && Array.isArray(local.fetchedViewData) && Object.keys(local.fetchedViewData).length > 0) {
        nextTick(() => {
            local.fetchedViewData.forEach((item: any, index: number) => {
                // let card = cardRefs.value[index];
                // handleLoading(card);
            });
        });
    }
}, { immediate:true });
const deleteForm = async (event: Event) => {
    event.preventDefault();
    if(local.isRequestInProgress) return;
    local.isRequestInProgress = true;
    eventBus.emit('showLoading');
    let res = await DeleteDevice({ id_device: route.params.id });
    if(res.status === 'success'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        eventBus.emit('showGreenPopup', res.message);
    }else if(res.status === 'error'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        eventBus.emit('showRedPopup', res.message);
    }
}
</script>