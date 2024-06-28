// Replace @tailwindcss/postcss with @tailwindcss/vite once it's possible to set
// Tailwind V4's output directly in the Vite config.
// import tailwindcss from '@tailwindcss/vite'
import tailwind from '@tailwindcss/postcss'
import {defineConfig} from 'vite'
import viteRestart from 'vite-plugin-restart'
import purgeCSS from "@fullhuman/postcss-purgecss";

// @ts-ignore
import sortMediaQueries from 'postcss-sort-media-queries'

// noinspection JSUnusedGlobalSymbols
export default defineConfig(({}) => ({
    // base: command === 'serve' ? '' : '/dist/',
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
                '/images',
                '/fonts',
            ],
            input: {
                app: './resources/js/app.ts',
            },
        },
    },
    css: {
        postcss: {
            plugins: [
                tailwind({
                    base: './resources/',
                }),
                sortMediaQueries(),
                purgeCSS({
                    content: ['./resources/views/**/*.php'],
                    variables: true,
                }),
            ],
        },
    },
    plugins: [
        // tailwindcss(),
        viteRestart({
            restart: [
                './resources/views/**/*.php',
            ]
        })
    ],
}));