import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

// export default defineConfig({
//     plugins: [
//         vue(),
//         laravel({
//             input: ["resources/css/app.css", "resources/js/app.js"],
//             refresh: true,
//         }),
//     ],
//     resolve: {
//         alias: {
//             vue: "vue/dist/vue.esm-bundler.js",
//         },
//     },
// });

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ["resources/js/app.js", "resources/css/app.css"],
            refresh: true,
            server: 'http://localhost:5173',
        }),
    ],
    resolve: {
        alias: {
            vue: "vue/dist/vue.esm-bundler.js",
        },
    },
    build: {
        rollupOptions: {
            output: {
                entryFileNames: `[name].builds.js`,
                chunkFileNames: `[name].build.js`,
                assetFileNames: `[name].build.[ext]`
            }
        }
    }
});