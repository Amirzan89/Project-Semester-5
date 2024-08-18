<template>
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
    fetchedViewData: null as any,
});
useAsyncData(async () => {
    const res = await useFetchDataStore().fetchData();
    if(res.status == 'success') local.fetchedViewData = res.data.viewData;
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