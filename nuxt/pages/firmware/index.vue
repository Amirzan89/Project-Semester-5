<template>
    <template v-if="local.isDoneFetch">
    </template>
    <div>
        <ul class="container">
            <template v-for="(item, index) in local.fetchedViewData" :key="index">
                <li>
                    <div>
                        
                        <button @click.prevent="deleteForm"></button>
                    </div>
                </li>
            </template>
        </ul>
    </div>
</template>
<style scoped lang="scss"></style>
<script setup lang="ts">
import { ref, reactive, onMounted, type Ref } from "vue";
import { eventBus } from '~/app/eventBus';
import { DeleteFirmware } from '~/composables/api/firmware';
import { useFetchDataStore } from '~/store/FetchData';
const route = useRoute();
const publicConfig = useRuntimeConfig().public;
definePageMeta({
    name: 'Firmware',
    layout: 'authenticated',
});
useHead({
    title:`Firmware | ${publicConfig.appName}`
});
const local = reactive({
    isRequestInProgress: false,
    isDoneFetch: false,
    fetchedViewData: null,
});
useAsyncData(async () => {
    const res = await useFetchDataStore().fetchData();
    local.fetchedViewData = res.data.viewData;
});
const deleteForm = async (event: Event) => {
    event.preventDefault();
    if(local.isRequestInProgress) return;
    local.isRequestInProgress = true;
    eventBus.emit('showLoading');
    let res = await DeleteFirmware({ id_firmware: route.params.id });
    if(res.status === 'success'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        eventBus.emit('showGreenPopup', res.message);
    }else if(res.status === 'error'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        eventBus.emit('showRedPopup', res.message);
    }
}
</script>