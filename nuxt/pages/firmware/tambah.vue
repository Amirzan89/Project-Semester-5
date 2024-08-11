<template>
    <div>
        <form action="">
            <div>
                <label for="">Nama Fir</label>
                <input type="text">
            </div>
        </form>
    </div>
</template>
<style scoped lang="scss"></style>
<script setup lang="ts">
import { useFetchDataStore } from '~/store/FetchData';

useAsyncData(async() => {
    const res = useFetchDataStore().fetchData();
});
</script>