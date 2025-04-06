// src/stores/global.js
import { defineStore } from "pinia";

export const useGlobalStore = defineStore("global", {
    state: () => ({
        dotActive: null,
    }),
    actions: {
        setDot(dotActive) {
            this.dotActive = dotActive;
        },
    },
});
