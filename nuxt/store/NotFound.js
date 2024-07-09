import { defineStore } from "pinia";
export const useNotFoundStore = defineStore('notfound', {
    state: () => ({
        isNotFound: false,
        messageNotFound: '',
        linkBack: '/',
    }),
    actions: {
        setIsNotFound(value, linkBack, message) {
            this.isNotFound = value;
            this.linkBack = linkBack;
            this.messageNotFound = message;
        },
        async resetState() {
            this.isNotFound = false;
            this.messageNotFound = '';
            this.linkBack = '/';
        },
    },
});