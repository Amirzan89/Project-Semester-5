<template>
    <div>
        iki delok + edit device
        <form action="">
            <div>
                <label for="name">Name</label>
                <input type="text">
            </div>
            <div>
                <label for="name">Name</label>
                <input type="text">
            </div>
            <div>
                <label for="name">Name</label>
                <input type="text">
            </div>
            <div>
                <label for="name">Name</label>
                <input type="text">
            </div>
        </form>
    </div>
</template>
<script setup lang="ts">
import { ref } from "vue";
// import CarouselSlide from '~/composition/CarouselSlide';
import { useNotFoundStore } from '~/store/NotFound';
import { useFetchDataStore } from "~/store/FetchData";
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
    title: `Detail Device | ${publicConfig.appName}`
});
const local = reactive({
    fetchedDetailProject: null,
    fetchedOtherProject: null as any,
    thumbnail: '',
    carouselSlide: null,
    formattedDeskripsi:'',
    isUpdated: false,
});
const input = reactive({
    name: '',
    subject: '',
    email: '',
    message: '',
});
const inpName = ref(null);
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
useLazyAsyncData(async () => {
    const res = await useFetchDataStore().fetchData();
    if(res.status == 'success'){
        local.fetchedDetailProject = res.data.detailProject;
        local.formattedDeskripsi = local.fetchedDetailProject.deskripsi.split('\n').map(item => {
            return item.trim()!== ''? `<p>${item}</p>` : '<br>';
        }).join('');
        local.fetchedOtherProject = res.data.other;
    }else{
        useNotFoundStore().setIsNotFound(true, '/device','Data Device not found');
        return;
    }
});
onMounted(() => {
    //
})
watch(() => local.fetchedDetailProject, () => {
    if (local?.fetchedDetailProject !== undefined && local.fetchedDetailProject !== null && typeof local.fetchedDetailProject === 'object' && !Array.isArray(local.fetchedDetailProject) && Object.keys(local.fetchedDetailProject).length > 0) {
        local.thumbnail = local.fetchedDetailProject.thumbnail;
        nextTick(() => {
            // local.carouselSlide = new CarouselSlide(carouselRef.value, caItemRef.value, slideRef.value);
            // local.carouselSlide.initCarousel();
        });
    }
}, { immediate:true });
const inpChange = (cond) => {
    switch(cond){
        case 'email':
            inpEmail.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpEmail.value.classList.add('border-orange-500', 'hover:border-orange-500', 'focus:border-orange-500', 'dark:border-blue-600', 'dark:hover:border-blue-600', 'dark:focus:border-blue-600');
        break;
        case 'name':
            inpName.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpName.value.classList.add('border-orange-500', 'hover:border-orange-500', 'focus:border-orange-500', 'dark:border-blue-600', 'dark:hover:border-blue-600', 'dark:focus:border-blue-600');
        break;
        case 'subject':
            inpSubject.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpSubject.value.classList.add('border-orange-500', 'hover:border-orange-500', 'focus:border-orange-500', 'dark:border-blue-600', 'dark:hover:border-blue-600', 'dark:focus:border-blue-600');
        break;
        case 'message':
            inpMessage.value.classList.remove('border-popup_error','hover:border-popup_error','focus:border-popup_error');
            inpMessage.value.classList.add('border-orange-500', 'hover:border-orange-500', 'focus:border-orange-500', 'dark:border-blue-600', 'dark:hover:border-blue-600', 'dark:focus:border-blue-600');
        break;
    }
    let isFilled = true; 
    if(input.name === null || input.name === ''){
        isFilled = false;
        inpName.value.classList.remove('border-orange-500', 'dark:border-blue-600');
    }
    if(input.subject === null || input.subject === ''){
        isFilled = false;
        inpSubject.value.classList.remove('border-orange-500', 'dark:border-blue-600');
    }
    if(input.email === null || input.email === ''){
        isFilled = false;
        inpEmail.value.classList.remove('border-orange-500', 'dark:border-blue-600');
    }
    if(input.message === null || input.message === ''){
        isFilled = false;
        inpMessage.value.classList.remove('border-orange-500', 'dark:border-blue-600');
    }
    local.isUpdated = isFilled;
};
const updateForm = (event: Event) => {
    event.preventDefault();
    local.isUpdated = false;
}
</script>