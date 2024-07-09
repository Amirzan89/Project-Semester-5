<template>
    <div>
        iki tambah
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
<script setup>
import { TambahDevice } from '../composition/Device';
const baseURL = useRuntimeConfig().public.baseURL;
definePageMeta({
    name: 'Projects',
    layout: 'home',
});
useHead({
    title:`Tambah Device | ${publicConfig.appName}`
});
const local = reactive({
    isTambah: false,
    fetchedViewData: null,
});
const input = reactive({
    name: '',
    subject: '',
    email: '',
    message: '',
});
const inpName = ref(null);
onBeforeRouteUpdate(() => {
    if(local.isTambah){
        const answer = window.confirm(
            'Do you really want to leave? you have unsaved changes!'
        )
        // cancel the navigation and stay on the same page
        if (!answer) return false
    }
    useFetchDataStore().resetFetchData();
});
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
    local.isTambah = isFilled;
};
const tambahForm = (event) => {
    event.preventDefault();
    local.isTambah = false;
}
</script>