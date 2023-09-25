const defaultTheme = require('tailwindcss/defaultTheme')

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{vue,js,jsx}',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                dark: {
                    'eval-0': '#151823',
                    'eval-1': '#222738',
                    'eval-2': '#2A2F42',
                    'eval-3': '#2C3142',
                },
                gray: {
                    25: '#FCFCFD',
                    50: '#F9FAFB',
                    100: '#F3F4F6',
                    200: '#E5E7EB',
                    300: '#D2D6DB',
                    400: '#9DA4AE',
                    500: '#6C737F',
                    600: '#4D5761',
                    700: '#384250',
                    800: '#1F2A37',
                    900: '#111927',
                    950: '#0D121C',
                },
                pink: {
                    25: '#FFFBFA',
                    50: '#FEF3F2',
                    100: '#FFDCD5',
                    200: '#FFB2AB',
                    300: '#FF8181',
                    400: '#FF6170',
                    500: '#FF2D55',
                    600: '#DB2056',
                    700: '#B71653',
                    800: '#930E4D',
                    900: '#7A0849',
                },
                success: {
                    25: '#F6FEF9',
                    50: '#ECFDF3',
                    100: '#D1FADF',
                    200: '#A6F4C5',
                    300: '#6CE9A6',
                    400: '#32D583',
                    500: '#12B76A',
                    600: '#039855',
                    700: '#027A48',
                    800: '#05603A',
                    900: '#054F31',
                },
                warning: {
                    25: '#FFFCF5',
                    50: '#FFFAEB',
                    100: '#FEF0C7',
                    200: '#FEDF89',
                    300: '#FEC84B',
                    400: '#FDB022',
                    500: '#F79009',
                    600: '#DC6803',
                    700: '#B54708',
                    800: '#93370D',
                    900: '#7A2E0E',
                },
                error: {
                    25: '#FFFBFA',
                    50: '#FEF3F2',
                    100: '#FEE4E2',
                    200: '#FECDCA',
                    300: '#FDA29B',
                    400: '#F97066',
                    500: '#F04438',
                    600: '#D92D20',
                    700: '#B42318',
                    800: '#912018',
                    900: '#7A271A',
                },
                purple: '#AF52DE',
                indigo: '#5856D6',
                cyan: '#32ADE6',
                mint: '#00C7BE',
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
}
