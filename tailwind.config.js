const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                maroon: {
                    50: '#fdf2f2',
                    100: '#fde8e8',
                    200: '#fbd5d5',
                    300: '#f8b4b4',
                    400: '#f39090',
                    500: '#ed6464',
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                    950: '#450a0a',
                },
                ojt: {
                    primary: '#8B0000',    // Deep maroon
                    secondary: '#A0522D',  // Saddle brown
                    accent: '#FFD700',     // Gold
                    light: '#F8F9FA',      // Light gray
                    dark: '#343A40',       // Dark gray
                    success: '#28a745',    // Green
                    warning: '#ffc107',    // Yellow
                    danger: '#dc3545',     // Red
                }
            },
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
