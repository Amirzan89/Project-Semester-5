import { defineStore } from 'pinia';
import { CheckAuth } from '~/composables/api/auth';
export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        loading: false,
    }),
    actions: {
        async fetchUser() {
            this.loading = true;
            const res = await CheckAuth('/');
            this.loading = false;
            if(res.status == 'success'){
                this.user = res.data;
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