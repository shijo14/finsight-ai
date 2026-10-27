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
                sans: ['Roboto', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                finsight: {
                    bg: '#0a0f1a',
                    surface: 'rgba(255, 255, 255, 0.05)',
                    border: 'rgba(255, 255, 255, 0.1)',
                    accent: '#22d3ee',
                    'accent-dim': '#0891b2',
                    glow: 'rgba(34, 211, 238, 0.15)',
                },
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-out forwards',
                'slide-up': 'slideUp 0.5s ease-out forwards',
                'pulse-soft': 'pulseSoft 3s ease-in-out infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                pulseSoft: {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0.7' },
                },
            },
            backgroundImage: {
                'finsight-gradient': 'radial-gradient(ellipse at top, rgba(34, 211, 238, 0.08) 0%, transparent 50%), radial-gradient(ellipse at bottom right, rgba(99, 102, 241, 0.06) 0%, transparent 50%), linear-gradient(180deg, #0a0f1a 0%, #0f172a 50%, #0a0f1a 100%)',
            },
        },
    },

    plugins: [forms],
};
