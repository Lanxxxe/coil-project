import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    // Put MapLibre and its CSS into a dedicated chunk
                    if (id.includes('node_modules/maplibre-gl') || id.includes('maplibre-gl/dist/maplibre-gl.css')) {
                        return 'vendor-maplibre';
                    }
                    // Optionally split other heavy vendor libs here later
                },
            },
        },
    },
});
