import tailwindcss from '@tailwindcss/vite'
import {defineConfig} from 'vite'
import purgeCSS from "@fullhuman/postcss-purgecss";

// @ts-ignore
import sortMediaQueries from 'postcss-sort-media-queries'

// noinspection JSUnusedGlobalSymbols
export default defineConfig({
    build: {
        outDir: './web/dist',
        manifest: true,
        modulePreload: {
            polyfill: false,
        },
        rollupOptions: {
            input: {
                app: './resources/js/app.ts',
            }
        },
    },
    plugins: [tailwindcss()],
    css: {
        postcss: {
            plugins: [
                sortMediaQueries(),
                purgeCSS({
                    content: ['./index.html'],
                    variables: true,
                }),
            ],
        },
    }
})