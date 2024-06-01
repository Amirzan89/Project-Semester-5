<template>
    <!-- <div class="w-full h-15 flex items-center fixed top-0 left-0 bg-white" style="box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px; z-index:20">
        <HeaderServer></HeaderServer>
    </div>
    <aside class="h-full w-40 fixed top-0 left-0 bg-white flex-1" style="box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0,  0, 0, 0.08) 0px 0px 0px 1px; z-index:30">
        <NavbarComponent></NavbarComponent>
    </aside>
    <main class="flex flex-col items-center flex-grow ml-40 mt-15" style="z-index: 10;">
        <slot/>
    </main>
    <footer class="flex-grow h-10 ml-40 mt-2 flex justify-center items-center gap-1">
        <FooterComponent></FooterComponent>
    </footer>
    <Loading></Loading>
    <Popup></Popup> -->
    <main class="flex flex-col items-center flex-grow ml-40 mt-15" style="z-index: 10;">
        <slot/>
    </main>
</template>
<script setup>
import HeaderServer from '~/components/Header.server.vue';
import NavbarComponent from '~/components/Navbar.server.vue';
import FooterComponent from '~/components/Footer.server.vue';
import Loading from '~/components/Loading.vue';
import Popup from '~/components/Popup.vue';
</script>