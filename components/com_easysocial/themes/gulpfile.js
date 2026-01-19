'use strict';

var gulp = require('gulp'),
	rename = require('gulp-rename'),
	cssimport = require('gulp-cssimport'),
	rtlcss = require('gulp-rtlcss'),
	cssnano = require('gulp-cssnano');

var options = {};

gulp.task("build-rtl-dev", function() {
	gulp.src("wireframe/styles/style.css")
		.pipe(cssimport(options))
		.pipe(rename('style-for-rtl.css'))
		.pipe(gulp.dest("wireframe/styles/"))

		.pipe(rtlcss()) 
		.pipe(rename('style-rtl.css'))
		.pipe(gulp.dest('wireframe/styles/'))
}); 

gulp.task("build-rtl", function() {
	gulp.src("wireframe/styles/style.css")
		.pipe(cssimport(options))
		.pipe(rename('style-for-rtl.css'))
		.pipe(gulp.dest("wireframe/styles/"))

		.pipe(rtlcss()) 
		.pipe(rename('style-rtl.css'))
		.pipe(gulp.dest('wireframe/styles/'))
		
		.pipe(cssnano({discardComments: {removeAll: true}, zindex: false}))
		.pipe(rename({suffix: '.min'}))
		.pipe(gulp.dest('wireframe/styles/'))
}); 