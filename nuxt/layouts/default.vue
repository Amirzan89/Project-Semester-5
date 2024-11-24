<template>
    <template v-if="useNotFoundStore().isNotFound || fetchDataS.processFetch.isDone != 'success'">
        <NuxtPage/>
    </template>
    <template v-else>
        <NuxtPage/>
        <LoadingComponent/>
        <Toast/>
        <Toast position="bottom-right" group="br" />
    </template>
</template>
<style>
/* .page-left-enter-active,
.page-right-enter-active,
.page-left-leave-active,
.page-right-leave-active{
    transition: all 0.3s linear;
}
.page-left-enter-from, .page-right-leave-to{
    transform: translateX(100%);
    opacity: 0;
}
.page-left-leave-to, .page-right-enter-from{
    transform: translateX(-100%);
    opacity: 0;
}
.page-left-enter-to, .page-right-enter-to{
    transform: translateX(0);
    opacity: 1;
} */
</style>
<script setup lang="ts">
import LoadingComponent from "~/components/Loading.vue";
import { useNotFoundStore } from '~/store/NotFound';
import { useFetchDataStore } from '~/store/FetchData';
const fetchDataS = useFetchDataStore();
const route = useRoute();
watch(() => route.fullPath, async() => {
    const res = await fetchDataS.fetchPage();
    if(res ==  undefined || res.status == 'error'){
        return;
    }
}, { immediate:true });
</script>