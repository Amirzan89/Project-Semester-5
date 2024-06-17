import { defineStore } from "pinia";
export const useNotFoundStore = defineStore('notfound', {
    state: () => {
        isNotFound: false;
        messageNotFound: '';
        linkBack:'/';
    },
    actions: {
        setIsNotFound(value = false, linkBack = '') {
            this.isNotFound = value;
            if(this.isNotFound){
                this.linkBack = linkBack;
            }
        },
        setMessageNotFound(message = '') {
            this.messageNotFound = message;
        },
        resetState() {
            this.isNotFound = false;
            this.messageNotFound = '';
            this.linkBack = '/';
        }
    },
});