<template>
    <div>
        iki tambah
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
</script>