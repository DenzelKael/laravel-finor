import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/adminlte.css',
                'resources/js/adminlte.js',
                'resources/js/clients/create.js',
                'resources/js/clients/edit.js',
                'resources/js/clients/index.js',
                'resources/js/payments/create.js',
                'resources/js/payments/index.js',
            ],
            refresh: true,
          
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
