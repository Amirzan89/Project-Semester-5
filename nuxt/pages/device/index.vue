<template>
    <div>
        iki list device
    </div>
</template>
<script setup lang="ts">
import { TambahDevice } from '~/composables/api/device';
import { useFetchDataStore } from '~/store/FetchData';
const publicConfig = useRuntimeConfig().public;
definePageMeta({
    name: 'Projects',
    layout: 'home',
});
useHead({
    title:`List Device | ${publicConfig.appName}`
});
const local = reactive({
    isUpdated: false,
    fetchedViewData: null as any,

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
useAsyncData(async () => {
    const res = await useFetchDataStore().fetchData();
    local.fetchedViewData = res.data.viewData;
});
watch(() => local.fetchedViewData, () => {
    if (local?.fetchedViewData !== undefined && typeof local.fetchedViewData === 'object' && Array.isArray(local.fetchedViewData) && Object.keys(local.fetchedViewData).length > 0) {
        nextTick(() => {
            local.fetchedViewData.forEach((item, index: number) => {
                let card = cardRefs.value[index];
                handleLoading(card);
            });
        });
    }
}, { immediate:true });
</script>