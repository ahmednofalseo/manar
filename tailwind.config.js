/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        'cairo': ['Cairo', 'sans-serif'],
      },
      colors: {
        'primary': {
          DEFAULT: '#173343',
          50: '#e8f0f4',
          100: '#d1e1e9',
          200: '#a3c3d3',
          300: '#75a5bd',
          400: '#4787a7',
          500: '#173343',
          600: '#122936',
          700: '#0e1f29',
          800: '#09141c',
          900: '#050a0f',
        },
      },
    },
  },
  plugins: [],
}
