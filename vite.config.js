import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
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
                'resources/js/pages/plan-form.js',
                'resources/js/pages/plans-index.js',
                'resources/js/dashboard/main.js',
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
