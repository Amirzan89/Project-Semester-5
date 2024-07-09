<template>
    <div>
        <template v-if="useNotFoundStore().isNotFound">
            <NotFoundComponent/>
        </template>
        <template v-else>
            <template v-if="useFetchDataStore().processFetch.isDone == 'success' || useFetchDataStore().processFetch.isDone == 'loading'">
                <Header></Header>
                <main class="flex flex-col items-center flex-grow" style="z-index: 10;">
                    <slot/>
                    <app-footer></app-footer>
                </main>
                <!-- <Toast /> -->
            </template>
            <template v-else-if="useFetchDataStore().processFetch.isDone  == 'error'">
                <NotFoundComponent/>
            </template>
        </template>
    </div>
</template>
<style>
.page-left-enter-active,
.page-right-enter-active,
.page-left-leave-active,
.page-right-leave-active{
    transition: all 0.3s linear;
}
.page-left-enter-from, .page-right-leave-to{
    transform: translateX(100%);
    opacity: 0;
}
.page-left-leave-to, .page-right-enter-from{
    transform: translateX(-100%);
    opacity: 0;
}
.page-left-enter-to, .page-right-enter-to{
    transform: translateX(0);
    opacity: 1;
}
</style>
<script setup>
import Header from '~/components/Header.vue';
import NotFoundComponent from '~/components/NotFound.vue';
import { useNotFoundStore } from '~/store/NotFound';
import { useFetchDataStore } from '~/store/FetchData';
</script>