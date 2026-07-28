import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                // This font pairs well with the "Axiom" design
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'brand-green': '#65C34A', // Your primary logo green
                'brand-blue': '#1F6BFF',  // Your secondary logo blue
                'light-gray': '#F7F8FC', // The "Axiom" background

                // Priority colors
                'priority-low': '#FBBF24',
                'priority-medium': '#F59E0B',
                'priority-high': '#EF4444',
            }
        },
    },

    plugins: [forms],
};
