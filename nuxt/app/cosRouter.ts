export default [
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
];