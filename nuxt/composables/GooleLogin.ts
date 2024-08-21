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
                    if(res.status == 'success'){
                        setTimeout(() => {
                            navigateTo('/dashboard');
                        }, 1500);
                    }
                }).catch((error: any) => {
                    console.error('Error logging in:', error)
                });
            },
            prompt_parent_id: "root",
        });
        google.accounts.id.prompt((notification: any) => {
            if (notification.isNotDisplayed()) {
            console.log(notification.getNotDisplayedReason());
            } else if (notification.isSkippedMoment()) {
            console.log(notification.getSkippedReason());
            } else if (notification.isDismissedMoment()) {
            console.log(notification.getDismissedReason());
            } else {
            }
        });
    }
    return { initializeGoogleOneTap }
}