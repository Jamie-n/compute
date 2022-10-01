/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
    ],
    safelist: [
        'bg-green-800',
        'bg-red-800',
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/line-clamp'),
    ],
}
