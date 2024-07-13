import autoprefixer from 'autoprefixer'
import {defineConfig} from 'vite'
import tailwindcss from "tailwindcss";
import nesting from 'tailwindcss/nesting'
import purgeCSS from "@fullhuman/postcss-purgecss";
import mergeCss from 'postcss-combine-duplicated-selectors';
// @ts-ignore-next-line
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
                '/images/cc/arrow.svg',
            ],
            input: [
                './resources/js/app.ts',
            ],
            preserveEntrySignatures: 'strict',
        },
    },
    css: {
        postcss: {
            plugins: [
                nesting(),
                tailwindcss(),
                purgeCSS({
                    content: [
                        './app/models/*.php',
                        './app/helpers/*.php',
                        './app/widgets/*.php',
                        './resources/css/**/*.css',
                        './resources/js/**/*.ts',
                        './resources/views/**/*.php',
                    ],
                    defaultExtractor: (content: string) => content.match(/[\w\-:.\/\[#%\]]+(?<!:)/g) || [],
                    variables: true,
                }),
                autoprefixer(),
                mergeCss(),
                sortMediaQueries(),
            ],
        },
    },
}));