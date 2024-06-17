import { defineStore } from "pinia";
export const useDarkModeStore = defineStore('darkMode', {
    state: () => {
        darkMode: false
    },
    actions: {
        initializeDarkMode() {
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