import {defineConfig} from 'vite'
import tailwindcss from "@tailwindcss/vite";

export default defineConfig(({}) => ({
    build: {
        assetsDir: '',
        assetsInlineLimit: 0,
        outDir: './web/dist/',
        manifest: true,
        modulePreload: {
            polyfill: false,
        },
        rollupOptions: {
            external: [
            ],
            input: {
                'app': './resources/js/app.ts',
            },
            preserveEntrySignatures: 'strict',
        },
    },
    plugins: [
        tailwindcss(),
    ],
}));