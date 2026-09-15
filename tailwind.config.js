import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Solid primary palette around #139dd5 (no gradients).
                // `brand.*` accents are mapped onto the primary so existing
                // utility classes recolour automatically.
                primary: {
                    50: '#e8f6fc',
                    100: '#c6eafa',
                    200: '#94d8f4',
                    300: '#5cc2ec',
                    400: '#2ba7da',
                    500: '#139dd5',
                    600: '#0f7fae',
                    700: '#0d6790',
                    800: '#0f5675',
                    900: '#114a63',
                },
                brand: {
                    blue: '#139dd5',
                    cyan: '#2ba7da',
                    purple: '#139dd5',   // accent → primary
                    magenta: '#0f7fae',  // darker primary for subtle variety
                    ink: '#12263b',
                    50: '#e8f6fc',
                    100: '#c6eafa',
                    200: '#94d8f4',
                    300: '#5cc2ec',
                    400: '#2ba7da',
                    500: '#139dd5',
                    600: '#0f7fae',
                    700: '#0d6790',
                    800: '#0f5675',
                    900: '#114a63',
                },
            },
            backgroundImage: {
                // Kept as named utilities but now render SOLID primary.
                'brand-gradient': 'linear-gradient(#139dd5, #139dd5)',
                'brand-gradient-soft':
                    'linear-gradient(rgba(19,157,213,0.10), rgba(19,157,213,0.10))',
            },
            boxShadow: {
                brand: '0 16px 36px -18px rgba(19,157,213,0.45)',
            },
        },
    },

    plugins: [forms],
};
