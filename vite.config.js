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
        // Optimize chunk size
        chunkSizeWarningLimit: 500,

        // Enable minification
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true, // Remove console.log in production
                drop_debugger: true,
            },
        },

        // CSS minification
        cssMinify: true,

        // Rollup options for better code splitting
        rollupOptions: {
            output: {
                // Split vendor chunks for better caching
                manualChunks: {
                    'alpine': ['alpinejs'],
                    'chart': ['chart.js'],
                },
                // Use hashed filenames for cache busting
                entryFileNames: 'assets/[name]-[hash].js',
                chunkFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash].[ext]',
            },
        },

        // Generate sourcemaps only in development
        sourcemap: false,
    },

    // Optimize dev server
    server: {
        hmr: {
            overlay: true,
        },
    },
});
