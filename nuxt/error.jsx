import { useDarkModeStore } from './store/darkMode';
import notFoundImage from '~/assets/images/pages/notfound.png';
export default defineComponent({
    setup(props) {
        const darkModeStore = useDarkModeStore();
        darkModeStore.initializeDarkMode();
        const error = useError();
        if(error.value?.statusCode){
            return () => {
                return (
                    <div class="h-screen bg-white dark:bg-dark_bg">
                        <div class="w-3/4 h-3/4 relative left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2 flex items-center justify-around rounded-xl">
                            <img src={notFoundImage} class="object-cover rounded-lg w-120 bg-blue-00"/>
                            <div class="flex flex-col items-center gap-20 bg-slate-r00">
                                <div class="flex flex-col items-center gap-2">
                                    <h1 class="text-9xl font-semibold text-primary_text dark:text-primary_dark_text">Error {error.value?.statusCode}</h1>
                                    <p class="text-6xl font-semibold text-primary_text dark:text-primary_dark_text">Page Not Found</p>
                                </div>
                                <NuxtLink to="/" class="w-70 h-15 bg-second dark:bg-primary_dark_text rounded-xl cursor-pointer text-white dark:text-dark_bg text-5xl flex justify-center items-center" style="border-radius: 20px;">Back </NuxtLink>
                            </div>
                        </div>
                    </div>
                )
            }
        }
    }
})