import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/notification-realtime.js',
                'resources/js/providers.js',
                'resources/js/admin.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources'),
            '@components': resolve(__dirname, 'resources/js/components'),
            '@utils': resolve(__dirname, 'resources/js/utils'),
            '@assets': resolve(__dirname, 'resources/assets'),
            '@css': resolve(__dirname, 'resources/css'),
            '@js': resolve(__dirname, 'resources/js'),
            '@images': resolve(__dirname, 'resources/images'),
            '@fonts': resolve(__dirname, 'resources/fonts'),
        },
    },
    server: {
        host: 'localhost',
        port: 5173,
        strictPort: false,
        open: false,
        hmr: {
            host: 'localhost',
        },
        watch: {
            ignored: [
                '**/storage/framework/views/**',
                '**/storage/logs/**',
                '**/node_modules/**',
                '**/vendor/**',
            ],
        },
    },
    build: {
        target: 'es2020',
        outDir: 'public/build',
        emptyOutDir: true,
        sourcemap: process.env.NODE_ENV === 'development',
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['axios', 'laravel-echo'],
                    filament: ['@tailwindcss/vite'],
                    realtime: ['pusher-js'],
                },
                chunkFileNames: 'assets/js/[name]-[hash].js',
                entryFileNames: 'assets/js/[name]-[hash].js',
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name && assetInfo.name.endsWith('.css')) {
                        return 'assets/css/[name]-[hash][extname]';
                    }
                    return 'assets/[name]-[hash][extname]';
                },
            },
        },
        cssCodeSplit: true,
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: process.env.NODE_ENV === 'production',
                drop_debugger: process.env.NODE_ENV === 'production',
            },
        },
    },
    optimizeDeps: {
        include: [
            'axios',
            'laravel-echo',
            'pusher-js',
            '@tailwindcss/vite',
        ],
    },
    define: {
        __VUE_OPTIONS_API__: JSON.stringify(true),
        __VUE_PROD_DEVTOOLS__: JSON.stringify(false),
    },
});
