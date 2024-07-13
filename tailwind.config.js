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
    future: {
        hoverOnlyWhenSupported: true,
    },
    theme: {
        extend: {
            aspectRatio: {
            },
            colors: {
            },
            spacing: {
                'box-x': 'var(--box-x)',
                'box-y': 'var(--box-y)',
            },
            transitionDuration: {
                DEFAULT: '.3s',
            },
        },
        fontFamily: {
        },
        screens: {
            xs: '22rem', // 352px
            sm: '48rem', // 768px
            md: '64rem', // 1024px
            lg: '80rem', // 1280px
            xl: '100rem', // 1600px
        },
    },
}

