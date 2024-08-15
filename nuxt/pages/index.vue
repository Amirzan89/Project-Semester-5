<template>
    <section id="about" class="flex h-screen justify-center pt-22">
        <div class="flex items-center justify-center gap-10 w-7/8 h-7/8">
            <div class="flex flex-col">
                <h1 class="text-7xl font-bold">IkiApp</h1>
                <p class="text-4xl font-normal mt-5">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis itaque nemo, et magnam officia pariatur totam obcaecati repellendus laboriosam dignissimos vero quos eos. A voluptates maxime reprehenderit quos esse! Similique?</p>
                <Button label="Primary" rounded class="mr-2 mb-2 bg-bold w-50 h-16 rounded-3xl text-white text-3xl font-bold mt-10">Cobak </Button>
            </div>
            <img src="~assets/images/app.png" alt="" class="w-100 object-cover animated-cos">
        </div>
    </section>
    <section>
        <div></div>
    </section>
</template>
<style scoped lang="scss">
.animated-cos{
    animation: updown 2s ease-in-out infinite alternate-reverse both;
}
@keyframes updown{
    0%{
        transform: translateY(10px);
    }
    100%{
        transform: translateY(-10px);
    }
}
</style>
<script setup lang="ts">
import { ref, reactive, onMounted, watch } from "vue";
import { useFetchDataStore } from "~/store/FetchData";
const publicConfig = useRuntimeConfig().public;
const route = useRoute();
definePageMeta({
    name: 'LandingPage',
    layout:'home',
});
useHead({
    title:`Landing Page | ${publicConfig.appName}`
});
const local = reactive({
    fetchedData: null,
});
useAsyncData(async() => {
    const res = await useFetchDataStore().fetchData();
});
onMounted(() => {
    if (route.hash) document.querySelector(route.hash)?.scrollIntoView({ behavior: "smooth" });
});
</script>