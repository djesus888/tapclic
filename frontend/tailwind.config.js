/** @type {import('tailwindcss').Config} */
module.exports = {

content: [
  "./index.html",
  "./**/*.{html,js,ts,vue,jsx,tsx}" 
],
  theme: {
    extend: {
      borderRadius: {
    '2xl': '1rem'
  }
},
  },
  plugins: [],
}

