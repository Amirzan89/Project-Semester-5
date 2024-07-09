<template>
    <div class="w-full h-22 sticky top-0 flex items-center justify-center scroll-pt-20 bg-primary1 text-white" style="box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.27); z-index:999">
        <div class="w-11/12 flex items-center justify-between">
            <div class="flex items-center gap-30">
                <h1 class="text-6xl font-bold">{{ publicConfig.appName }}</h1>
                <ul class="flex flex-row gap-4 font-semibold text-2xl">
                    <li>
                        <a href="#about">About</a>
                    </li>
                    <li>
                        <a href="#service">Service</a>
                    </li>
                </ul>
            </div>
            <div>
                <nuxt-link to="/login" class="flex items-center gap-3">
                    <FontAwesomeIcon icon="fa-solid fa-right-to-bracket" class="text-2xl" />
                    <span class="text-2xl font-semibold">Masuk</span>
                </nuxt-link>
            </div>
        </div>
    </div>
</template>
<style scoped>
    header{
        z-index: 999;
        height: var(--paddTop);
        scroll-padding-top: var(--paddTop);
    }
    a{
        transition: color 0.2s ease-in;
    }
</style>
<script setup>
import { reactive } from "vue";
import { useDarkModeStore } from '~/store/DarkMode';
const route = useRoute();
const linkHref = ['#me', '#about', '#project', '#contact'];
const isActive = reactive([false, false, false, false]);
const isHover = reactive([false, false, false, false]);
const darkModeStore = useDarkModeStore();
const { $gsap } = useNuxtApp();
let header = null;
const changeHover = (item) => {
    isHover[item] = !isHover[item];
};
const changeActive = (item) => {
    isActive.forEach((active, index) => {
        if (active) isActive[index] = false;
    });
    isActive[item] = true;
};
const changeIcon = (cond = '') => {
    if(cond == 'light'){
        $gsap.set(header('img#modeLight'), { display: !darkModeStore.darkMode ? 'block':'none' });
    }else if(cond == 'dark'){
        $gsap.set(header('img#modeDark'), { display: darkModeStore.darkMode ? 'block':'none' });
    }else{
        $gsap.set(header('img#modeLight'), { display: !darkModeStore.darkMode ? 'block':'none' });
        $gsap.set(header('img#modeDark'), { display: darkModeStore.darkMode ?'block':'none' });
    }
}
onMounted(() => {
    header = $gsap.utils.selector('header');
    changeIcon();
    const index = linkHref.indexOf(route.hash);
    isActive[index === -1 ? 0 : index] = true;
});
const changeMode = () => {
    darkModeStore.toggleDarkMode();
    const tl = $gsap.timeline();
    if(darkModeStore.darkMode){
        tl.to(header('img#modeLight'), {
            onComplete: () => {
                changeIcon('light');
                changeIcon('dark');
                $gsap.set(header('img#modeLight'), { y: '0%', autoAlpha: 1 });
            },
            y:'50%',
            autoAlpha: 0,
            duration: darkModeStore.transitionTime/2,
        });
        tl.from(header('img#modeDark'), {
            onComplete: () => {
                $gsap.set(header('img#modeDark'), { y: '0%', autoAlpha: 1 });
            },
            y:'50%',
            autoAlpha: 0,
            duration: darkModeStore.transitionTime/2,
        })
    }else{
        tl.to(header('img#modeDark'), {
            onComplete: () => {
                changeIcon('light');
                changeIcon('dark');
                $gsap.set(header('img#modeDark'), { y: '0%', autoAlpha: 1 });
            },
            y:'50%',
            autoAlpha: 0,
            duration: darkModeStore.transitionTime/2,
        });
        tl.from(header('img#modeLight'), {
            onComplete: () => {
                $gsap.set(header('img#modeLight'), { y: '0%', autoAlpha: 1 }); 
            },
            y:'50%',
            autoAlpha: 0,
            duration: darkModeStore.transitionTime/2,
        });
    }
};
</script>