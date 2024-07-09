<template>
    <NuxtLayout>
        <NuxtPage></NuxtPage>
    </NuxtLayout>
</template>
<style>
    :root{
        --darkMode: none;
        --paddTop: 0px;
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
import resolveConfig from 'tailwindcss/resolveConfig';
import tailwindConfig from '~/tailwind.config';
import { useDarkModeStore } from '~/store/DarkMode';
const darkModeStore = useDarkModeStore();
const fullConfig = resolveConfig(tailwindConfig);
let screenCon = null;
const dynamicPad = {
    'phone':'30px',
    'sm':'40px',
    'md':'50px',
    'lg':'60px',
    'xl':'70px',
    '2xl':'70px',
}
const updatePadding = () => {
    const currentWidth = window.innerWidth;
    const breakpoints = [...screenCon].sort((a, b) => b - a);
    const breakpoint = breakpoints.find((fixWidth) => {
        return currentWidth >= fixWidth;
    });
    if(breakpoint !== undefined){
        document.documentElement.style.setProperty('--paddTop', `${dynamicPad[Object.keys(dynamicPad)[screenCon.indexOf(breakpoint)]]}`);
    }else{
        document.documentElement.style.setProperty('--paddTop', '50');
    }
}
onBeforeMount(() => {
    darkModeStore.initializeDarkMode(0.35);
    screenCon = Object.values(fullConfig.theme.screens).map((item) => {
        return parseInt(item.replace('px', ''));
    });
    updatePadding();
});
window.onresize = () => {
    updatePadding();
}
darkModeStore.$subscribe((mutation, state) => {
    if (state.preDarkMode) {
        document.documentElement.style.setProperty('--darkMode', `background-color ${state.transitionTime}s ease-in-out`);
    }else{
        document.documentElement.style.setProperty('--darkMode', 'none');
    }
});
</script>