import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    primary: '#0F0F0F',
                    secondary: '#D4AF37',
                    bg: '#F8F6F2',
                    text: '#2B2B2B',
                    accent: '#0F5132',
                    tan: '#E5DDD0',
                    sand: '#C9B99A',
                    card: '#EDE8E0',
                },
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Playfair Display', ...defaultTheme.fontFamily.serif],
            },
            borderRadius: {
                '4xl': '2rem',
                '5xl': '2.5rem',
            },
            boxShadow: {
                luxury: '0 8px 32px rgba(15, 15, 15, 0.08)',
                pill: '0 4px 24px rgba(15, 15, 15, 0.12)',
            },
        },
    },

    plugins: [forms],
};
