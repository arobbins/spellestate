var gulp = require('gulp'),
    config = require('../config'), // Relative to this file
    sass = require('gulp-sass'),
    rename = require('gulp-rename'),
    browserSync = require('browser-sync'),
    please = require('gulp-pleeease'),
    reload = browserSync.reload;

// Pre and post processing our CSS
gulp.task('styles', function() {
	return gulp.src(config.paths.css.entry)
		.pipe(sass())
		.pipe(please({
      "autoprefixer": true,
      "filters": true,
      "rem": true,
      "opacity": true
    }))
		.pipe(rename(config.names.css))
		.pipe(gulp.dest(config.paths.project))
		.pipe(reload({
			stream: true
		}));
});