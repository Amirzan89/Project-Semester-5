<template>
    <div>
        <p>gk enek data </p>
        <h1>{{ useNotFoundStore().messageNotFound }}</h1>
        <button @click="backPage()" class="bg-red-500 w-60 h-15">balikkk</button>
    </div>
</template>
<script setup>
import { useNotFoundStore } from '~/store/NotFound';
const backPage = async () => {
    const link = useNotFoundStore().linkBack;
    await navigateTo(link);
    useNotFoundStore().resetState();
}
</script>