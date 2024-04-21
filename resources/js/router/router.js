import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/image",
        component: () => import("../Pages/IndexRoute.vue"),
    },
    {
        path: "/test",
        component: () => import("../Pages/TestRoute.vue"),
    },
    {
        path: "/audios",
        component: () => import("../Pages/AudioRoute.vue"),
    },
];

export default createRouter({
    history: createWebHistory(),
    routes,
});