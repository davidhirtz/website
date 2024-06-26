import tailwindcss from '@tailwindcss/vite'
import {defineConfig} from 'vite'
import purgeCSS from "@fullhuman/postcss-purgecss";

// @ts-ignore
import sortMediaQueries from 'postcss-sort-media-queries'

// noinspection JSUnusedGlobalSymbols
export default defineConfig(({}) => ({
    // base: command === 'serve' ? '' : '/dist/',
    build: {
        assetsDir: '',
        assetsInlineLimit: 0,
        outDir: './web/build/',
        manifest: true,
        modulePreload: {
            polyfill: false,
        },
        rollupOptions: {
            external: [
                '/images',
                '/fonts',
            ],
            input: {
                app: './resources/js/app.ts',
            }
        },
    },
    css: {
        postcss: {
            plugins: [
                sortMediaQueries(),
                purgeCSS({
                    content: ['./resources/views/**/*.php'],
                    variables: true,
                }),
            ],
        },
    },
    plugins: [tailwindcss()]
}));