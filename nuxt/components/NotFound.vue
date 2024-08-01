<template>
    <div>
        <p>gk enek data </p>
        <h1>{{ useNotFoundStore().messageNotFound }}</h1>
        <button @click="backPage()" class="bg-red-500 w-60 h-15">balikkk</button>
    </div>
</template>
<script setup lang="ts">
import { useNotFoundStore } from '~/store/NotFound';
const backPage = async () => {
    useNotFoundStore().resetState();
    useRouter().go(-1);
    // await navigateTo(useNotFoundStore().linkBack);
}
</script>