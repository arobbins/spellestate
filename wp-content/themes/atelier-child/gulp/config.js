module.exports = {
	paths: {
		project: './',
		css: {
			entry: './assets/css/local/styles.scss',
			all: './assets/css/local/**/*.scss'
		},
		js: {
			entry: './assets/js/local/app.js',
      vendor: './assets/js/vendor/*.js',
      dest: './assets/js',
      all: './assets/js/**/*.js'
		}
	},
	names: {
		css: 'style.css',
		js: {
			app: 'app.min.js',
      vendor: 'vendor.min.js'
		}
	}
};