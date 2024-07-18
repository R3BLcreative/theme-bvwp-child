module.exports = {
	plugins: {
		'tailwindcss/nesting': {},
		tailwindcss: {},
		'postcss-prefix-selector': {
			prefix: 'body.infinite-css',
		},
		autoprefixer: {},
	},
};
