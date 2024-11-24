<template>
    <template v-if="useNotFoundStore().isNotFound || fetchDataS.processFetch.isDone  == 'error'"><NotFoundComponent/></template>
    <section v-else class="tw-w-full tw-relative" style="min-height: calc(100vh - 7rem - (2rem + 20px) - 20px);">
        <div class="col-12">
            <div class="">
                <h1 class="3xs:tw-text-3xl lg:tw-text-4xl tw-font-semibold">Tambah Admin</h1>
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
            <form class="tw-relative tw-bg-white tw-border-palBg tw-border-1 tw-rounded-xl tw-mt-5 3xs:tw-p-3 xs:tw-p-4 sm:tw-p-7 md:tw-p-8 xl:tw-p-7 sm:tw-pb-3 md:tw-pb-4 xl:tw-pb-5" style="border: 1px solid #e2e8f0; box-shadow: rgba(145,158,171,0.2) 0px 0px 2px 0px, rgba(145,158,171,0.12) 0px 12px 24px -4px;" @submit.prevent="tambahAdminForm()">
                <div class="3xs:tw-w-full lg:tw-w-[75%] xl:tw-w-[78%] 2xl:tw-w-[82%] tw-flex tw-grow tw-flex-col md:tw-gap-0.5 xl:tw-gap-2 3xs:tw-mt-30 lg:tw-mt-0">
                    <label for="nama_lengkap" class="">Nama Lengkap</label>
                    <InputText type="text" ref="inpNamaLengkap" class="3xs:tw-rounded-md xl:tw-rounded-lg 3xs:tw-outline-0 3xs:focus:tw-outline-1 xl:focus:tw-outline-3 tw-text-palText tw-border-palText hover:tw-border-palText focus:tw-border-palText focus:tw-outline-palText" v-model="input.nama_lengkap" @input="inpChange('nama_lengkap')"/>
                </div>
                <div class="3xs:tw-w-full lg:tw-w-[75%] xl:tw-w-[78%] 2xl:tw-w-[82%] tw-mt-6 3xs:tw-mt-2 xl:tw-mt-4 2xl:tw-mt-5 tw-flex 3xs:tw-flex-col md:tw-flex-row 3xs:tw-gap-2 lg:tw-gap-5">
                    <div class="tw-flex tw-grow tw-flex-col md:tw-gap-0.5 xl:tw-gap-2.5">
                        <label for="jenis_kelamin" class="">Jenis Kelamin</label>
                        <Dropdown v-model="input.jenis_kelamin" ref="inpJenisKelamin" :options="jenisKelaminValue" optionLabel="name" optionValue="cosValue" class="tw-w-full tw-h-9 tw-flex tw-justify-center tw-items-center 3xs:tw-rounded-md xl:tw-rounded-lg 3xs:tw-outline-0 3xs:focus:tw-outline-1 xl:focus:tw-outline-3 tw-text-palText tw-border-palText tw-outline-palText hover:tw-border-palText focus:tw-border-palText focus:tw-outline-palText" placeholder="Pilih Jenis Kelamin" @input="inpChange('jenis_kelamin')">
                            <template #option="slotProps">
                                <span class="3xs:tw-text-sm sm:tw-text-base md:tw-text-lg lg:tw-text-xl xl:tw-text-2xl">{{ slotProps.option.name }}</span>
                            </template>
                            <template #value="slotProps">
                                <template v-if="!slotProps.value || slotProps.value.length === 0">
                                    <span class="tw-text-palText 3xs:tw-text-sm sm:tw-text-base md:tw-text-lg lg:tw-text-xl xl:tw-text-2xl">{{ slotProps.placeholder }}</span>
                                </template>
                                <template v-else>
                                    <span class="tw-text-palText 3xs:tw-text-base lg:tw-text-lg tw-m-0">{{ jenisKelaminValue.find(option => option.cosValue === slotProps.value)?.name || slotProps.placeholder }}</span>
                                </template>
                            </template>
                        </Dropdown>
                    </div>
                    <div class="tw-flex tw-grow tw-flex-col md:tw-gap-0.5 xl:tw-gap-2">
                        <label for="no_telpon" class="">No Telpon</label>
                        <InputText type="text" ref="inpNoTelpon" class="tw-w-full 3xs:tw-rounded-md xl:tw-rounded-lg 3xs:tw-outline-0 3xs:focus:tw-outline-1 xl:focus:tw-outline-3 tw-text-palText tw-border-palText hover:tw-border-palText focus:tw-border-palText focus:tw-outline-palText" v-model="input.no_telpon" @input="formatNoAdmin($event); inpChange('no_telpon')" />
                    </div>
                </div>
                <div class="tw-w-full tw-mt-6 3xs:tw-mt-2 sm:tw-mt-2.5 xl:tw-mt-4 2xl:tw-mt-5 tw-flex 3xs:tw-flex-col lg:tw-flex-row 3xs:tw-gap-2 lg:tw-gap-5">
                    <div class="tw-flex tw-grow tw-flex-col md:tw-gap-0.5">
                        <label for="email" class=""> Email</label>
                        <InputText type="email" ref="inpEmail" class="tw-w-full 3xs:tw-rounded-md xl:tw-rounded-lg 3xs:tw-outline-0 3xs:focus:tw-outline-1 xl:focus:tw-outline-3 tw-text-palText tw-border-palText hover:tw-border-palText focus:tw-border-palText focus:tw-outline-palText" v-model="input.email" @input="inpChange('email')"/>
                    </div>
                    <div class="tw-flex tw-grow tw-flex-col md:tw-gap-0.5">
                        <label for="password" class="">Password</label>
                        <div class="tw-relative tw-w-full tw-flex tw-items-center">
                            <InputText type="password" ref="inpPassword" class="tw-w-full 3xs:tw-rounded-md xl:tw-rounded-lg 3xs:tw-outline-0 3xs:focus:tw-outline-1 xl:focus:tw-outline-3 tw-text-palText tw-border-palText hover:tw-border-palText focus:tw-border-palText focus:tw-outline-palText" @input="inpChange('password')" v-model="input.password"/>
                            <div class="tw-absolute tw-top-1/2 -tw-translate-y-1/2 tw-right-3" @click="showPass">
                                <div class="tw-relative xl:tw-w-9 lg:tw-w-8 xl:tw-h-9 lg:tw-h-8 tw-flex tw-items-center tw-justify-center">
                                    <I_eye_slash class="tw-absolute xl:tw-w-9 lg:tw-w-8 tw-transition tw-duration-400" :class="input.password === '' || local.isPasswordShow ? 'tw-opacity-0 tw-pointer-events-none' : 'tw-opacity-100 tw-cursor-pointer'"/>
                                    <I_eye class="tw-absolute xl:tw-w-9 lg:tw-w-8 tw-transition tw-duration-[400ms]" :class="input.password === '' || !local.isPasswordShow ? 'tw-opacity-0 tw-pointer-events-none' : 'tw-opacity-100 tw-cursor-pointer'"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tw-absolute tw-w-[15%] 3xs:tw-w-[70%] 2xs:tw-w-[62%] xs:tw-w-[60%] phone:tw-w-[45%] sm:tw-w-[40%] md:tw-w-[31%] lg:tw-w-[22%] xl:tw-w-[20%] 2xl:tw-w-[17%] 3xs:tw-h-[18%] sm:tw-h-[22%] md:tw-h-[24%] lg:tw-h-[35%] 3xs:tw-top-[4%] lg:tw-top-[15%] xl:tw-top-[14%] 2xl:tw-top-[12%] 3xs:tw-left-1/2 3xs:-tw-translate-x-1/2 lg:tw-translate-x-0 lg:tw-right-[3%] xl:tw-right-[2.2%] 2xl:tw-right-[1.2%] lg:tw-ml-auto tw-object-cover tw-flex tw-flex-col tw-justify-center tw-gap-2 tw-rounded-lg" :class="{
                    '' : input.linkFileProfile !== '',
                    'tw-border-black tw-border-dashed tw-border-3' : input.linkFileProfile === ''
                }" @dragover.prevent="handleDragOverPersonal" @drop.prevent="handleDropPersonal" @click="handleFormClickPersonal">
                    <img :src="input.linkFileProfile" alt="" class="tw-w-full tw-h-full tw-object-contain tw-absolute" :class="{ 'tw-hidden': input.linkFileProfile === ''}">
                    <I_Drop class="tw-mt-2 tw-h-15 tw-relative tw-top-2 tw-pointer-events-none" :class="{ 'hidden': input.linkFileProfile !== ''}"/>
                    <span class="tw-text-center tw-text-lg" :class="{ 'tw-hidden': input.linkFileProfile !== ''}">Pilih atau jatuhkan file gambar</span>
                    <input type="file" class="tw-hidden" ref="fileInputProfile" @change="handleFileChangePersonal">
                </div>
                <div class="tw-relative 3xs:tw-w-full md:tw-w-max tw-h-max md:tw-right-0 md:tw-ml-auto 3xs:tw-mt-6 sm:tw-mt-8 xl:tw-mt-10 2xl:tw-mt-12 3xs:tw-mb-1.5 sm:tw-mb-2 md:tw-mb-3 tw-flex 3xs:tw-justify-between md:tw-gap-5 tw-text-palText">
                    <Button class="tw-w-max tw-flex tw-justify-center tw-items-center tw-text-palBg 3xs:tw-px-1.5 xs:tw-px-2 md:tw-px-3 lg:tw-px-3 xl:tw-px-3 3xs:tw-text-sm xs:tw-text-base sm:tw-text-lg lg:tw-text-xl">
                        <NuxtLink to="/admin" class="tw-text-palBg tw-w-full tw-h-full tw-flex tw-justify-center tw-items-center">Kembali</NuxtLink>
                    </Button>
                    <Button type="submit" class="tw-flex tw-justify-around tw-items-center 3xs:tw-gap-1 sm:tw-gap-2 xl:tw-gap-2.5 tw-w-max 3xs:tw-px-2 lg:tw-px-3 tw-text-palBg">
                        <I_Tambah class="3xs:tw-w-5 xs:tw-w-6 sm:tw-w-7 lg:tw-w-8"/>
                        <span class="3xs:tw-text-base sm:tw-text-lg">Tambah</span>
                    </Button>
                </div>
            </form>
        </div>
    </section>
</template>
<script setup lang="ts">
import * as z from 'zod';
import { useToast } from 'primevue/usetoast';
import { ref, reactive, watch } from 'vue';
import { eventBus } from '~/app/eventBus';
import NotFoundComponent from '~/components/NotFound.vue';
import { useFetchDataStore } from '~/store/FetchData';
import { useNotFoundStore } from '~/store/NotFound';
import { TambahAdmin } from '~/composables/api/admin';
import I_eye from '~/assets/icons/eye.svg';
import I_eye_slash from '~/assets/icons/eye-slash.svg';
import I_Drop from '~/assets/icons/admin/drop.svg';
import I_Tambah from '~/assets/icons/plus.svg';
const publicConfig = useRuntimeConfig().public;
const fetchDataS = useFetchDataStore();
const toast = useToast();
definePageMeta({
    layout:'authenticated',
});
useHead({
    title:`Tambah Admin | ${publicConfig.appName}`
});
const local = reactive({
    isRequestInProgress: false,
    isPasswordShow: false,
});
const input = reactive({
    nama_lengkap: '',
    jenis_kelamin: '',
    no_telpon: '',
    email: '',
    password: '',
    linkFileProfile: '',
    foto: null as File | null,
});
const bNavIcon = ref({ label: 'Dashboard', url: '/dashboard' });
const bNavItems = ref([
    { label: 'Kelola Admin', url: '/admin' },
    { label: 'Tambah', url: '/admin/tambah' },
]);
const inpNamaLengkap: Ref = ref(null);
const inpJenisKelamin: Ref = ref(null);
const inpNoTelpon: Ref = ref(null);
const inpEmail: Ref = ref(null);
const inpPassword: Ref = ref(null);
const fileInputProfile: Ref = ref(null);
const jenisKelaminValue = ref([
    { name: 'Laki-laki', cosValue: 'laki-laki' },
    { name: 'Perempuan', cosValue: 'perempuan' },
]);
watch(() => fetchDataS.processFetch.isDone, async() => {
    if(fetchDataS.processFetch.isDone == 'loading' || fetchDataS.processFetch.isDone == 'error') return;
    const res = await fetchDataS.fetchPage();
    if(res ==  undefined || res.status == 'error'){
        return;
    }
}, { immediate:true });
const formatNoAdmin = (event: any) => {
    input.no_telpon = event.target.value.replace(/\D/g, '').slice(0, 13);
    event.target.value = input.no_telpon;
};
const cosValidationObject = {
    nama_lengkap: inpNamaLengkap,
    jenis_kelamin: inpJenisKelamin,
    no_telpon: inpNoTelpon,
    email: inpEmail,
    password: inpPassword,
    foto: fileInputProfile,
};
const cosValidationArray = Object.keys(cosValidationObject) as Array<keyof typeof cosValidationObject>;
type cosValidationType = typeof cosValidationArray[number];
const getElement = (key: keyof typeof cosValidationObject) => {
    return cosValidationObject[key].value?.$el;
};
const showPass = () => {
    if(local.isPasswordShow){
        getElement('password').type = 'password';
        local.isPasswordShow = false;
    }else{
        getElement('password').type = 'text';
        local.isPasswordShow = true;
    }
};
const handleFormClickPersonal = () => {
    fileInputProfile.value.click();
};
const handleFileChangePersonal = (event: any) => {
    handleFiles(event.target.files);
};
const handleDragOverPersonal = (event: Event) => {
    event.preventDefault();
};
const handleDropPersonal = (event: any) => {
    event.preventDefault();
    handleFiles(event.dataTransfer.files);
};
const handleFiles = async(files: FileList) => {
    if (files.length !== 1) {
        throw new Error('Please select only one file.');
    }
    const file = files[0];
    if (!file.type.startsWith('image/')) {
        throw new Error('Please select an image file.');
    }
    if (file.size > 2 * 1024 * 1024) {
        throw new Error('File size exceeds the maximum limit of 2 MB.');
    }
    input.foto = file;
    input.linkFileProfile = URL.createObjectURL(file);
};
const formSchema = z.object({
    nama_lengkap: z.string().min(1, 'Nama lengkap harus diisi!').max(50, 'Nama Lengkap maksimal 50 karakter'),
    jenis_kelamin: z.enum(['laki-laki', 'perempuan'], { message: 'Jenis kelamin harus dipilih!' }),
    no_telpon: z.string().min(10, 'Nomor telepon minimal 10 digit!').max(15, 'Nomor telepon maksimal 15 digit!').regex(/^\d+$/, 'Nomor telepon harus berupa angka!'),
    email: z.string().min(1, 'Email Harus diisi!').max(45, 'Email maksimal 45 karakter').email('Masukkan email dengan benar!'),
    password: z.string().min(1, 'Password Harus diisi !')
        .min(8, 'Password minimal 8 karakter !').max(40, 'Password maksimal 40 karakter !')
        .regex(/[A-Z]/, 'Password minimal ada 1 huruf kapital !')
        .regex(/[a-z]/, 'Password minimal ada 1 huruf kecil !')
        .regex(/\d/, 'Password minimal ada 1 angka !')
        .regex(/[!@#$%^&*]/, 'Password minimal ada 1 karakter unik !'),
    foto: z.union([z.instanceof(File), z.null()]).refine(file => file !== null, { message: 'Foto harus dipilih!' }).refine(file => !file || file.size <= 2 * 1024 * 1024, { message: 'File size exceeds the maximum limit of 2 MB.' })
});
const checkValidator = () => {
    const validSchema = formSchema.safeParse(input);
    if(validSchema.success){
        return { status: 'success' };
    }
    let errMessage = '', divItem = '', dValid = validSchema.error.format();
    Object.keys(dValid).filter((key) => (cosValidationArray as readonly string[]).includes(key)).forEach((key: string) => {
        if(errMessage == ''){
            const fieldValue = dValid[key as keyof typeof dValid];
            if (typeof fieldValue === 'object' && fieldValue !== null && '_errors' in fieldValue) {
                const fieldErrors = fieldValue._errors;
                if (fieldErrors && fieldErrors.length > 0) {
                    divItem = key;
                    errMessage = fieldErrors[0];
                }
            }
        }
    });
    return { status: 'error', message: errMessage, item: divItem };
}
const inpChange = (div: cosValidationType, isError = false, isNeedValidator = false): void => {
    if(isNeedValidator){
        const isValidForm = checkValidator();
        if(isValidForm.status == 'error') return inpChange(isValidForm.item as cosValidationType, true);
    }
    const itemE = getElement(div);
    if(!itemE) return console.warn(`Element for ${div} not found.`);
    if(isError){
        itemE.classList.remove('tw-border-palText', 'hover:tw-border-palText', 'focus:tw-border-palText', 'focus:tw-outline-palText');
        itemE.classList.add('tw-border-popup_error', 'hover:tw-border-popup_error', 'focus:tw-border-popup_error', 'focus:tw-outline-popup_error');
    }else{
        itemE.classList.remove('tw-border-popup_error', 'hover:tw-border-popup_error', 'focus:tw-border-popup_error', 'focus:tw-outline-popup_error');
        itemE.classList.add('tw-border-palText', 'hover:tw-border-palText', 'focus:tw-border-palText', 'focus:tw-outline-palText');
    }
};
const tambahAdminForm = async() => {
    if(local.isRequestInProgress) return;
    const isValidForm = checkValidator();
    if(isValidForm.status == 'error'){
        inpChange(isValidForm.item as cosValidationType, true);
        return toast.add({ severity: 'error', summary: 'Gagal Tambah Admin', detail: isValidForm.message, group: 'br', life: 3000 })
    }
    const formData = {
        nama_lengkap: input.nama_lengkap,
        jenis_kelamin: input.jenis_kelamin,
        no_telpon: input.no_telpon,
        email: input.email,
        password: input.password,
        foto: input.foto as File,
    }
    local.isRequestInProgress = true;
    eventBus.emit('showLoading');
    let res = await TambahAdmin(formData);
    if(res.status === 'success'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        toast.add({ severity: 'success', summary: 'Berhasil Tambah Admin', detail: res.message, group: 'br', life: 3000 });
        setTimeout(function(){
            navigateTo('/admin');
        }, 1500);
    }else if(res.status === 'error'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        toast.add({ severity: 'error', summary: 'Gagal Tambah Admin', detail: res.message, group: 'br', life: 3000 });
        cosValidationArray.forEach((field) => {
            if (res.message.toLowerCase().includes(field)) {
                inpChange(field, true);
            }
        });
    }
};
</script>