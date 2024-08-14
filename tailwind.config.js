const plugin = require('tailwindcss/plugin')

// noinspection JSUnusedGlobalSymbols
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './app/models/*.php',
        './app/helpers/*.php',
        './app/widgets/*.php',
        './resources/js/*.ts',
        './resources/views/**/*.php',
    ],
    corePlugins: {
        backgroundOpacity: false,
        textOpacity: false,
    },
    future: {
        hoverOnlyWhenSupported: true,
    },
    plugins: [
        plugin(function ({addVariant}) {
            addVariant('nf', '&:not(:first-child)');
        })
    ],
    theme: {
        extend: {
            aspectRatio: {
            },
            colors: {
            },
            spacing: {
                vsx: 'var(--vsx)',
                vss: 'var(--vss)',
                vsm: 'var(--vsm)',
                vsl: 'var(--vsl)',
            },
            transitionDuration: {
                DEFAULT: '.3s',
            },
        },
        // fontFamily: {
        // },
        screens: {
            xs: '22rem', // 352px
            sm: '48rem', // 768px
            md: '64rem', // 1024px
            lg: '80rem', // 1280px
            xl: '100rem', // 1600px
        },
    },
}

