import {defineConfig} from 'vite'
import tailwindcss from "@tailwindcss/vite";
import queryHash from 'vite-plugin-query-hash'
import sortMediaQueries from 'postcss-sort-media-queries'

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
                '/fonts/Moderat-Bold.woff2',
                '/fonts/Moderat-Regular.woff2',
                '/images/site/play.svg',
            ],
            input: {
                'app': './resources/js/app.ts',
            },
            output: {
                assetFileNames: '[name].[ext]',
                chunkFileNames: '[name].js',
                entryFileNames: '[name].js',
            },
            preserveEntrySignatures: 'strict',
        },
    },
    css: {
        postcss: {
            plugins: [
                sortMediaQueries(),
            ],
        },
    },
    plugins: [
        queryHash({queryParamValue: Math.floor(Date.now() / 60000).toString(36)}),
        tailwindcss(),
    ]
}));