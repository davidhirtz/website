import {defineConfig} from 'vite'
import tailwindcss from "@tailwindcss/vite";
// @ts-ignore-next-line
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
                '/images/site/play.svg',
            ],
            input: {
                'app': './resources/js/app.ts',
            },
            preserveEntrySignatures: 'strict',
        },
    },
    css: {
        postcss: {
            plugins: [
                sortMediaQueries({
                    onlyTopLevel: true,
                }),
            ],
        },
    },
    plugins: [
        tailwindcss(),
    ]
}));