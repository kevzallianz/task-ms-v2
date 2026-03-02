import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/projects/index.js",
                "resources/js/campaigns/index.js",
                'resources/js/superadmin/index.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
        cors: {
            credentials: true,
            origin: "*",
        },
        host: "161.31.11.84",
        hmr: {
            host: "161.31.11.84",
        },
    },
});
