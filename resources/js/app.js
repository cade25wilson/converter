import "./bootstrap";
import router from "./router/router";
import { createApp } from "vue";
import "bootstrap";
import 'bootstrap/dist/js/bootstrap.bundle.min.js';


import App from "./App.vue";

createApp(App).use(router).mount("#app");