<template>
    <div>
        iki edit device
    </div>
</template>
<script setup>
import { ref } from "vue";
import CarouselSlide from '~/composition/CarouselSlide';
import { useNotFoundStore } from '~/store/NotFound';
import { projectDetailPage } from '../composition/home';
const publicConfig = useRuntimeConfig().public;
const route = useRoute();
definePageMeta({
    name: 'ProjectsDetail',
    layout: 'home',
    validate: async(route) => {
        return true;
        // if(route.params.link === ''){
        //     navigateTo('/projects');
        // }else{
        //     return true;
        // }
    }
});
useHead({
    title: route.params.link + ' | Amirzan Portfolio'
});
onBeforeRouteLeave((to, from) => {
    const answer = window.confirm(
        'Do you really want to leave? you have unsaved changes!'
    )
    // cancel the navigation and stay on the same page
    if (!answer) return false
})
useLazyAsyncData(async () => {
    const res = await  projectDetailPage(route.params.link);
    if(res.status == 'success'){
        local.fetchedDetailProject = res.data.detailProject;
        local.formattedDeskripsi = local.fetchedDetailProject.deskripsi.split('\n').map(item => {
            return item.trim()!== ''? `<p>${item}</p>` : '<br>';
        }).join('');
        local.fetchedOtherProject = res.data.other;
    }else{
        if(res.code && res.code === 404){
            useNotFoundStore().setIsNotFound(true, '/projects');
            useNotFoundStore().setMessageNotFound('Project Adios');
            return;
        }
    }
});
const local = reactive({
    fetchedDetailProject: null,
    fetchedOtherProject: null,
    thumbnail: '',
    carouselSlide: null,
    formattedDeskripsi:'',
});
watch(() => local.fetchedDetailProject, () => {
    if (local?.fetchedDetailProject !== undefined && local.fetchedDetailProject !== null && typeof local.fetchedDetailProject === 'object' && !Array.isArray(local.fetchedDetailProject) && Object.keys(local.fetchedDetailProject).length > 0) {
        local.thumbnail = local.fetchedDetailProject.thumbnail;
        nextTick(() => {
            // local.carouselSlide = new CarouselSlide(carouselRef.value, caItemRef.value, slideRef.value);
            // local.carouselSlide.initCarousel();
        });
    }
}, { immediate:true });
</script>