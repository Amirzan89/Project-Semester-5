export default [
    {
        name:'DeviceTambah',
        path:'/device/tambah',
        file:'~/pages/device/tambah.vue',
    },
    {
        name:'FirmwareTambah',
        path:'/firmware/tambah',
        file:'~/pages/firmware/tambah.vue',
    },
    {
        name:'FirmwareDownload',
        path:'/firmware/download',
        file:'~/pages/firmware/download/index.vue',
    },
    {
        name:'FirmwareDownloadDetail',
        path:'/firmware/download/:link',
        file:'~/pages/firmware/download/[link].vue',
    },
    {
        name: 'NotFound',
        path: '/:catchAll(.*)',
        component: '~/pages/NotFound.vue',
    }
];