module.exports = {
	content: ['./resources/**/*'],
	corePlugins: {
		preflight: false,
	},
	prefix: 'i-',
	darkMode: ['variant', ['html[class*="dark"] &']],
}
