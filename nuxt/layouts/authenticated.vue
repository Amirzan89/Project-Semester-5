<template>
    <template v-if="useNotFoundStore().isNotFound || fetchDataS.processFetch.isDone != 'success'">
        <NuxtPage/>
    </template>
    <template v-else>
        <div class="layout-wrapper layout-static">
            <HeaderComponent @menu-toggled="onNavToggle"/>
            <NavbarComponent/>
            <div class="tw-min-h-screen tw-flex tw-flex-col tw-justify-between tw-pt-28 tw-pr-8 tw-pb-8 tw-pl-4" style="transition: margin-left 0.2s ease-out;" :style="checkNav">
                <div class="tw-flex-1">
                    <NuxtPage/>
                </div>
                <FooterComponent/>
            </div>
        </div>
        <Loading/>
        <Toast/>
        <Toast position="bottom-right" group="br" />
    </template>
</template>
<style>
.page-left-enter-active,
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
}
</style>
<script setup lang="ts">
import HeaderComponent from '~/components/HeaderAuth.vue';
import NavbarComponent from '~/components/Navbar.vue';
import FooterComponent from '~/components/Footer.vue';
import Loading from '~/components/Loading.vue';
import { useNotFoundStore } from '~/store/NotFound';
import { useFetchDataStore } from "~/store/FetchData";
const fetchDataS = useFetchDataStore();
const route = useRoute();
const isNavInactive: Ref = ref(false);
watch(() => route.fullPath, async() => {
    const res = await fetchDataS.fetchPage();
    if(res ==  undefined || res.status == 'error'){
        return;
    }
}, { immediate:true });
const onNavToggle = () => {
    document.querySelector('body')?.classList.toggle('nav-inactive', !isNavInactive.value);
    isNavInactive.value = !isNavInactive.value;
};
const checkNav = computed(() => {
    return {
        marginLeft: isNavInactive.value ? '0' : '200px',
        paddingLeft: isNavInactive.value ? '2rem' : ''
    };
});
</script>