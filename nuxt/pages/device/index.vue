<template>
    <div>
        iki list device
    </div>
</template>
<script setup>
import { TambahDevice } from '../composition/Device';
const baseURL = useRuntimeConfig().public.baseURL;
definePageMeta({
    name: 'Projects',
    layout: 'home',
});
useHead({
    title:`List Device | ${publicConfig.appName}`
});
useAsyncData(async () => {
    const res = await  projectPage();   
    local.fetchedViewData = res.data.viewData;
});
watch(() => local.fetchedViewData, () => {
    if (local?.fetchedViewData !== undefined && typeof local.fetchedViewData === 'object' && Array.isArray(local.fetchedViewData) && Object.keys(local.fetchedViewData).length > 0) {
        nextTick(() => {
            local.fetchedViewData.forEach((item, index) => {
                let card = cardRefs.value[index];
                handleLoading(card);
            });
        });
    }
}, { immediate:true });
</script>