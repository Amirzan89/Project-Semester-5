import { CreateLoginGoogleTap } from './api/auth';
const publicConfig = useRuntimeConfig().public;
export default function useGoogleLoginTap(){
    const initializeGoogleOneTap = () => {
        const google = (window as any).google;
        google.accounts.id.initialize({
            client_id: publicConfig.GoogleId,
            cancel_on_tap_outside: false,
            ux_mode: 'popup',
            callback: (response: any) => {
                CreateLoginGoogleTap(response.credential).then((res: any) => {
                    if(res.status == 'error'){
                        return console.error('Error logging in:', res.status)
                    }
                    setTimeout(() => {
                        navigateTo(res.data ? res.data : '/dashboard');
                    }, 1500);
                }).catch((error: any) => {
                    console.error('Error logging in:', error)
                });
            },
            prompt_parent_id: "root",
        });
        google.accounts.id.prompt();
    }
    return { initializeGoogleOneTap }
}