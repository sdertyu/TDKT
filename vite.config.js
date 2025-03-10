import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    optimizeDeps: {
        include: ["jquery", "admin-lte"],
    },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        host: "localhost", // Cho phép truy cập từ mọi địa chỉ IP
        hmr: {
            host: "localhost",
        },
        cors: true,
    },
    resolve: {
        alias: {
            "@": "/resources/js",
            $: "jquery",
        },
    },
});
