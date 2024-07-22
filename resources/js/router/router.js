import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/",
        component: () => import("../Pages/IndexRoute.vue"),
        meta: { title: "Image Converter" },
    },
    {
        path: "/archives",
        component: () => import("../Pages/ArchiveRoute.vue"),
        meta: { title: "Archive Converter" },
    },
    {
        path: "/audios",
        component: () => import("../Pages/AudioRoute.vue"),
        meta: { title: "Audio Converter" },
    },
    {
        path: "/contact",
        component: () => import("../Pages/ContactRoute.vue"),
        meta: { title: "Contact Converter" },
    },
    {
        path: "/ebooks",
        component: () => import("../Pages/EbookRoute.vue"),
        meta: { title: "Ebook Converter" },
    },
    {
        path: "/images",
        component: () => import("../Pages/IndexRoute.vue"),
        meta: { title: "Image Converter" },
    },
    {
        path: "/signin",
        component: () => import("../Pages/SigninRoute.vue"),
        meta: { title: "Sign in to converter" },
    },
    {
        path: "/signup",
        component: () => import("../Pages/SignupRoute.vue"),
        meta: { title: "Sign up to converter" },
    },
    {
        path: "/spreadsheets",
        component: () => import("../Pages/SpreadsheetRoute.vue"),
        meta: { title: "Spreadsheet Converter" },
    },
    {
        path: "/previousconversions",
        component: () => import("../Pages/PastRoute.vue"),
        meta: { title: "Previous Conversions" },
    },
    {
        path: "/privacy",
        component: () => import("../Pages/PrivacyRoute.vue"),
        meta: { title: "Privacy Policy" },
    },
    {
        path: "/videos",
        component: () => import("../Pages/VideoRoute.vue"),
        meta: { title: "Video Converter" },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    document.title = `${to.meta.title} | File Converter`;
    next();
});

export default router;