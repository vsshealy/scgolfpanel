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
        sass          = require('gulp-sass')(require('sass')),
        postcss       = require('gulp-postcss'),
        autoprefixer  = require('autoprefixer'),
        sortmq        = require('postcss-sort-media-queries'),
        minifycss     = require('gulp-clean-css'),
        plumber       = require('gulp-plumber'),
        rename        = require('gulp-rename'),
        sourcemaps    = require('gulp-sourcemaps'),
        concat        = require('gulp-concat'),
        minifyjs      = require('gulp-terser'),
        sharp         = require('sharp'),
        path          = require('path'),
        { Transform } = require('stream')
    ;

// FILE PATHS
    const imagesRoot = '../../../../../../_files/images';

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
            root: imagesRoot,
            src:  path.join(imagesRoot, '**/**/02-edits/*.webp'),
            svg:  path.join(imagesRoot, '**/**/02-edits/*.svg'),
            exportsDir: '03-exports',
            compressedDir: '04-compressed'
        }
    };

    function renameDirectorySegment(dirname, sourceSegment, targetSegment) {
        if (path.basename(dirname) !== sourceSegment) {
            return dirname;
        }

        return path.join(path.dirname(dirname), targetSegment);
    }

// TASK | CSS
    function compileCSS() {
        return gulp
            .src(paths.sass.src, {allowEmpty: true})
            .pipe(plumber())
            .pipe(sourcemaps.init({loadMaps: true}))
            .pipe(sass.sync({outputStyle: 'expanded'}).on('error', sass.logError))
            .pipe(postcss([
                autoprefixer({overrideBrowserslist: ['last 4 versions']}),
                sortmq()
            ]))
            .pipe(gulp.dest(paths.sass.dest))
            .pipe(rename({suffix: '.min'}))
            .pipe(minifycss())
            .pipe(sourcemaps.write('.'))
            .pipe(gulp.dest(paths.sass.dest));
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
            .pipe(gulp.dest(paths.js.dest));
    }

// TASK | IMAGES
    const imageSizes = [
        {width: 1200, height: 675},
        {width: 1200, height: 800},
        {width: 1200, height: 1200},
        {width: 1200, height: 1500},
        {width: 1920, height: 1080},
        {width: 2560, height: 640},
        {width: 2560, height: 1100},
        {width: 3840, height: 2560}
    ];

    const resizeSettings = {
        webp: {lossless: true},
        avif: {lossless: true}
    };

    const compressSettings = {
        webp: {quality: 75},
        avif: {quality: 60}
    };

    function resizeImages() {
        return gulp
            .src(paths.images.src, {
                allowEmpty: true,
                encoding: false,
                base: paths.images.root
            })
            .pipe(plumber())
            .pipe(new Transform({
                objectMode: true,
                transform(file, enc, callback) {
                    if (file.isNull() || file.isDirectory()) {
                        return callback(null, file);
                    }

                    const name = path.basename(file.path, path.extname(file.path));
                    const conversions = [];

                    imageSizes.forEach(size => {
                        ['webp', 'avif'].forEach(format => {
                            const outName = `${name}-${size.width}x${size.height}.${format}`;

                            conversions.push(
                                sharp(file.contents)
                                    .resize({
                                        width:    size.width,
                                        height:   size.height,
                                        fit:      'cover',
                                        position: 'centre'
                                    })
                                    [format](resizeSettings[format])
                                    .toBuffer()
                                    .then(buffer => {
                                        const cloned = file.clone();
                                        cloned.contents = buffer;
                                        cloned.path = path.join(path.dirname(file.path), outName);
                                        return cloned;
                                    })
                            );
                        });
                    });

                    Promise.all(conversions)
                        .then(files => {
                            files.forEach(f => this.push(f));
                            callback();
                        })
                        .catch(err => callback(err));
                }
            }))
            .pipe(rename(filePath => {
                filePath.dirname = renameDirectorySegment(filePath.dirname, '02-edits', paths.images.exportsDir);
            }))
            .pipe(gulp.dest(paths.images.root));
    }

    function compressImages() {
        return gulp
            .src([
                path.join(paths.images.root, '**/**', paths.images.exportsDir, '*.webp'),
                path.join(paths.images.root, '**/**', paths.images.exportsDir, '*.avif')
            ], {
                allowEmpty: true,
                encoding: false,
                base: paths.images.root
            })
            .pipe(plumber())
            .pipe(new Transform({
                objectMode: true,
                transform(file, enc, callback) {
                    if (file.isNull() || file.isDirectory()) {
                        return callback(null, file);
                    }

                    const ext = path.extname(file.path).toLowerCase().replace('.', '');
                    const settings = compressSettings[ext];

                    sharp(file.contents)
                        [ext](settings)
                        .toBuffer()
                        .then(buffer => {
                            file.contents = buffer;
                            callback(null, file);
                        })
                        .catch(err => callback(err));
                }
            }))
            .pipe(rename(filePath => {
                filePath.dirname = renameDirectorySegment(filePath.dirname, paths.images.exportsDir, paths.images.compressedDir);
            }))
            .pipe(gulp.dest(paths.images.root));
    }

    function copySVGs() {
        return gulp
            .src(paths.images.svg, {
                allowEmpty: true,
                base: paths.images.root
            })
            .pipe(plumber())
            .pipe(rename(filePath => {
                filePath.dirname = renameDirectorySegment(filePath.dirname, '02-edits', paths.images.exportsDir);
            }))
            .pipe(gulp.dest(paths.images.root));
    }

    const processImages = gulp.series(resizeImages, compressImages);

// TASK | WATCHFILES
    function watchFiles() {
        gulp.watch(paths.sass.dir, compileCSS);
        gulp.watch(paths.js.dir, compileJS);
        gulp.watch(paths.images.src, processImages);
        gulp.watch(paths.images.svg, copySVGs);
        gulp.watch([
            path.join(paths.images.root, '**/**', paths.images.exportsDir, '*.webp'),
            path.join(paths.images.root, '**/**', paths.images.exportsDir, '*.avif')
        ], compressImages);
    }

// EXECUTE TASKS
    // JOB 1: COMPILE SASS AND JS
        exports.compile = gulp.parallel(compileCSS, compileJS);

    // JOB 2: PROCESS AND COMPRESS IMAGES
        exports.images = gulp.series(processImages, compressImages, copySVGs);

    // DEFAULT: COMPILE SASS AND JS THEN WATCH FOR CHANGES
        exports.default = gulp.series(exports.compile, watchFiles);