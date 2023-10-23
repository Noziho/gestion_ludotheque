/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        'darkBlue': '#0f172a',
        'white' : ' #FAFAFA',
        'lightenDarkBlue' : '#1D2535',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}

