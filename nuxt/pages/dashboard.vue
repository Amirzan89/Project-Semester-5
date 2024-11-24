<template>
    <template v-if="useNotFoundStore().isNotFound || fetchDataS.processFetch.isDone  == 'error'"><NotFoundComponent/></template>
    <section v-else class="tw-flex tw-flex-col" style="min-height: calc(100vh - 7rem - (2rem + 20px) - 20px);">
        <div class="">
            <h1 class="3xs:tw-text-3xl lg:tw-text-4xl tw-font-semibold">Beranda</h1>
            <Breadcrumb :home="bNavIcon" class="3xs:tw-p-[10px] sm:tw-p-[12px] xl:tw-p-[14px] 3xs:tw-text-base sm:tw-text-lg lg:tw-text-xl tw-font-medium" style="box-shadow: rgba(145,158,171,0.2) 0px 0px 2px 0px, rgba(145,158,171,0.12) 0px 12px 24px -4px;">
            <template #item="{ item }">
                <span class="tw-text-pal1">{{ item.label }}</span>
            </template>
            </Breadcrumb>
        </div>
        <div class="grid" style="margin-top:5px">
            <div class="col-12 lg:col-6">
                <div class="card mb-0">
                    <div class="flex justify-content-between mb-3">
                        <div>
                            <span class="block text-500 font-medium mb-3">Jumlah Green House</span>
                            <div class="text-900 font-medium text-xl">{{ local.jumlah_gh }}</div>
                        </div>
                        <div class="flex align-items-center justify-content-center bg-blue-100 border-round" style="width: 2.5rem; height: 2.5rem">
                            <i class="pi pi-map-marker text-blue-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 lg:col-6">
                <div class="card mb-0">
                    <div class="flex justify-content-between mb-3">
                        <div>
                            <span class="block text-500 font-medium mb-3">Jumlah Users</span>
                            <div class="text-900 font-medium text-xl">{{ local.jumlah_users }}</div>
                        </div>
                        <div class="flex align-items-center justify-content-center bg-orange-100 border-round" style="width: 2.5rem; height: 2.5rem">
                            <i class="pi pi-user text-blue-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tw-flex-d1 tw-flex tw-gap-2" style="height: calc((100vh - 7rem - (2rem + 20px) - 20px) - 270px)">
            <div class="card tw-w-full tw-h-full"><FullCalendar class="tw-h-full" :options="calendarOptions"/></div>
            <div class="card tw-overflow-x-auto tw-overflow-y-auto">
                <Timeline :value="groupedEvents" class="tw-w-110 tw-items-start">
                    <template #opposite="timeData">
                        <div class="tw-w-max">{{ formatTgl(new Date(timeData.item.date)) }}</div>
                    </template>
                    <template #content="timeData">
                        <template v-if="timeData.item.statuses.length > 1">
                            <ul class="tw-p-0">
                                <li v-for="(status, index) in timeData.item.statuses" :key="index" class="tw-list-none tw-cursor-pointer" @click="showDetailTL(timeData.item.ids[index])">
                                {{ status }}
                                </li>
                            </ul>
                        </template>
                        <template v-else>
                            <div class="tw-cursor-pointer" @click="showDetailTL(timeData.item.ids[0])">
                                {{ timeData.item.statuses[0] }}
                            </div>
                        </template>
                    </template>
                </Timeline>
            </div>
        </div>
        <Dialog v-model:visible="local.dialogVisible" header="" :modal="true" :closeOnEscape="true" :dismissableMask="true" class="3xs:tw-w-[90%] lg:tw-w-[80%] xl:tw-w-[70%] 2xl:tw-w-[60%]">
            <form class="p-fluid">
                <div class="field tw-whitespace-normal tw-break-words">
                    <label for="nama_gh">Nama Acara</label>
                    <InputText type="text" disabled class="tw-bg-palBg 3xs:tw-rounded-md xl:tw-rounded-lg 3xs:tw-outline-0 3xs:focus:tw-outline-1 xl:focus:tw-outline-3 tw-text-palText tw-border-palText hover:tw-border-palText focus:tw-border-palText focus:tw-outline-palText" :value="input.nama_acara"/>
                </div>
                <div class="field">
                    <label for="nama_gh">Deskripsi Acara</label>
                    <Textarea type="text" disabled class="tw-resize-none tw-bg-palBg 3xs:tw-h-25 xs:tw-h-30 sm:tw-h-35 lg:tw-h-40 xl:tw-h-45 3xs:tw-pl-[7px] xl:tw-pl-3 3xs:tw-rounded-md xl:tw-rounded-lg 3xs:tw-outline-0 3xs:focus:tw-outline-1 xl:focus:tw-outline-3 tw-text-palText tw-border-palText hover:tw-border-palText focus:tw-border-palText focus:tw-outline-palText" v-model="input.deskripsi_acara"/>
                </div>
                <div class="3xs:tw-mt-0 lg:tw-mt-2 xl:tw-mt-3">
                    <div class="formgrid grid">
                        <div class="col">
                            <label for="tanggal_awal">Tanggal Mulai</label>
                            <InputText type="text" disabled class="tw-bg-palBg 3xs:tw-rounded-md xl:tw-rounded-lg 3xs:tw-outline-0 3xs:focus:tw-outline-1 xl:focus:tw-outline-3 tw-text-palText tw-border-palText hover:tw-border-palText focus:tw-border-palText focus:tw-outline-palText" :value="formatTgl(input.tgl_awal)"/>
                        </div>
                        <div class="col">
                            <label for="tanggal_akhir">Tanggal Selesai</label>
                            <InputText type="text" disabled class="tw-bg-palBg 3xs:tw-rounded-md xl:tw-rounded-lg 3xs:tw-outline-0 3xs:focus:tw-outline-1 xl:focus:tw-outline-3 tw-text-palText tw-border-palText hover:tw-border-palText focus:tw-border-palText focus:tw-outline-palText" :value="formatTgl(input.tgl_akhir)"/>
                        </div>
                    </div>
                </div>
                <div class="tw-w-max tw-relative tw-left-1/2 -tw-translate-x-1/2">
                    <Button type="button" class="tw-w-max 3xs:tw-mt-5 xl:tw-mt-7 2xl:tw-mt-10 3xs:tw-rounded-md xl:tw-rounded-xl 3xs:tw-px-2 xs:tw-px-2 lg:tw-px-4 3xs:tw-py-0.5 lg:tw-py-1.5 tw-text-palBg 3xs:tw-text-base sm:tw-text-lg md:tw-text-xl lg:tw-text-2xl" @click="local.dialogVisible = false">Kembali</Button>
                    <!-- <Button class="tw-w-full tw-flex tw-justify-center tw-items-center tw-text-palBg tw-text-xl" @click="local.dialogVisible = false">Kembali</Button> -->
                </div>
            </form>
        </Dialog>
    </section>
</template>
<script setup lang="ts">
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import { useToast } from 'primevue/usetoast';
import { ref, computed, reactive } from 'vue';
import { eventBus } from '~/app/eventBus';
import NotFoundComponent from '~/components/NotFound.vue';
import { useFetchDataStore } from "~/store/FetchData";
import { useNotFoundStore } from '~/store/NotFound';
import createAxios from "~/composables/api/axios";
const { axiosJson, createCSRF } = createAxios();
const publicConfig = useRuntimeConfig().public;
const fetchDataS = useFetchDataStore();
const toast = useToast();
definePageMeta({
    layout:'authenticated',
});
useHead({
    title:`Beranda | ${publicConfig.appName}`
});
const local = reactive({
    dialogVisible: false,
    isDoneFetch: false,
    acaraDat: null as any,
    jumlah_gh: 0,
    jumlah_users: 0,
});
const input = reactive({
    nama_acara: '',
    deskripsi_acara: '',
    tgl_awal: null as Date | null,
    tgl_akhir: null as Date | null,
});
const bNavIcon = ref({ label: 'Beranda', url: '/beranda' });
const cacheDetail: Ref = ref([]);
watch(() => fetchDataS.processFetch.isDone, async() => {
    if(fetchDataS.processFetch.isDone == 'loading' || fetchDataS.processFetch.isDone == 'error') return;
    const res = await fetchDataS.fetchPage();
    if(res ==  undefined || res.status == 'error'){
        return;
    }
    local.jumlah_gh = res.data.jumlah_gh;
    local.jumlah_users = res.data.jumlah_users;
    local.acaraDat = res.data.acara;
    calendarOptions.value.events = (local.acaraDat as any).map((acara: any) => ({
        title: acara.nama_acara,
        id: acara.id_acara,
        start: acara.tgl_awal,
    }));
}, { immediate:true });
const manageCache = async(idAcara: any) => {
    interface Response{
        status: string,
        code?: number,
        message?: string,
        data?: any,
    }
    const maxCache = 3;
    let retryCount = 0;
    const fetchCache = async (idAcara: any): Promise<Response> => {
        try{
            const cIndex = cacheDetail.value.findIndex((item: any) => item.id_acara == idAcara);
            if(cIndex > -1){
                return { status: 'success', data: JSON.parse(JSON.stringify(cacheDetail.value[cIndex])) };
            }
            const res: Record<string, any> = await axiosJson.get(`/beranda`, { params: { idAcara: idAcara, _: Date.now()}});
            if(cacheDetail.value.length > maxCache){
                cacheDetail.value.shift();
            }
            res.data.data['id_acara'] = idAcara;
            cacheDetail.value.push(res.data.data);
            return res.data;
        }catch(err: any){
            if (err.response){
                if(err.response.status === 401){
                    navigateTo('/login');
                }
                if(err.response.status === 404){
                    return { status:'error', message: err.response.data.message, code: 404 };
                }
                if(err.response.status === 419) {
                    if (retryCount <= 3) {
                        retryCount++;
                        createCSRF();
                        return fetchCache(idAcara);
                    } else {
                        retryCount = 0;
                        return { status: 'error', message: 'Request failed' };
                    }
                }
                if(err.response.status === 500){
                    return { status: 'error', message: err.response.data.message };
                }
                return { status:'error', message: err.response.data.message };
            }
            return { status:'error', message: err };
        }
    }
    return await fetchCache(idAcara);
}
const formatTgl = (inpTgl: Date) => {
    const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    const day = inpTgl.getDate();
    const month = monthNames[inpTgl.getMonth()];
    const year = inpTgl.getFullYear();
    return `${day} ${month} ${year}`;
};
const showDetail = async(idAcara: any) => {
    eventBus.emit('showLoading');
    const fCache: any = await manageCache(idAcara);
    if(fCache.status == 'error'){
        if(fCache.code && fCache.code == 404){
            const lDelete = local.acaraDat.findIndex((item: any) => item.id_acara === idAcara);
            if (lDelete > -1) local.acaraDat.splice(lDelete, 1);
            const iDelete = cacheDetail.value.findIndex((item: any) => item.id_acara === idAcara);
            if (iDelete > -1) cacheDetail.value.splice(iDelete, 1);
        }
        return toast.add({ severity: 'error', summary: 'Gagal Get Data', detail: fCache.message, group: 'br', life: 3000 });;
    }
    input.nama_acara = fCache.data.nama_acara;
    input.deskripsi_acara = fCache.data.deskripsi_acara;
    input.tgl_awal = new Date(fCache.data.tgl_awal);
    input.tgl_akhir = new Date(fCache.data.tgl_akhir);
    local.dialogVisible = true;
    eventBus.emit('closeLoading');
}
const handleDateClick = async(arg: any) => await showDetail(arg.event.id);
const calendarOptions = ref({
    plugins: [dayGridPlugin, interactionPlugin ],
    initialView: 'dayGridMonth',
    selectable: true,
    eventClick: handleDateClick,
    events: []
});
const groupedEvents = computed(() => {
    const grouped: any = {};
    if(local.acaraDat == null){
        return;
    }
    (local?.acaraDat as any).map((acara: any) => ({
        status: acara.nama_acara,
        id: acara.id_acara,
        date: acara.tgl_awal,
    })).forEach((event: any) => {
        if(!grouped[event.date]){
            grouped[event.date] = { date: event.date, statuses: [], ids: [] };
        }
        grouped[event.date].statuses.push(event.status);
        grouped[event.date].ids.push(event.id);  
    });
    return Object.values(grouped).sort((a: any, b: any) => {
        const dateA = new Date(a.date.split(' ').reverse().join(' ')).getTime();
        const dateB = new Date(b.date.split(' ').reverse().join(' ')).getTime();
        return dateA - dateB;
    });
});
const showDetailTL = async(idAcara: any) => await showDetail(idAcara);
</script>