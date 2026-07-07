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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                gray: {
                    105: '#f4f5f7',
                    150: '#eaebed',
                    205: '#dbdce0',
                    250: '#cbced3',
                    450: '#90949d',
                    455: '#858992',
                    550: '#676b73',
                    555: '#5c6068',
                    650: '#46494f',
                    750: '#31343a',
                    755: '#282b30',
                    850: '#1b1e24',
                    855: '#14161a',
                    955: '#0b0c10',
                },
                red: {
                    850: '#8e1c1c',
                    855: '#701a1a',
                },
                green: {
                    655: '#15803d',
                }
            }
        },
    },

    plugins: [forms],
};
