import {fileURLToPath, URL} from 'url'
import {defineConfig} from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from 'tailwindcss'
import laravel from "vite-plugin-laravel";
import autoprefixer = require("autoprefixer");

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [
        vue(),
        laravel({
            postcss: [
                tailwindcss(),
                autoprefixer()
            ],
        }),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./Modules/Vpanel/Resources/scripts/', import.meta.url)),
        }
    },
    server: {
        host: true,
        hmr: {
            host: 'localhost'
        }
    },
})
