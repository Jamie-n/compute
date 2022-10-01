import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        watch: {
            usePolling: true
        }
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css'],
            refresh: true,
        }),
    ],

});
