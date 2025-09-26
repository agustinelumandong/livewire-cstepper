/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './src/**/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './examples/**/*.php',
        './examples/**/*.blade.php',
        './tests/**/*.php',
        './vendor/wireui/wireui/src/*.php',
        './vendor/wireui/wireui/ts/**/*.ts',
        './vendor/wireui/wireui/src/WireUi/**/*.php',
        './vendor/wireui/wireui/src/Components/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                // You can add custom colors here
            },
            animation: {
                'fadeInUp': 'fadeInUp 0.3s ease-out',
            },
            keyframes: {
                fadeInUp: {
                    from: {
                        opacity: '0',
                        transform: 'translateY(20px)',
                    },
                    to: {
                        opacity: '1',
                        transform: 'translateY(0)',
                    },
                },
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
    presets: [
        require('./vendor/wireui/wireui/tailwind.config.js')
    ],
}