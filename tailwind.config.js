const colors = require('tailwindcss/colors')

module.exports = {
  content: [
    './resources/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
  ],
  theme: {
    extend: {
      colors: {
        danger: colors.rose,
        gray: colors.stone,
        primary: colors.blue,
        success: colors.green,
        warning: colors.yellow,
      },
      fontFamily: {
        sans: ['DM Sans'],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
