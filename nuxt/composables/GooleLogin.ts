import Cookies from 'js-cookie';
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
                    let redir = '';
                    let coDat = Cookies.get('__INITIAL_COSTUM_STATE__');
                    if(coDat != null && coDat != '' && coDat != undefined){
                        let deC = atob(coDat as string);
                        if(deC == null || deC == ''){
                            console.log('error decoded');
                            return;
                        }
                        let dataC = JSON.parse(deC);
                        redir = dataC.url ?? '/dashboard'
                    }else{
                        redir = res.data;
                    }
                    setTimeout(() => {
                        navigateTo(redir);
                    }, 1500);
                }).catch((error: any) => {
                    console.log('Error logging in:', error)
                });
            },
            prompt_parent_id: "root",
        });
        google.accounts.id.prompt();
    }
    return { initializeGoogleOneTap }
}