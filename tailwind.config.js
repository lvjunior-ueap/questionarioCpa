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
                ueap: {
                  blue: '#1F4E79',
                  green: '#2E7D32',
                  orange: '#E06A2C',
                  yellow: '#F2C94C',
                  bg: '#F8FAFC',
                  text: '#1F2933',
                  muted: '#4B5563'
                }
              },
              
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
