'use strict';

const gulp = require('gulp');
const sass = require('gulp-sass');
const cached = require('gulp-cached');
const dependents = require('gulp-dependents');
const debug = require('gulp-debug');
const sourcemaps = require('gulp-sourcemaps');

sass.compiler = require('node-sass');

// const sassFiles = process.env.SASS || '**/*.+(sass|scss)';
const config = {
    'sass': '/site/theme/sass/**/*.scss',
    'css': '/site/theme'
};

console.log(`SASS: ${config.sass}`)
console.log(`CSS: ${config.css}`)

gulp.task('sass', function () {
    return gulp.src(config.sass)
        .pipe(cached('sasscache'))
        .pipe(debug({title: 'cache pass:'}))
        .pipe(dependents())
        .pipe(debug({title: 'dependents:'}))
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(config.css));
});

gulp.task('sass:watch', function () {
    gulp.watch(config.sass, gulp.series('sass'));
});

gulp.task('default', gulp.series('sass', 'sass:watch'));

