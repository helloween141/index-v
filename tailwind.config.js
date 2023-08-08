module.exports = {
    darkMode: 'class',
    content: [
        './node_modules/flowbite/**/*.js',
        './index.html',
        './Modules/*/Resources/**/*.{vue,js,jsx,tsx,blade.php}'
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('flowbite/plugin')
    ],
}
