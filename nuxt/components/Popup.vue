<template>
    <div v-if="local.state === 'green'" class="w-80 h-15 fixed right-1 bottom-1 text-lg font-normal" ref="greenPopup">
        <div class="w-full h-full absolute  rounded-2xl" @click="closePopup('red',true)" style="background: #ECFFEB;border: 2px #01B701 solid;"></div>
        <div class="absolute w-8 h-8 top-1/2 -translate-y-1/2 rounded-full" style="left: 5%; background: #bcffb7;">
            <div class="absolute w-8 h-8 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 rounded-full" style="border: 2px #01B701 solid;"></div>
            <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-lg font-bold text-green-500"><img :src="publicConfig.baseURL + '/img/icon/check.svg'" alt="" class="w-full h-full"></span>
        </div>
        <span class="top-1/2 -translate-y-1/2 absolute text-2xl font-semibold cursor-pointer text-green-500" style="right: 5%;" @click="closePopup('red',true)">X</span>
        <label class="absolute top-1/2 -translate-y-1/2 text-lg font-medium leading-7 whitespace-normal break-keep text-green-500" style="left: 22%;">{{  local.message }}</label>
    </div>
    <div v-else-if="local.state === 'red'" class="w-80 h-15 fixed right-5 bottom-5 text-lg font-normal" ref="redPopup">
        <div class="w-full h-full absolute  rounded-2xl" @click="closePopup('red',true)" style="background: #FFE1E1;border: 2px #FF0000 solid;"></div>
        <div class="absolute w-8 h-8 top-1/2 -translate-y-1/2 rounded-full" style="left: 5%;">
            <div class="absolute w-8 h-8 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 rounded-full" style="border: 2px #FF0000 solid;"></div>
            <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-lg font-bold text-red-500">!</span>
        </div>
        <span class="top-1/2 -translate-y-1/2 absolute text-2xl font-semibold cursor-pointer text-red-500" style="right: 5%;" @click="closePopup('red',true)">X</span>
        <label class="absolute top-1/2 -translate-y-1/2 text-lg font-medium leading-7 whitespace-normal break-keep text-red-500" style="left: 22%;">{{  local.message }}</label>
    </div>
</template>
<style scoped>
.fade-out{
    animation: fadeOut 0.75s ease forwards;
}
@keyframes fadeOut {
    0% {
        opacity: 1;
    }
    100% {
        opacity: 0;
        display: none;
    }
}
</style>
<script setup lang="ts">
import { onMounted, reactive, ref,  } from 'vue';
import { eventBus } from '../app/eventBus';
const publicConfig = useRuntimeConfig().public;
const local = reactive({
    state:'',
    message:'',
});
const greenPopup: Ref = ref(null);
const redPopup: Ref = ref(null);
onMounted(()=> {
    eventBus.on('showGreenPopup',function(message: any){
        showGreenPopup(message);
    });
    eventBus.on('showRedPopup',function(message: any){
        showRedPopup(message);
    });
    eventBus.on('showCountDown',function(message: any){
        showCountDown(message);
    });
    eventBus.on('closePopup',function(opt: any){
        closePopup(opt);
    });
});
const showCountDown = (data: string) => {
    local.message = data;
    local.state = 'red';
};
const showRedPopup = (data: string, div = null) => {
    local.message = data;
    local.state = 'red';
    setTimeout(function(){
        closePopup('red');
    }, 3000);
};
const showGreenPopup = (data: string, div = null) => {
    local.message = data;
    local.state = 'green';
    setTimeout(function(){
        closePopup('green');
    }, 3000);
};
const closePopup = (opt: string, click = false, divInp = null) => {
    if(click){
        if (opt == 'green') {
            local.state = '';
            local.message = '';
        } else if (opt == 'red') {
            local.state = '';
            local.message = '';
        }
    }else{
        if (opt == 'green') {
            greenPopup.value.classList.add('fade-out');
            setTimeout(function(){
                greenPopup.value.classList.remove('fade-out');
                local.state = '';
                local.message = '';
            }, 750);
        } else if (opt == 'red') {
            redPopup.value.classList.add('fade-out');
            setTimeout(function(){
                redPopup.value.classList.remove('fade-out');
                local.state = '';
                local.message = '';
            }, 750);
        }
    }
    if(divInp !== null){
        if(divInp == 'login'){
            loginPage();
        }else{
            showDiv(divInp);
        }
    }
};
</script>