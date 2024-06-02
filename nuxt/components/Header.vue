<template>
    <div class="w-full h-22 sticky top-0 flex items-center justify-center scroll-pt-20 bg-primary1 text-white" style="box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.27); z-index:999">
        <div class="w-11/12 flex items-center justify-between">
            <div class="flex items-center gap-30">
                <h1 class="text-6xl font-bold">{{ publicConfig.appName }}</h1>
                <ul class="flex flex-row gap-4 font-semibold text-2xl">
                    <li>
                        <a href="#about">About</a>
                    </li>
                    <li>
                        <a href="#service">Service</a>
                    </li>
                </ul>
            </div>
            <div>
                <nuxt-link to="/login" class="flex items-center gap-3">
                    <FontAwesomeIcon icon="fa-solid fa-right-to-bracket" class="text-2xl" />
                    <span class="text-2xl font-semibold">Masuk</span>
                </nuxt-link>
            </div>
        </div>
    </div>
</template>
<script setup>
const publicConfig = useRuntimeConfig().public;
</script>