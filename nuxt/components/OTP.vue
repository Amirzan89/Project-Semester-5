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
            <input v-for="(input, index) in inpOtp" :key="index" type="text" class="w-10 h-10 border-gray-400 focus:border-black border-2 rounded-xl text-center text-2xl font-medium" :ref="'input' + index" v-model="inpOtp[index]" @input="handleInput(index, $event)" @keyup="handleKeyUp(index, $event)" @keypress="inpChange">
        </div>
        <button class="bg-green-700 mt-10 w-50 h-10 rounded-2xl font-semibold text-white text-xl" @click.prevent="otpForm"> 
            <template v-if="props.data.condition == 'email'">
                Verifikasi Email
            </template>
            <template v-else-if="props.data.condition == 'password'">
                Konfirmasi
            </template>    
        </button>
        <span class="text-lg mt-5">Tidak Menerima Kode OTP ? <button type="button" @click="sendOtp" class="hover:text-red-500 font-medium">kirim ulang</button></span>
    </form>
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
<script setup>
import { ref, defineEmits } from "vue";
import { eventBus } from '../app/eventBus';
import { SendOtp } from "../../composition/Auth";
// import { SendOtp, VerifyOtp } from '../composition/Auth';
const emit = defineEmits(['change-popup', 'red-popup', 'green-popup', 'countdown']);
const props = defineProps({
    data: Object,
    timer: Object,
})
const inpOtp = ref(Array(6).fill(''));
const errMessage = ref('');
const div = ref('');
const inpChange = () => {
    emit('change-popup');
};
const handleInput = (index, event) => {
    let val = event.target.value;
    if (isNaN(val[val.length - 1])) {
        inpOtp.value[index] = val.slice(0, -1);
        return;
    }
    if (val.length > 1) {
        inpOtp.value[index] = val[val.length - 1];
        return;
    }
    if (val !== "") {
        const nextIndex = index + 1;
        if (nextIndex < inpOtp.value.length) {
            this.$refs['input' + nextIndex][0].focus();
        }
    }
};
const handleKeyUp = (index, event) => {
    const key = event.key.toLowerCase();
    if (key == "backspace" || key == "delete") {
        inpOtp.value[index] = "";
        const prevIndex = index - 1;
        if (prevIndex >= 0) {
            this.$refs['input' + prevIndex][0].focus();
        }
        return;
    }
    if (key === "arrowleft" || key === "arrowright") {
        const direction = key === "arrowleft" ? "previousElementSibling" : "nextElementSibling";
        const nextInput = this.$refs['input' + index][0][direction];
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
const sendOtp = async () => {
    if (props.data.email && props.data.email.trim() !== '') {
        emit('red-popup', 'Email harus diisi');
        return;
    }
    if(props.timer.timer){
        showTimerPopup();
        return;
    }
    eventBus.emit('showLoading');
    let link = props.data.condition === 'email' ? '/verify/create/email' : '/verify/create/password';
    let sendOTP = await SendOtp({email: props.data.email, link: link});
    if(sendOTP.status === 'success'){
        eventBus.emit('closeLoading');
        eventBus.emit('showGreenPopup', sendOTP.message);
        emit('countdown',new Date(sendOTP.data.waktu).getTime());
        emit('green-popup', 'success verifikasi otp');
    }else if(sendOTP.status === 'error'){
        eventBus.emit('closeLoading');
        popup.value.classList.remove('invisible');
        errMessage.value = sendOTP.message;
    }
};
const otpForm = async (event) => {
    let hasError = false;
    event.preventDefault();
    inpOtp.value.forEach(function(inpotp){
        if(inpotp === '' || inpotp === null){
            hasError = true;
            emit('red-popup', 'kode OTP harus diisi');
            return;
        }
    });
    if(hasError){
        return;
    }
    let link = props.data.condition === 'email' ? '/verify/otp/email' : '/verify/otp/password';
    eventBus.emit('showLoading');
    let verifyOTP = await VerifyOtp({email: props.data.email, otp: inpOtp.value.join(''), link: link});
    if(verifyOTP.status === 'success'){
        eventBus.emit('closeLoading');
        emit('green-popup', 'success verifikasi otp', inpOtp.value.join(''));
        return;
    }else if(verifyOTP.status === 'error'){
        eventBus.emit('closeLoading');
        emit('red-popup', err.response.data.message);
        return
    }
};
</script>