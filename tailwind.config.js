module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.{html,js,vue}',
    ],

    theme: {
        fontFamily: {
            Futura: ['Futura', "cursive"],
            FuturaItalic: ['FuturaItalic', "cursive"],
            FuturaMedium: ['FuturaMedium', "cursive"],
            FuturaBold: ['FuturaBold', "cursive"],
            FuturaLtCnBT:['FuturaLtCnBT', 'cursive'],
            FuturaMdCnBT:['FuturaMdCnBT','cursive'],
            FuturaBdCnBT:['FuturaBdCnBT', 'cursive'],
            Nunito:['Nunito'],
        },
        extend: {
            colors: {
                primary: '#2563eb',
                secondary: '#111827',
                aero: '#106BC7',
                greenXS: '#00A99D',
            },
            boxShadow: {
                '3xl': 'rgba(0, 0, 0, 0.16) 0px 1px 4px;',
            },
            screens: {
                'xs': '376px',
            }
        },
    },

    plugins: [require('@tailwindcss/forms'),require('tailwindcss-debug-screens'),],
};
