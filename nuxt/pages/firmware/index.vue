<template>
    <div>
        <div class="container">
            <template v-for="">
                <div>
                    
                </div>
            </template>
        </div>
    </div>
</template>
<style scoped lang="scss"></style>
<script setup lang="ts">
import { useFetchDataStore } from '~/store/FetchData';

useAsyncData(async() => {
    const res = useFetchDataStore().fetchData();
});
</script>