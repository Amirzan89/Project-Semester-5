const delayConst = 20;
import { defineStore } from "pinia";
export const useDarkModeStore = defineStore('darkMode', {
    state: () => {
        return {
            transitionTime: 0 as number,
            preDarkMode: false as boolean,
            darkMode: false as boolean,
        };
    },
    actions: {
        initializeDarkMode(timing: number) {
            this.transitionTime = timing;
            const storedPreference = localStorage.getItem('dark-mode');
            if (storedPreference !== null) {
                this.darkMode = storedPreference === 'enabled';
            } else {
                this.darkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            }
            this.applyDarkMode();
            this.watchSystemPreference();
        },
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            this.applyDarkMode();
            localStorage.setItem('dark-mode', this.darkMode ? 'enabled' : 'disabled');
        },
        applyDarkMode() {
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            this.preDarkMode = true;
            setTimeout(() => {
                this.preDarkMode = false;
            }, (this.transitionTime * 1000) + delayConst);
        },
        watchSystemPreference() {
            if (window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (localStorage.getItem('dark-mode') === null) {
                    this.darkMode = e.matches;
                    this.applyDarkMode();
                }
                });
            }
        }
    },
});