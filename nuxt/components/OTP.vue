<template>
    <form class="relative flex flex-col items-center ml-5 mt-10">
        <div  class="text-4xl font-semibold">
            <template v-if="props.data.condition == 'email'">
                Verifikasi Email
            </template>
            <template v-else-if="props.data.condition == 'password'">
                Lupa Password
            </template>
        </div>
        <div class="flex flex-row gap-2 mt-10" style="caret-color: transparent;">
            <input v-for="(input, index) in inpOtp" :key="index" type="text" class="w-10 h-10 border-gray-400 focus:border-black border-2 rounded-xl text-center text-2xl font-medium" ref="inpOtpRefs" v-model="inpOtp[index]" @input="handleInput(index, $event)" @keyup="handleKeyUp(index, $event)" @keypress="inpChange">
        </div>
        <button class="bg-green-700 mt-10 w-50 h-10 rounded-2xl font-semibold text-white text-xl" @click.prevent="otpForm">
            <template v-if="props.data.condition == 'email'">
                Verifikasi Email
            </template>
            <template v-else-if="props.data.condition == 'password'">
                Konfirmasi
            </template>    
        </button>
        <span class="text-lg mt-5">Tidak Menerima Kode OTP ? <button type="button" @click="ResendOtp" class="hover:text-red-500 font-medium">kirim ulang</button></span>
    </form>
</template>
<style scoped lang="scss">
input{
    outline: none;
}
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
import { ref, defineEmits } from "vue";
import { eventBus } from '~/app/eventBus';
import { ReSendOtp, VerifyOtp } from '~/composables/api/auth';
import type { Props } from "nuxt/dist/head/runtime/types";
const emit = defineEmits(['change-popup', 'green-popup', 'countdown']);
const props: Props = defineProps({
    data: Object,
    timer: Object,
});
const local = reactive({
    isReSend: false,
    isSend: false,
})
const inpOtpRefs: Ref = ref(null);
const inpOtp: Ref = ref(Array(6).fill(''));
const inpChange = () => {
    // emit('change-popup');
};
const handleInput = (index: number, event: any) => {
    let val = event.target.value;
    if (isNaN(val[val.length - 1])) {
        inpOtp.value[index] = val.slice(0, -1);
        return;
    }
    if (val.length > 1) {
        inpOtp.value[index] = val[val.length - 1];
    }
    const nextIndex = index + 1;
    if(nextIndex < inpOtp.value.length && inpOtp.value[nextIndex] == ''){
        inpOtpRefs.value[nextIndex].focus();
    }
};
const handleKeyUp = (index: number, event: any) => {
    const key = event.key.toLowerCase();
    if (key == "backspace" || key == "delete") {
        const prevIndex = index - 1;
        if (prevIndex >= 0 && inpOtp.value[prevIndex] != '') {
            inpOtpRefs.value[prevIndex].focus();
            inpOtp.value[prevIndex] = '';
        }
        return;
    }
    if (key === "arrowleft" || key === "arrowright") {
        const direction = key === "arrowleft" ? "previousElementSibling" : "nextElementSibling";
        const nextInput = inpOtpRefs.value[index][direction];
        if (nextInput) {
            nextInput.focus();
        }
    }
};
const showTimerPopup = () => {
    let second = 0;
    const intervalId = setInterval(() => {
        eventBus.emit('showCountDown', `sisa waktu ${props.timer.timerMenit} menit ${props.timer.timerDetik} detik untuk kirim kembali`);
        second++;
        if (second >= 3) {
            clearInterval(intervalId);
            eventBus.emit('closePopup','red');
        }
    }, 1000);
};
const ResendOtp = async () => {
    if(local.isReSend) return;
    if (!props.data.email || props.data.email.trim() == '') return;
    if(props.timer.timer){
        return showTimerPopup();
    }
    eventBus.emit('showLoading');
    let link = props.data.condition === 'email' ? '/verify/otp/resend/email' : '/verify/otp/resend/password';
    local.isReSend = true;
    let sendOTP = await ReSendOtp({email: props.data.email, link: link});
    if(sendOTP.status === 'success'){
        local.isReSend = false;
        eventBus.emit('closeLoading');
        emit('countdown',new Date(sendOTP.data.waktu).getTime());
        eventBus.emit('showGreenPopup', sendOTP.message);
    }else if(sendOTP.status === 'error'){
        local.isReSend = false;
        eventBus.emit('closeLoading');
        eventBus.emit('showRedPopup', sendOTP.message);
    }
};
const otpForm = async (event: Event) => {
    event.preventDefault();
    if(local.isSend) return;
    inpOtp.value.forEach(function(inpotp: string){
        if(inpotp === '' || inpotp === null){
            eventBus.emit('showRedPopup', 'kode OTP harus diisi');
            return;
        }
    });
    let link = props.data.condition === 'email' ? '/verify/otp/email' : '/verify/otp/password';
    eventBus.emit('showLoading');
    local.isSend = true;
    let verifyOTP = await VerifyOtp({email: props.data.email, otp: inpOtp.value.join(''), link: link});
    if(verifyOTP.status === 'success'){
        local.isSend = false;
        eventBus.emit('closeLoading');
        eventBus.emit('showGreenPopup', 'success verifikasi otp');
        emit('green-popup', { otp: inpOtp.value.join(''), cond: props.data.condition == 'email' ? 'verifyEmail' : 'refPassword' });
    }else if(verifyOTP.status === 'error'){
        local.isSend = false;
        eventBus.emit('closeLoading');
        eventBus.emit('showRedPopup', verifyOTP.message);
    }
};
</script>