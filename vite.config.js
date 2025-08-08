import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        // Enable minification
        minify: 'terser',
        // Enable tree shaking
        rollupOptions: {
            output: {
                manualChunks: {
                    // Split vendor libraries into separate chunks
                    'alpine': ['alpinejs'],
                    'flowbite': ['flowbite'],
                    'sweetalert': ['sweetalert2'],
                }
            }
        },
        // Optimize chunk size
        chunkSizeWarningLimit: 1000,
        // Enable source maps for debugging
        sourcemap: false,
    },
    // Optimize dependencies
    optimizeDeps: {
        include: ['alpinejs', 'flowbite', 'sweetalert2']
    }
});
