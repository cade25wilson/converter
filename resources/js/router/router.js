import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/",
        component: () => import("../Pages/IndexRoute.vue"),
    },
    {
        path: "/archives",
        component: () => import("../Pages/ArchiveRoute.vue"),
    },
    {
        path: "/audios",
        component: () => import("../Pages/AudioRoute.vue"),
    },
    {
        path: "/contact",
        component: () => import("../Pages/ContactRoute.vue"),
    },
    {
        path: "/ebooks",
        component: () => import("../Pages/EbookRoute.vue"),
    },
    {
        path: "/images",
        component: () => import("../Pages/IndexRoute.vue"),
    },
    {
        path: "/signin",
        component: () => import("../Pages/SigninRoute.vue"),
    },
    {
        path: "/signup",
        component: () => import("../Pages/SignupRoute.vue"),
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
    },
    {
        path: "/videos",
        component: () => import("../Pages/VideoRoute.vue"),
    },
];

export default createRouter({
    history: createWebHistory(),
    routes,
});