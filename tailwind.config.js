// Compiled Tailwind build for the whole app (replaces the in-browser cdn.tailwindcss.com script).
// Rebuild after changing any Blade view:  npm run css
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/**/*.php',
    ],
    // Classes assembled at runtime (e.g. bg-{{ $accent }}-50) are invisible to the scanner.
    safelist: [
        { pattern: /^(bg|text|border|ring)-(emerald|rose|indigo|sky|amber|violet|slate|zinc)-(50|100|200|300|400|500|600|700)$/ },
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
                mono: ['JetBrains Mono', 'monospace'],
            },
            colors: { brand: '#4f46e5' },
            boxShadow: {
                glass: '0 8px 32px 0 rgba(31, 38, 135, 0.07)',
                soft: '0 10px 40px -10px rgba(0,0,0,0.05)',
                floating: '0 20px 60px -10px rgba(79, 70, 229, 0.15)',
            },
        },
    },
    plugins: [],
};
