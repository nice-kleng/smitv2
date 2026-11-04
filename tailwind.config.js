import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './Modules/Inventory/resources/views/pwa/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                ink: '#17201D',
                paper: '#F4F6F4',
                surface: '#FFFFFF',
                brand: { DEFAULT: '#C8102E', deep: '#8E0B20' },
                muted: '#5C6864',
                line: { DEFAULT: '#D9DFDC', strong: '#8A9691' },
                success: '#0B6B4F',
                danger: '#B3261E',
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                mono: ['"IBM Plex Mono"', ...defaultTheme.fontFamily.mono],
            },
        },
    },
    plugins: [],
};