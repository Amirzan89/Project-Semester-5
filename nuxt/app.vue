<template>
    <NuxtLayout>
        <NuxtPage></NuxtPage>
    </NuxtLayout>
</template>
<style>
    :root{
        --darkMode: none;
    }
    header, main, footer, button{
        transition: var(--darkMode);
    }
    .items-loading{
        background: linear-gradient(135deg, #b9b9b9 0%, #cdcdcd 52%, #fff 52%, #fff 54%, #cdcdcd 54%, #b9b9b9 100%);
        background-size: 180% 180%; background-position: 100% 100%;
    }
    @keyframes shine {
        to {
            background-position: 0% 0%;
        }
    }
</style>
<script setup>
import { useDarkModeStore } from '~/store/DarkMode';
const darkModeStore = useDarkModeStore();
onBeforeMount(() => {
    darkModeStore.initializeDarkMode(0.35);
})
darkModeStore.$subscribe((mutation, state) => {
    if (state.preDarkMode) {
        document.documentElement.style.setProperty('--darkMode', `background-color ${state.transitionTime}s ease-in-out`);
    }else{
        document.documentElement.style.setProperty('--darkMode', 'none');
    }
});
</script>