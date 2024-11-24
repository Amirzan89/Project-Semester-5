<template>
    <template v-if="useNotFoundStore().isNotFound || fetchDataS.processFetch.isDone  == 'error'"><NotFoundComponent/></template>
    <section v-else class="tw-w-full" style="min-height: calc(100vh - 7rem - (2rem + 20px) - 20px);">
        <div class="col-12 tw-mt-5 tw-relative tw-left-1/2 -tw-translate-x-1/2">
            <div class="col-12">
                <h1 class="3xs:tw-text-3xl sm:tw-text-3xl lg:tw-text-4xl tw-font-semibold">Kelola Admin</h1>
                <Breadcrumb :home="bNavIcon" :model="bNavItems" class="3xs:tw-p-[10px] sm:tw-p-[12px] xl:tw-p-[14px] 3xs:tw-text-base sm:tw-text-lg lg:tw-text-xl tw-font-medium" style="box-shadow: rgba(145,158,171,0.2) 0px 0px 2px 0px, rgba(145,158,171,0.12) 0px 12px 24px -4px;">
                    <template #item="{ item }">
                        <template v-if="item.url != bNavItems[bNavItems.length - 1].url">
                            <NuxtLink :to="item.url" class="tw-text-palText hover:tw-text-pal1">{{ item.label }}</NuxtLink>
                        </template>
                        <template v-else>
                            <span class="tw-text-pal1">{{ item.label }}</span>
                        </template>
                    </template>
                </Breadcrumb>
            </div>
            <div class="col-12">
                <div class="card">
                    <DataTable stripedRows showGridlines :value="local.fetchedViewData" :paginator="true" :rows="10" :rowHover="true" v-model:filters="filterTable" :loading="loadingTable" :filters="filterTable" :globalFilterFields="['nama_lengkap', 'no_telpon']">
                        <template #header>
                            <div class="flex justify-content-between flex-column sm:flex-row">
                                <div>
                                    <Button class="" outlined>
                                        <NuxtLink class="tw-text-pal1" to="/admin/tambah">Tambah</NuxtLink>
                                    </Button>
                                </div>
                                <IconField iconPosition="left">
                                    <InputIcon class="pi pi-search" />
                                    <InputText v-model="filterTable['global'].value" placeholder="Keyword Search" style="width: 100%" class="tw-border-palText hover:tw-border-palText focus:tw-border-palText focus:tw-outline-palText" />
                                </IconField>
                            </div>
                        </template>
                        <template #empty> Data Admin Tidak tersedia. </template>
                        <template #loading> Loading customers data. Please wait. </template>
                        <Column header="No" class="tw-w-[3%] tw-text-center">
                            <template #body="{ index }">
                                {{ index + 1 }}
                            </template>
                        </Column>
                        <Column field="nama_lengkap" header="Nama Lengkap" class="tw-w-[70%] tw-whitespace-normal"></Column>
                        <Column field="no_telpon" header="No Telpon" class="tw-w-[10%]"></Column>
                        <Column field="" header="Aksi" class="tw-w-auto tw-flex tw-gap-2">
                            <template #body="{ data }">
                                <Button type="button" outlined class="tw-px-2 tw-py-2 tw-flex-1">
                                    <NuxtLink :to="{ name: 'AdminDetail', params: { adminDetail: data.uuid }}" class="tw-w-full tw-flex tw-justify-center tw-items-center tw-gap-1 tw-text-palText">
                                        <I_Lihat class="tw-w-10"/>
                                        <span>Lihat</span>
                                    </NuxtLink>
                                </Button>
                                <Button type="button" outlined class="tw-flex-1 tw-flex tw-justify-around tw-items-center tw-gap-1 tw-text-palText tw-px-2" @click="local.idDelete = data.uuid; local.diCon = true;">
                                    <I_Hapus class="tw-w-10"/>
                                    <span>Hapus</span>
                                </Button>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
        <Dialog v-model:visible="local.diCon" header="Konfirmasi Hapus Admin" :modal="true" :closeOnEscape="true" :dismissableMask="true" class="">
            <div class="tw-flex tw-flex-col">
                <span class="3xs:tw-text-sm xs:tw-text-base sm:tw-text-lg md:tw-text-xl lg:tw-text-2xl xl:tw-text-3xl">Apakah ingin menghapus admin ?</span>
                <div class="tw-flex tw-justify-between tw-mt-5">
                    <Button class="tw-w-max 3xs:tw-rounded-sm 3xs:tw-px-1.5 xs:tw-px-2 lg:tw-px-5 3xs:tw-py-1.5 tw-text-palBg 3xs:tw-text-xs xs:tw-text-sm sm:tw-text-lg md:tw-text-xl lg:tw-text-2xl xl:tw-text-3xl" @click="local.diCon = false">Batal</Button>
                    <Button class="tw-w-max 3xs:tw-rounded-sm 3xs:tw-px-1.5 xs:tw-px-2 lg:tw-px-5 3xs:tw-py-1.5 tw-text-palBg 3xs:tw-text-xs xs:tw-text-sm sm:tw-text-lg md:tw-text-xl lg:tw-text-2xl xl:tw-text-3xl" @click="deleteAdmin()">Hapus</Button>
                </div>
            </div>
        </Dialog>
    </section>
</template>
<style scoped lang="scss">
@keyframes shine{
    to {
        background-position: 0% 0%;
    }
}
</style>
<script setup lang="ts">
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { ref, watch, reactive, onBeforeMount } from 'vue';
import { eventBus } from '~/app/eventBus';
import NotFoundComponent from '~/components/NotFound.vue';
import { useFetchDataStore } from "~/store/FetchData";
import { useNotFoundStore } from '~/store/NotFound';
import { DeleteAdmin } from '~/composables/api/admin';
import I_Lihat from '~/assets/icons/edit.svg';
import I_Hapus from '~/assets/icons/trash.svg';
const publicConfig = useRuntimeConfig().public;
const fetchDataS = useFetchDataStore();
const toast = useToast();
definePageMeta({
    layout:'authenticated',
});
useHead({
    title:`Kelola Admin | ${publicConfig.appName}`
});
const local = reactive({
    isRequestInProgress: false,
    isDoneFetch: false,
    diCon: false,
    idDelete: '',
    fetchedViewData: null as any,
});
const loadingTable: Ref = ref(null);
const filterTable: Ref = ref(null);
onBeforeMount(() => {
    loadingTable.value = false
    initFilters1();
});
watch(() => fetchDataS.processFetch.isDone, async() => {
    if(fetchDataS.processFetch.isDone == 'loading' || fetchDataS.processFetch.isDone == 'error') return;
    const res = await fetchDataS.fetchPage();
    if(res ==  undefined || res.status == 'error'){
        return;
    }
    local.fetchedViewData = res.data;
}, { immediate:true });
const bNavIcon = ref({ label: 'Dashboard', url: '/beranda' });
const bNavItems = ref([
    { label: 'Kelola Admin', url: '/admin' },
]);
const initFilters1 = () => {
    filterTable.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        nama_lengkap: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        no_telpon: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    };
};
const deleteAdmin = async () => {
    if(local.idDelete == '') return;
    local.isRequestInProgress = true;
    eventBus.emit('showLoading');
    let res = await DeleteAdmin({ id_admin: local.idDelete });
    if(res.status === 'success'){
        const lDelete = local.fetchedViewData.findIndex((item: any) => item.uuid === local.idDelete);
        if (lDelete > -1) local.fetchedViewData.splice(lDelete, 1);
        local.diCon = false;
        local.idDelete = '';
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        toast.add({ severity: 'success', summary: 'Berhasil Hapus Admin', detail: res.message, group: 'br', life: 3000 });
        eventBus.emit('showGreenPopup', res.message);
    }else if(res.status === 'error'){
        local.isRequestInProgress = false;
        local.diCon = false;
        eventBus.emit('closeLoading');
        toast.add({ severity: 'error', summary: 'Gagal Hapus Admin', detail: res.message, group: 'br', life: 3000 });
    }
}
</script>