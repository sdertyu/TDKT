// main.js
import { createApp } from "vue";
import App from "./App.vue";
import router from "./src/router";

import $ from "jquery";
window.$ = window.jQuery = $;

const app = createApp(App);
app.use(router);
app.mount("#app");

import "./bootstrap";
import "bootstrap/dist/js/bootstrap.min.js";
import "@fortawesome/fontawesome-free/js/all.js";
