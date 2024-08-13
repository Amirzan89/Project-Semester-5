<template>
    <div>
        <form action="">
            <div>
                <label for="">Nama Firmware</label>
                <input type="text">
            </div>
            <div>
                <label for="">Deskripsi</label>
                <input type="text">
            </div>
            <div>
                <label for="">Version Firmware</label>
                <input type="text">
            </div>
            <div>
                <label for="">release Firmware</label>
                <input type="text">
            </div>
            <div>
                <label for="">release Firmware</label>
                <input type="text">
            </div>
        </form>
        <button>Edit</button>
        <button>Cancel</button>
    </div>
</template>
<style scoped lang="scss"></style>
<script setup lang="ts">
import { useFetchDataStore } from '~/store/FetchData';
import { encrypt } from '~/composables/encrypt';
useAsyncData(async() => {
    const res = useFetchDataStore().fetchData();
});
const upload = () => {
    encrypt(data);
}
</script>