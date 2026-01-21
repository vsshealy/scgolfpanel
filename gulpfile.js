/**
 * gulpfile.js
 * @package scgolfpanel
 * @author Scott Shealy
 * @version 1.0.0 (2026.01.01)
 * @copyright 2026 (2026.01.01)
**/

// PLUGINS
    const
        gulp = require('gulp'),
        sass = require('gulp-dart-sass'),
        autoprefixer = require('gulp-autoprefixer'),
        mediaqueries = require('gulp-group-css-media-queries'),
        minifycss = require('gulp-clean-css'),
        plumber = require('gulp-plumber'),
        rename = require('gulp-rename'),
        sourcemaps = require('gulp-sourcemaps'),
        concat = require('gulp-concat'),
        minifyjs = require('gulp-terser')
    ;

// FILE PATHS
    var paths = {
        root: '.',

        sass: {
            src: './sass/style.scss',
            dir: './sass/**/**/*.scss',
            dest: '.'
        },

        js: {
            src: './js/script.js',
            dir: './js/**/*.js',
            dest: '.'
        }
    }

// TASK | CSS
    function compileCSS() {
        return gulp
            .src(paths.sass.src, {allowEmpty: true})
            .pipe(plumber())
            .pipe(sourcemaps.init({loadMaps: true}))
            .pipe(sass({outputStyle: 'expanded'}))
            .pipe(autoprefixer('last 4 versions'))
            .pipe(sourcemaps.write())
            .pipe(mediaqueries())
            .pipe(gulp.dest(paths.sass.dest))

            .pipe(rename({suffix: '.min'}))
            .pipe(minifycss())
            .pipe(gulp.dest(paths.sass.dest))
    }

// TASK | JS
    function compileJS() {
        return gulp
            .src(paths.js.src, {allowEmpty: true})
            .pipe(concat('script.js'))
            .pipe(gulp.dest(paths.js.dest))

            .pipe(rename({suffix: '.min'}))
            .pipe(minifyjs())
            .pipe(gulp.dest(paths.js.dest))
    }

// TASK | WATCHFILES
    function watchFiles() {
        gulp.watch(paths.sass.dir, compileCSS),
        gulp.watch(paths.js.dir, compileJS)
    }

// EXECUTE TASKS
    gulp.task('build', compileCSS, compileJS);
    gulp.task('default', gulp.series('build', watchFiles));