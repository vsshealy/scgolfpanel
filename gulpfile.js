/**
 * gulpfile.js
 * @package scgolfpanel
 * @author Scott Shealy
 * @version 1.0.0 (2026.01.01)
 * @copyright 2026 (2026.01.01)
**/

// PLUGINS
    const
        gulp          = require('gulp'),
        sass          = require('gulp-dart-sass'),
        postcss       = require('gulp-postcss'),
        autoprefixer  = require('autoprefixer'),
        sortmq        = require('postcss-sort-media-queries'),
        minifycss     = require('gulp-clean-css'),
        plumber       = require('gulp-plumber'),
        rename        = require('gulp-rename'),
        sourcemaps    = require('gulp-sourcemaps'),
        concat        = require('gulp-concat'),
        minifyjs      = require('gulp-terser'),
        through2      = require('through2'),
        sharp         = require('sharp'),
        path          = require('path')
    ;

// FILE PATHS
    var paths = {
        root: '.',
        sass: {
            src:  './sass/style.scss',
            dir:  './sass/**/**/*.scss',
            dest: '.'
        },
        js: {
            src:  './js/**/*.js',
            dir:  './js/**/*.js',
            dest: '.'
        },
        images: {
            src:  './assets/images/src/**/*.{jpg,jpeg,png,webp,avif}',
            svg:  './assets/images/src/**/*.svg',
            dest: './assets/images/dist'
        }
    };

// TASK | CSS
    function compileCSS() {
        return gulp
            .src(paths.sass.src, {allowEmpty: true})
            .pipe(plumber())
            .pipe(sourcemaps.init({loadMaps: true}))
            .pipe(sass({outputStyle: 'expanded'}))
            .pipe(postcss([
                autoprefixer({overrideBrowserslist: ['last 4 versions']}),
                sortmq()
            ]))
            .pipe(sourcemaps.write('.'))
            .pipe(gulp.dest(paths.sass.dest))
            .pipe(rename({suffix: '.min'}))
            .pipe(minifycss())
            .pipe(gulp.dest(paths.sass.dest))
    }

// TASK | JS
    function compileJS() {
        return gulp
            .src(paths.js.src, {allowEmpty: true})
            .pipe(plumber())
            .pipe(concat('script.js'))
            .pipe(gulp.dest(paths.js.dest))
            .pipe(rename({suffix: '.min'}))
            .pipe(minifyjs())
            .pipe(gulp.dest(paths.js.dest))
    }

// TASK | IMAGES
    const imageSettings = {
        jpg:  {quality: 82},
        png:  {quality: 82, compressionLevel: 9},
        webp: {quality: 82},
        avif: {quality: 65}
    };

    function compressImages() {
        return gulp
            .src(paths.images.src, {allowEmpty: true, encoding: false})
            .pipe(plumber())
            .pipe(through2.obj(function(file, enc, callback) {
                if (file.isNull() || file.isDirectory()) {
                    return callback(null, file);
                }

                const ext = path.extname(file.path).toLowerCase().replace('.', '');
                const validTypes = ['jpg', 'jpeg', 'png', 'webp', 'avif'];

                if (!validTypes.includes(ext)) {
                    return callback(null, file);
                }

                const format   = (ext === 'jpeg') ? 'jpg' : ext;
                const settings = imageSettings[format];
                let sharpChain = sharp(file.contents);

                if (format === 'jpg') sharpChain = sharpChain.jpeg(settings);
                else if (format === 'png') sharpChain = sharpChain.png(settings);
                else if (format === 'webp') sharpChain = sharpChain.webp(settings);
                else if (format === 'avif')  sharpChain = sharpChain.avif(settings);

                sharpChain.toBuffer()
                    .then(buffer => {
                        file.contents = buffer;
                        callback(null, file);
                    })
                    .catch(err => callback(err));
            }))
            .pipe(gulp.dest(paths.images.dest))
    }

    function copySVGs() {
        return gulp
            .src(paths.images.svg, {allowEmpty: true})
            .pipe(plumber())
            .pipe(gulp.dest(paths.images.dest))
    }

// TASK | WATCHFILES
    function watchFiles() {
        gulp.watch(paths.sass.dir,    compileCSS);
        gulp.watch(paths.js.dir,      compileJS);
        gulp.watch(paths.images.src,  compressImages);
        gulp.watch(paths.images.svg,  copySVGs);
    }

// EXECUTE TASKS
    exports.build   = gulp.parallel(compileCSS, compileJS, compressImages, copySVGs);
    exports.default = gulp.series(exports.build, watchFiles);