// Replace @tailwindcss/postcss with @tailwindcss/vite once it's possible to set
// Tailwind V4's output directly in the Vite config.
// import tailwindcss from '@tailwindcss/vite'
import autoprefixer from 'autoprefixer'
import {defineConfig} from 'vite'
import tailwindcss from "tailwindcss";
import nesting from 'tailwindcss/nesting'
// import viteRestart from 'vite-plugin-restart'
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
                tailwindcss(),
                nesting(),
                autoprefixer(),
                sortMediaQueries(),
                purgeCSS({
                    content: [
                        './resources/css/**/*.css',
                        './resources/js/**/*.ts',
                        './resources/views/**/*.php',
                        './app/models/*.php',
                    ],
                    defaultExtractor: (content) => content.match(/[\w-:./]+(?<!:)/g) || [],
                    variables: true,
                }),
            ],
        },
    },
    plugins: [
        // tailwindcss(),
        // viteRestart({
        //     restart: [
        //         './resources/**/*.{css,php}',
        //     ]
        // })
    ],
}));