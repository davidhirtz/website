/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.php',
    ],
    theme: {
        extend: {
            screens: {
                xs: '24rem',
                sm: '40rem',
                md: '48rem',
                lg: '64rem',
                xl: '80rem',
            }
        },
    },
    plugins: [],
}

