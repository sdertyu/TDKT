// src/stores/global.js
import { defineStore } from "pinia";

export const useGlobalStore = defineStore("global", {
    state: () => ({
        dotActive: null,
        loading: false,
        pendingRequests: 0,
    }),
    actions: {
        setDot(dotActive) {
            this.dotActive = dotActive;
        },
        startLoading() {
            this.pendingRequests++;
            this.loading = true;
        },
        stopLoading() {
            this.pendingRequests--;
            if (this.pendingRequests <= 0) {
                this.pendingRequests = 0;
                this.loading = false;
            }
        },
    },
});
