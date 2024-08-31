// /** @type {import('tailwindcss').Config} */
// export default {
//   content: [
//     "./resources/**/*.blade.php",
//     "./resources/**/*.js",
//     "./resources/**/*.vue",
//     'node_modules/preline/dist/*.js',
//   ],
//   theme: {
//     extend: {},
//   },
//   plugins: [
//     require('preline/plugin'),
//   ],
// }


/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    'node_modules/preline/dist/*.js',
  ],
  theme: {
    extend: {
      fontFamily: {
        audiowide: ['Audiowide', 'sans-serif'],
      },
      screens: {
        'ipad-pro': { 'min': '1024px', 'max': '1366px' },
        '2xl': '1030px',
      },
    },
  },
  plugins: [
    require('preline/plugin'),
  ],
}
