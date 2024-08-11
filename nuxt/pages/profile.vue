<template>
    <div>
        <div>
            //
        </div>
        <div>
            //
        </div>
        <div>
            //
        </div>
    </div>
</template>
<style scoped lang="scss"></style>
<script setup lang="ts">
import { useFetchDataStore } from '~/store/FetchData';

useAsyncData(async() => {
    const res = await useFetchDataStore().fetchData();
    if(res.status == 'error'){
        //
    }
});
</script>