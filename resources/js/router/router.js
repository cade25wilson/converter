import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/image",
        component: () => import("../Pages/IndexRoute.vue"),
    },
    {
        path: "/audios",
        component: () => import("../Pages/AudioRoute.vue"),
    },
    {
        path: "/videos",
        component: () => import("../Pages/VideoRoute.vue"),
    },
    {
        path: "/spreadsheets",
        component: () => import("../Pages/SpreadsheetRoute.vue"),
    }
];

export default createRouter({
    history: createWebHistory(),
    routes,
});