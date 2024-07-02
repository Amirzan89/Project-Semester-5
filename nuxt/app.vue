<template>
  <NuxtLayout>
    <NuxtPage></NuxtPage>
  </NuxtLayout>
</template>
<script setup>
import { useDarkModeStore } from '~/store/DarkMode';
const darkModeStore = useDarkModeStore();
onMounted(() => {
    darkModeStore.initializeDarkMode();
});
</script>