<template>
    <template v-if="useNotFoundStore().isNotFound || fetchDataS.processFetch.isDone  == 'error'"><NotFoundComponent/></template>
    <section v-else class="tw-w-full tw-flex tw-flex-col" style="min-height: calc(100vh - 7rem - (2rem + 20px) - 20px);">
        <div class="col-12">
            <div class="">
                <h1 class="3xs:tw-text-3xl lg:tw-text-4xl tw-font-semibold">Profile</h1>
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
            <TabView class="tw-mt-5" style="border: 1px solid #e2e8f0; box-shadow: rgba(145,158,171,0.2) 0px 0px 2px 0px, rgba(145,158,171,0.12) 0px 12px 24px -4px;">
                <TabPanel header="Profile">
                    <form class="">
                        <div class="tw-flex tw-items-center 3xs:tw-h-30 sm:tw-h-35 lg:tw-h-40 xl:tw-h-50 2xl:tw-h-55 tw-mb-4">
                            <div class="tw-relative 3xs:tw-w-[85%] xs:tw-w-[70%] phone:tw-w-[55%] sm:tw-w-[50%] md:tw-w-[40%] lg:tw-w-[32%] xl:tw-w-[30%] 2xl:tw-w-[25%] 3xs:tw-h-30 sm:tw-h-35 lg:tw-h-40 xl:tw-h-50 2xl:tw-h-55 tw-flex tw-flex-col tw-justify-center tw-mx-auto tw-cursor-pointer tw-gap-2 tw-rounded-lg">
                                <Skeleton v-if="isLoadingImg || inputProfile.linkImgProfile == ''" shape="rectangle" width="100%" height="100%" borderRadius="20px"/>
                                <img :src="inputProfile.linkImgProfile" alt="" class="tw-absolute tw-left-1/2 -tw-translate-x-1/2 tw-top-1/2 -tw-translate-y-1/2 tw-w-full tw-h-full tw-object-contain" :class="{ 'tw-hidden': inputProfile.linkImgProfile === ''}" @load="isLoadingImg = false" @error="isLoadingImg = false"/>
                            </div>
                        </div>
                        <div class="field grid">
                            <label for="nama_lengkap" class="col-12 mb-2 md:col-2 md:mb-0">Nama Lengkap</label>
                            <div class="col-12 md:col-10">
                                <span class="">{{ inputProfile.nama_lengkap }}</span>
                            </div>
                        </div>
                        <div class="field grid">
                            <label for="jenis_kelamin" class="col-12 mb-2 md:col-2 md:mb-0">Jenis Kelamin</label>
                            <div class="col-12 md:col-10">
                                <span class="">{{ inputProfile.jenis_kelamin }}</span>
                            </div>
                        </div>
                        <div class="field grid">
                            <label for="no_telpon" class="col-12 mb-2 md:col-2 md:mb-0">No Telpon</label>
                            <div class="col-12 md:col-10">
                                <span class="">{{ inputProfile.no_telpon }}</span>
                            </div>
                        </div>
                        <!-- <div class="field grid">
                            <label for="role" class="col-12 mb-2 md:col-2 md:mb-0">Role</label>
                            <div class="col-12 md:col-10">
                                <span class="">{{ inputProfile.role }}</span>
                            </div>
                        </div> -->
                        <div class="field grid">
                            <label for="email" class="col-12 mb-2 md:col-2 md:mb-0">Email</label>
                            <div class="col-12 md:col-10">
                                <span class="">{{ inputProfile.email }}</span>
                            </div>
                        </div>
                    </form>
                </TabPanel>
                <TabPanel header="Update Profile">
                    <form class="p-fluid tw-text-lg" @submit.prevent="updateProfileForm()">
                        <div class="field grid">
                            <label for="foto_profile" class="col-12 mb-2 md:col-2 md:mb-0">Foto Profile</label>
                            <div class="col-12 md:col-10">
                                <div class="3xs:tw-w-[85%] xs:tw-w-[70%] phone:tw-w-[55%] sm:tw-w-[50%] md:tw-w-[45%] lg:tw-w-[35%] xl:tw-w-[30%] 2xl:tw-w-[25%] 3xs:tw-h-30 sm:tw-h-35 lg:tw-h-40 xl:tw-h-50 2xl:tw-h-55 tw-flex tw-flex-col tw-justify-center tw-mx-auto tw-cursor-pointer tw-gap-2 tw-rounded-lg" :class="{
                                    'tw-border-black tw-border-dashed tw-border-3' : inputProfile.linkImgProfile === '' || inputProfile.isErrorFoto,
                                }" @dragover.prevent="handleDragOverPersonal" @drop.prevent="handleDropPersonal" @click="handleFormClickPersonal">
                                    <img :src="inputProfile.linkImgProfile" alt="" class="tw-w-full tw-h-full tw-object-contain" :class="{ 'tw-hidden': inputProfile.linkImgProfile === ''}" @load="inputProfile.isErrorFoto = false" @error="inputProfile.isErrorFoto = true">
                                    <I_Drop class="tw-mt-2 tw-h-15 tw-relative tw-top-2 tw-pointer-events-none" :class="inputProfile.linkImgProfile !== '' && !inputProfile.isErrorFoto ? 'tw-hidden' : ''"/>
                                    <span class="tw-text-center tw-text-lg" :class="inputProfile.linkImgProfile !== '' && !inputProfile.isErrorFoto ? 'tw-hidden' : ''">Pilih atau jatuhkan file gambar</span>
                                    <input type="file" class="tw-hidden" ref="fileInputProfile" @change="handleFileChangePersonal">
                                </div>
                            </div>
                        </div>
                        <div class="field grid">
                            <label for="nama_lengkap" class="col-12 mb-2 md:col-2 md:mb-0">Nama Lengkap</label>
                            <div class="col-12 md:col-10">
                                <InputText type="text" ref="inpNamaProfile" class="tw-border-palText hover:tw-border-palText focus:tw-border-palText focus:tw-outline-palText" v-model="inputProfile.nama_lengkap" @input="inpChange('nama_lengkap')"/>
                            </div>
                        </div>
                        <div class="field grid">
                            <label for="jenis_kelamin" class="col-12 mb-2 md:col-2 md:mb-0">Jenis Kelamin</label>
                            <div class="col-12 md:col-10">
                                <Dropdown :options="jenisKelaminValue" optionLabel="name" optionValue="cosValue" ref="inpJenisKelamin" class="tw-rounded-lg tw-border-palText tw-outline-palText hover:tw-border-palText focus:tw-border-palText focus:tw-outline-palText" placeholder="Pilih Jenis Kelamin" v-model="inputProfile.jenis_kelamin" @input="inpChange('jenis_kelamin')">
                                    <template #option="slotProps">
                                        <span>{{ slotProps.option.name }}</span>
                                    </template>
                                    <template #value="slotProps">
                                        <template v-if="!slotProps.value || slotProps.value.length === 0">
                                            <span>{{ slotProps.placeholder }}</span>
                                        </template>
                                        <template v-else>
                                            <span>{{ jenisKelaminValue.find(option => option.cosValue === slotProps.value)?.name || slotProps.placeholder }}</span>
                                        </template>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                        <div class="field grid">
                            <label for="no_telpon" class="col-12 mb-2 md:col-2 md:mb-0">No Telpon</label>
                            <div class="col-12 md:col-10">
                                <InputText type="text" ref="inpNoTelpon" class="tw-border-palText hover:tw-border-palText focus:tw-border-palText focus:tw-outline-palText" v-model="inputProfile.no_telpon" @input="formatNoProfile($event); inpChange('no_telpon')"/>
                            </div>
                        </div>
                        <!-- <div class="field grid">
                            <label for="role" class="col-12 mb-2 md:col-2 md:mb-0">Role</label>
                            <div class="col-12 md:col-10">
                                <InputText type="text" ref="inpRole" class="tw-border-palText hover:tw-border-palText focus:tw-border-palText focus:tw-outline-palText" />
                            </div>
                        </div> -->
                        <div class="field grid">
                            <label for="email" class="col-12 mb-2 md:col-2 md:mb-0">Email</label>
                            <div class="col-12 md:col-10">
                                <InputText type="email" ref="inpEmail" class="tw-border-palText hover:tw-border-palText focus:tw-border-palText focus:tw-outline-palText" v-model="inputProfile.email" @input="inpChange('email')"/>
                            </div>
                        </div>
                        <div class="tw-w-max tw-relative tw-left-1/2 -tw-translate-x-1/2 3xs:tw-mt-8 lg:tw-mt-10 2xl:tw-mt-5 tw-mb-0">
                            <Button type="submit" class="tw-flex tw-justify-center tw-items-center tw-text-palBg 3xs:tw-px-3.5 3xs:tw-py-2 3xs:tw-text-lg xs:tw-text-xl sm:tw-text-2xl lg:tw-text-3xl 2xl:tw-text-3xl">Edit</Button>
                        </div>
                    </form>
                </TabPanel>
                <TabPanel header="Ganti Password">
                    <form class="p-fluid" @submit.prevent="updatePasswordForm()">
                        <div class="field grid">
                            <label for="password_lama" class="col-12 mb-2 md:col-2 md:mb-0">Password lama</label>
                            <div class="col-12 md:col-10">
                                <div class="tw-relative tw-w-full tw-flex tw-items-center">
                                    <InputText type="password" ref="inpPassLama" class="tw-border-palText hover:tw-border-palText focus:tw-border-palText focus:tw-outline-palText" v-model="inputResetPass.pass_lama" @input="inpChange('pass_lama')" />
                                    <div class="tw-absolute tw-top-1/2 -tw-translate-y-1/2 tw-right-3" @click="showPass('lama')">
                                        <div class="tw-relative xl:tw-w-9 lg:tw-w-8 xl:tw-h-9 lg:tw-h-8 tw-flex tw-items-center tw-justify-center">
                                            <I_eye_slash class="tw-absolute tw-fill-palText xl:tw-w-9 lg:tw-w-8 tw-transition tw-duration-400" :class="inputResetPass.pass_lama === '' || inputResetPass.isPasswordLamaShow ? 'tw-opacity-0 tw-pointer-events-none' : 'tw-opacity-100 tw-cursor-pointer'"/>
                                            <I_eye class="tw-absolute tw-fill-palText xl:tw-w-9 lg:tw-w-8 tw-transition tw-duration-[400ms]" :class="inputResetPass.pass_lama === '' || !inputResetPass.isPasswordLamaShow ? 'tw-opacity-0 tw-pointer-events-none' : 'tw-opacity-100 tw-cursor-pointer'"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="field grid">
                            <label for="password_baru" class="col-12 mb-2 md:col-2 md:mb-0">Password baru</label>
                            <div class="col-12 md:col-10">
                                <div class="tw-relative tw-w-full tw-flex tw-items-center">
                                    <InputText type="password" ref="inpPassBaru" class="tw-border-palText hover:tw-border-palText focus:tw-border-palText focus:tw-outline-palText" v-model="inputResetPass.pass_baru" @input="inpChange('pass_baru')" />
                                    <div class="tw-absolute tw-top-1/2 -tw-translate-y-1/2 tw-right-3" @click="showPass('baru')">
                                        <div class="tw-relative xl:tw-w-9 lg:tw-w-8 xl:tw-h-9 lg:tw-h-8 tw-flex tw-items-center tw-justify-center">
                                            <I_eye_slash class="tw-absolute tw-fill-palText xl:tw-w-9 lg:tw-w-8 tw-transition tw-duration-400" :class="inputResetPass.pass_baru === '' || inputResetPass.isPasswordBaruShow ? 'tw-opacity-0 tw-pointer-events-none' : 'tw-opacity-100 tw-cursor-pointer'"/>
                                            <I_eye class="tw-absolute tw-fill-palText xl:tw-w-9 lg:tw-w-8 tw-transition tw-duration-[400ms]" :class="inputResetPass.pass_baru === '' || !inputResetPass.isPasswordBaruShow ? 'tw-opacity-0 tw-pointer-events-none' : 'tw-opacity-100 tw-cursor-pointer'"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="field grid">
                            <label for="ulangi_password_baru" class="col-12 mb-2 md:col-2 md:mb-0">Masukkan kembali Password Baru</label>
                            <div class="col-12 md:col-10">
                                <div class="tw-relative tw-w-full tw-flex tw-items-center">
                                    <InputText type="password" ref="inpPassBaruUlangi" class="tw-border-palText hover:tw-border-palText focus:tw-border-palText focus:tw-outline-palText" v-model="inputResetPass.pass_baru_ulangi" @input="inpChange('pass_baru_ulangi')" />
                                    <div class="tw-absolute tw-top-1/2 -tw-translate-y-1/2 tw-right-3" @click="showPass('ulangi')">
                                        <div class="tw-relative xl:tw-w-9 lg:tw-w-8 xl:tw-h-9 lg:tw-h-8 tw-flex tw-items-center tw-justify-center">
                                            <I_eye_slash class="tw-absolute tw-fill-palText xl:tw-w-9 lg:tw-w-8 tw-transition tw-duration-400" :class="inputResetPass.pass_baru_ulangi === '' || inputResetPass.isPasswordBaruUlangiShow ? 'tw-opacity-0 tw-pointer-events-none' : 'tw-opacity-100 tw-cursor-pointer'"/>
                                            <I_eye class="tw-absolute tw-fill-palText xl:tw-w-9 lg:tw-w-8 tw-transition tw-duration-[400ms]" :class="inputResetPass.pass_baru_ulangi === '' || !inputResetPass.isPasswordBaruUlangiShow ? 'tw-opacity-0 tw-pointer-events-none' : 'tw-opacity-100 tw-cursor-pointer'"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tw-w-max tw-relative tw-left-1/2 -tw-translate-x-1/2 tw-mt-5 tw-mb-0">
                            <Button type="submit" class="tw-flex tw-justify-center tw-items-center tw-px-6 tw-text-palBg tw-text-3xl">Ubah Password</Button>
                        </div>
                    </form>
                </TabPanel>
            </TabView>
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
import { updatePassword, updateProfile } from '~/composables/api/auth';
import I_eye from '~/assets/icons/eye.svg';
import I_eye_slash from '~/assets/icons/eye-slash.svg';
import I_Drop from '~/assets/icons/admin/drop.svg';
const publicConfig = useRuntimeConfig().public;
const fetchDataS = useFetchDataStore();
const toast = useToast();
definePageMeta({
    layout:'authenticated',
});
useHead({
    title:`Profile | ${publicConfig.appName}`
});
const local = reactive({
    isRequestInProgress: false,
    fetchedUserAuth: null as any,
});
const inputProfile = reactive({
    isUpdated: false,
    nama_lengkap: '',
    jenis_kelamin: '',
    no_telpon: '',
    email: '',
    linkImgProfile: '',
    foto:null as File | null,
    isErrorFoto: false,
});
const inputResetPass = reactive({
    pass_lama: '',
    pass_baru: '',
    pass_baru_ulangi: '',
    isPasswordLamaShow:false,
    isPasswordBaruShow:false,
    isPasswordBaruUlangiShow:false,
});
const bNavIcon = ref({ label: 'Dashboard', url: '/beranda' });
const bNavItems = ref([
    { label: 'Profile', url: '/profile' },
]);
const isLoadingImg: Ref = ref(true);
const inpNamaProfile: Ref = ref(null);
const inpEmail: Ref = ref(null);
const inpJenisKelamin: Ref = ref(null);
const inpNoTelpon: Ref = ref(null);
const fileInputProfile: Ref = ref(null);
const jenisKelaminValue = ref([
    { name: 'Laki-laki', cosValue: 'laki-laki' },
    { name: 'Perempuan', cosValue: 'perempuan' },
]);
// const roleValue = ref([
//     { name: 'admin', cosValue: 'admin' },
//     { name: 'admin', cosValue: 'admin' },
// ]);
const inpPassLama: Ref = ref(null);
const inpPassBaru: Ref = ref(null);
const inpPassBaruUlangi: Ref = ref(null);
watch(() => fetchDataS.processFetch.isDone, async() => {
    if(fetchDataS.processFetch.isDone == 'loading' || fetchDataS.processFetch.isDone == 'error') return;
    isLoadingImg.value = true;
    const res = await fetchDataS.fetchAuth();
    if(res ==  undefined || res.status == 'error'){
        return;
    }
    local.fetchedUserAuth = res.data;
}, { immediate:true });
watch(() => local.fetchedUserAuth, () => {
    if(!inputProfile.isUpdated && local?.fetchedUserAuth !== undefined && local.fetchedUserAuth !== null && typeof local.fetchedUserAuth === 'object' && !Array.isArray(local.fetchedUserAuth) && Object.keys(local.fetchedUserAuth).length > 0){
        let userData = local.fetchedUserAuth;
        inputProfile.nama_lengkap = userData.nama_lengkap;
        inputProfile.jenis_kelamin = userData.jenis_kelamin;
        inputProfile.no_telpon = userData.no_telpon;
        inputProfile.email = userData.email;
        inputProfile.linkImgProfile = userData.foto;
    }
}, { immediate:true });
const formatNoProfile = (event: any) => {
    inputProfile.no_telpon = event.target.value.replace(/\D/g, '').slice(0, 13);
    event.target.value = inputProfile.no_telpon;
};
const cosValidationProfileObject = {
    nama_lengkap: inpNamaProfile,
    jenis_kelamin: inpJenisKelamin,
    no_telpon: inpNoTelpon,
    email: inpEmail,
    foto: fileInputProfile,
};
const cosValidationProfileArray = Object.keys(cosValidationProfileObject) as Array<keyof typeof cosValidationProfileObject>;
type cosValidationProfileType = typeof cosValidationProfileArray[number];
const cosValidationPassObject = {
    pass_lama: inpPassLama,
    pass_baru: inpPassBaru,
    pass_baru_ulangi: inpPassBaruUlangi,
};
const cosValidationPassArray = Object.keys(cosValidationPassObject) as Array<keyof typeof cosValidationPassObject>;
type cosValidationPassType = typeof cosValidationPassArray[number];
const getElement = (key: keyof typeof cosValidationProfileObject | keyof typeof cosValidationPassObject, objectName: 'profile' | 'password') => {
    const obj = objectName === 'profile' ? cosValidationProfileObject : cosValidationPassObject;
    return obj[key as keyof typeof obj]?.value?.$el;
};
const showPass = (cond: string) => {
    if(cond === 'lama'){
        if(inputResetPass.isPasswordLamaShow){
            getElement('pass_lama', 'password').type = 'password';
            inputResetPass.isPasswordLamaShow = false;
        }else{
            getElement('pass_lama', 'password').type = 'text';
            inputResetPass.isPasswordLamaShow = true;
        }
    }else if(cond === 'baru'){
        if(inputResetPass.isPasswordBaruShow){
            getElement('pass_baru', 'password').type = 'password';
            inputResetPass.isPasswordBaruShow = false;
        }else{
            getElement('pass_baru', 'password').type = 'text';
            inputResetPass.isPasswordBaruShow = true;
        }
    }else if(cond === 'ulangi'){
        if(inputResetPass.isPasswordBaruUlangiShow){
            getElement('pass_baru_ulangi', 'password').type = 'password';
            inputResetPass.isPasswordBaruUlangiShow = false;
        }else{
            getElement('pass_baru_ulangi', 'password').type = 'text';
            inputResetPass.isPasswordBaruUlangiShow = true;
        }
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
    inputProfile.foto = file;
    inputProfile.linkImgProfile = URL.createObjectURL(file);
    inputProfile.isErrorFoto = false;
};
const formProfileSchema = z.object({
    nama_lengkap: z.string().min(1, 'Nama lengkap harus diisi!').max(50, 'Nama Lengkap maksimal 50 karakter'),
    jenis_kelamin: z.enum(['laki-laki', 'perempuan'], { message: 'Jenis kelamin harus dipilih!' }),
    no_telpon: z.string().min(10, 'Nomor telepon minimal 10 digit!').max(15, 'Nomor telepon maksimal 15 digit!').regex(/^\d+$/, 'Nomor telepon harus berupa angka!'),
    email: z.string().min(1, 'Email Harus diisi!').max(45, 'Email maksimal 45 karakter').email('Masukkan email dengan benar!'),
    foto: z.union([z.instanceof(File), z.null()]).optional().refine(file => !file || file.size <= 2 * 1024 * 1024, { message: 'Foto maksimal 2 MB !' })
});
const formPassSchema = z.object({
    pass_lama: z.string().min(1, 'Password Harus diisi!'),
    pass_baru: z.string().min(1, 'Password Harus diisi!')
        .min(8, 'Password minimal 8 karakter !')
        .regex(/[A-Z]/, 'Password minimal ada 1 huruf kapital !')
        .regex(/[a-z]/, 'Password minimal ada 1 huruf kecil !')
        .regex(/\d/, 'Password minimal ada 1 angka!')
        .regex(/[!@#$%^&*]/, 'Password minimal ada 1 karakter unik!'),
    pass_baru_ulangi: z.string().min(1, 'Ulangi Password Harus diisi!')
        .min(8, 'Password konfirmasi minimal 8 karakter!')
        .regex(/[A-Z]/, 'Password konfirmasi minimal ada 1 huruf kapital!')
        .regex(/[a-z]/, 'Password konfirmasi minimal ada 1 huruf kecil!')
        .regex(/\d/, 'Password konfirmasi minimal ada 1 angka!')
        .regex(/[!@#$%^&*]/, 'Password konfirmasi minimal ada 1 karakter unik!')
});
const checkValidator = (section: 'profile' | 'password') => {
    const referenceVar = section === 'profile' ? cosValidationProfileArray : cosValidationPassArray;
    const validSchema = section == 'profile' ? formProfileSchema.safeParse(inputProfile) : formPassSchema.safeParse(inputResetPass);
    if(validSchema.success){
        return { status: 'success' };
    }
    let errMessage = '', divItem = '', dValid = validSchema.error.format();
    // errorRef.value = dValid;
    Object.keys(dValid).filter((key) => (referenceVar as readonly string[]).includes(key)).forEach((key: string) => {
        if(errMessage == ''){
            const fieldValue = dValid[key as keyof typeof dValid];
            if (typeof fieldValue === 'object' && fieldValue !== null && '_errors' in fieldValue) {
                const fieldErrors = fieldValue._errors as string[];
                if (fieldErrors && fieldErrors.length > 0) {
                    divItem = key;
                    errMessage = fieldErrors[0];
                }
            }
        }
    });
    return { status: 'error', message: errMessage, item: divItem };
}
const inpChange = (div: cosValidationProfileType | cosValidationPassType, isError = false, isNeedValidator = false): void => {
    const objectName = cosValidationProfileArray.includes(div as cosValidationProfileType) ? 'profile' : 'password';
    if(isNeedValidator){
        const isValidForm = checkValidator(objectName);
        if(isValidForm.status == 'error') return inpChange(isValidForm.item as cosValidationProfileType | cosValidationPassType, true);
    }
    const itemE = getElement(div, objectName);
    if(!itemE) return console.warn(`Element for ${div} not found.`);
    if(isError){
        itemE.classList.remove('tw-border-palText', 'hover:tw-border-palText', 'focus:tw-border-palText', 'focus:tw-outline-palText');
        itemE.classList.add('tw-border-popup_error', 'hover:tw-border-popup_error', 'focus:tw-border-popup_error', 'focus:tw-outline-popup_error');
    }else{
        itemE.classList.remove('tw-border-popup_error', 'hover:tw-border-popup_error', 'focus:tw-border-popup_error', 'focus:tw-outline-popup_error');
        itemE.classList.add('tw-border-palText', 'hover:tw-border-palText', 'focus:tw-border-palText', 'focus:tw-outline-palText');
    }
};
const updateProfileForm = async() => {
    if(local.isRequestInProgress) return;
    let errMessage = '';
    if(inputProfile.nama_lengkap === local.fetchedUserAuth.nama_lengkap && inputProfile.email === local.fetchedUserAuth.email && inputProfile.jenis_kelamin === local.fetchedUserAuth.jenis_kelamin && inputProfile.no_telpon === local.fetchedUserAuth.no_telpon && inputProfile.foto === null) {
        if(!errMessage) errMessage = 'Data belum diubah !';
    }
    if(errMessage != ''){
        return toast.add({ severity: 'error', summary: 'Gagal Update Profile', detail: errMessage, group: 'br', life: 3000 })
    }
    const isValidForm = checkValidator('profile');
    if(isValidForm.status == 'error'){
        inpChange(isValidForm.item as cosValidationProfileType, true);
        return toast.add({ severity: 'error', summary: 'Gagal Update Profile', detail: isValidForm.message, group: 'br', life: 3000 })
    }
    local.isRequestInProgress = true;
    eventBus.emit('showLoading');
    const formData = {
        nama_lengkap: inputProfile.nama_lengkap,
        jenis_kelamin: inputProfile.jenis_kelamin,
        no_telpon: inputProfile.no_telpon,
        email_new: inputProfile.email !== local.fetchedUserAuth.email ? inputProfile.email : '',
        foto: inputProfile.foto,
    };
    let res = await updateProfile(formData);
    if(res.status === 'success'){
        eventBus.emit('closeLoading');
        const fotoCache = local.fetchedUserAuth.foto;
        fetchDataS.cacheAuth.nama_lengkap = inputProfile.nama_lengkap;
        fetchDataS.cacheAuth.jenis_kelamin = inputProfile.jenis_kelamin;
        fetchDataS.cacheAuth.no_telpon = inputProfile.no_telpon;
        fetchDataS.cacheAuth.email = inputProfile.email;
        fetchDataS.cacheAuth.foto = '';
        setTimeout(() => {
            fetchDataS.cacheAuth.foto = fotoCache;
            toast.add({ severity: 'success', summary: 'Berhasil Update Profile', detail: res.message, group: 'br', life: 3000 });
            setTimeout(function(){
                local.isRequestInProgress = false;
            }, 3000);
        }, 5);
    }else if(res.status === 'error'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        toast.add({ severity: 'error', summary: 'Gagal Update Profile', detail: res.message, group: 'br', life: 3000 });
        cosValidationProfileArray.forEach((field) => {
            if (res.message.toLowerCase().includes(field)) {
                inpChange(field, true);
            }
        });
    }
};
const updatePasswordForm = async() => {
    if(local.isRequestInProgress) return;
    const isValidForm = checkValidator('password');
    if(isValidForm.status == 'error'){
        inpChange(isValidForm.item as cosValidationPassType, true);
        return toast.add({ severity: 'error', summary: 'Gagal Update Password', detail: isValidForm.message, group: 'br', life: 3000 })
    }
    let errMessage = '';
    if(inputResetPass.pass_baru !== inputResetPass.pass_baru_ulangi) {
        inpChange('pass_baru_ulangi', true);
        if(!errMessage) errMessage = 'Password baru harus sama dengan Ulangi Password Baru !';
    }
    if(errMessage != ''){
        return toast.add({ severity: 'error', summary: 'Gagal Update Password', detail: errMessage, group: 'br', life: 3000 })
    }
    local.isRequestInProgress = true;
    eventBus.emit('showLoading');
    const res = await updatePassword({password_old: inputResetPass.pass_lama, password: inputResetPass.pass_baru, password_ulangi: inputResetPass.pass_baru_ulangi});
    if(res.status === 'success'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        inputResetPass.pass_lama = '';
        inputResetPass.pass_baru = '';
        inputResetPass.pass_baru_ulangi = '';
        toast.add({ severity: 'success', summary: 'Berhasil Update Password', detail: res.message, group: 'br', life: 3000 });
    }else if(res.status === 'error'){
        local.isRequestInProgress = false;
        eventBus.emit('closeLoading');
        toast.add({ severity: 'error', summary: 'Gagal Update Password', detail: res.message, group: 'br', life: 3000 });
        cosValidationPassArray.forEach((field) => {
            if (res.message.toLowerCase().includes(field)) {
                inpChange(field, true);
            }
        });
    }
};
</script>