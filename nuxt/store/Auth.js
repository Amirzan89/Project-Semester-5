import { defineStore } from 'pinia';
import { CheckAuth } from '~/composition/Auth';
export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        loading: false,
    }),
    actions: {
        async fetchUser() {
            this.loading = true;
            const res = await CheckAuth();
            this.loading = false;
            if(res.status == 'success'){
                this.user = response.data;
            }
        },
        logout() {
            this.user = null;
        }
    },

    getters: {
        isAuthenticated: state => !!state.user,
    }
});