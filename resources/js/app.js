// main.js
import { createApp } from "vue";
import App from "./App.vue";
import router from "./src/router";
import { createPinia } from "pinia";
import PrimeVue from "primevue/config";
import Aura from "@primeuix/themes/aura";

import $ from "jquery";
window.$ = window.jQuery = $;

const app = createApp(App);

router.beforeEach((to, from, next) => {
    const defaultTitle = "Hệ thống thi đua khen thưởng";

    if (to.meta && to.meta.title) {
        document.title = to.meta.title;
    } else {
        document.title = defaultTitle;
    }

    next();
});

app.use(router);
app.use(PrimeVue, {
    theme: {
        preset: Aura,
    },
});
app.use(createPinia());
app.mount("#app");

import "./bootstrap";
import "@fortawesome/fontawesome-free/js/all.js";
