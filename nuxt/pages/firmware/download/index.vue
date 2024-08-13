<template>
    <div>
        <ul class="container">
            <template v-for="(item, index) in local.fetchedViewData" :key="index">
                <li>
                    <div></div>
                </li>
            </template>
        </ul>
    </div>
</template>
<style scoped lang="scss"></style>
<script setup lang="ts">
import { useFetchDataStore } from '~/store/FetchData';
const route = useRoute();
const publicConfig = useRuntimeConfig().public;
definePageMeta({
    name: 'Firmware',
    layout: 'authenticated',
});
useHead({
    title:`FirmwareDownload| ${publicConfig.appName}`
});
const local = reactive({
    fetchedViewData: null,
});
useAsyncData(async () => {
    const res = await useFetchDataStore().fetchData();
    local.fetchedViewData = res.data.viewData;
});
</script>