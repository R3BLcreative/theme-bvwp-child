/** @type {import('tailwindcss').Config} */
module.exports = {
	content: ['./public/**/*.{php,js}'],
	theme: {
		screens: {
			mobile: '0px',
			tablet: '767px',
			laptop: '991px',
			desktop: '1281px',
		},
		extend: {},
	},
	plugins: [require('@tailwindcss/forms')],
};
