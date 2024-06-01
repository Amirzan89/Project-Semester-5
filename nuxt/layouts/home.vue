<template>
    <Header></Header>
    <main class="flex flex-col items-center flex-grow" style="z-index: 10;">
        <slot/>
        <app-footer></app-footer>
    </main>
    <Toast />
</template>
<script setup>
import Header from '~/components/Header.vue';
</script>