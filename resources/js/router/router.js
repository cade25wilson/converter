import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/",
        component: () => import("../Pages/IndexRoute.vue"),
    },
    {
        path: "/images",
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
    },
    {
        path: "/previousconversions",
        component: () => import("../Pages/PastRoute.vue"),
    },
    {
        path: "/privacy",
        component: () => import("../Pages/PrivacyRoute.vue"),
    }
];

export default createRouter({
    history: createWebHistory(),
    routes,
});