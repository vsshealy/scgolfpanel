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

    const paths = {
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
            src:  [
                path.join(imagesRoot, '**/**/02-edits/*.*'),
                '!' + path.join(imagesRoot, '**/**/02-edits/*.svg')
            ],
            svg:  path.join(imagesRoot, '**/**/02-edits/*.svg'),
            editsDir:      '02-edits',
            exportsDir:    '03-exports',
            compressedDir: '04-compressed'
        },
        oneOff: {
            root: path.join(imagesRoot, '_one-off'),
            src:  path.join(imagesRoot, '_one-off', '*.{tif,tiff}'),
            compressedDir: path.join(imagesRoot, '_one-off', 'compressed')
        }
    };

// HELPERS
    function renameDirectorySegment(dirname, sourceSegment, targetSegment) {
        if (path.basename(dirname) !== sourceSegment) {
            return dirname;
        }

        return path.join(path.dirname(dirname), targetSegment);
    }

    function exportsGlob() {
        return imageFormats.map(format =>
            path.join(paths.images.root, '**/**', paths.images.exportsDir, `*.${format}`)
        );
    }

    function exportsForSource(filepath) {
        const dirname = renameDirectorySegment(
            path.dirname(filepath),
            paths.images.editsDir,
            paths.images.exportsDir
        );
        const name = path.basename(filepath, path.extname(filepath));

        return imageFormats.map(format =>
            path.join(dirname, `${name}-[0-9]*x[0-9]*.${format}`)
        );
    }

    function familiesForFile(filePath) {
        const rel = path.relative(imagesRootAbs, path.resolve(filePath));
        const top = rel.split(path.sep)[0];

        return (familiesByFolder[top] || familiesDefault).map(name => imageFamilies[name]);
    }

// IMAGE CONFIGURATION
    const imagesRootAbs = path.resolve(imagesRoot);

    const imageFormats = ['webp', 'avif'];

    const imageFamilies = {
        'hero-primary':   {width: 3780, height: 1620},
        'hero-secondary': {width: 3780, height:  540},
        'gallery':        {width: 2880, height: 1620},
        'featured':       {width: 1200, height:  800},
        'square':         {width: 1080, height: 1080}
    };

    const familiesByFolder = {
        courses:      ['hero-primary', 'hero-secondary', 'gallery', 'featured'],
        placeholders: ['hero-primary', 'hero-secondary', 'gallery', 'featured'],
        logos:        ['square']
    };

    const familiesDefault = ['hero-primary', 'hero-secondary', 'gallery', 'featured'];

    const resizeSettings = {
        webp: {lossless: true},
        avif: {lossless: true}
    };

    const compressSettings = {
        webp: {quality: 82},
        avif: {quality: 70}
    };

    const watchImageSettings = {
        ignoreInitial: true,
        awaitWriteFinish: {
            stabilityThreshold: 2000,
            pollInterval: 100
        }
    };

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
    function resizeImagesFor(src) {
        return gulp
            .src(src, {
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

                    familiesForFile(file.path).forEach(size => {
                        imageFormats.forEach(format => {
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
                filePath.dirname = renameDirectorySegment(
                    filePath.dirname,
                    paths.images.editsDir,
                    paths.images.exportsDir
                );
            }))
            .pipe(gulp.dest(paths.images.root));
    }

    function compressImagesFor(src) {
        return gulp
            .src(src, {
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

                    const format = path.extname(file.path).toLowerCase().replace('.', '');

                    sharp(file.contents)
                        [format](compressSettings[format])
                        .toBuffer()
                        .then(buffer => {
                            file.contents = buffer;
                            callback(null, file);
                        })
                        .catch(err => callback(err));
                }
            }))
            .pipe(rename(filePath => {
                const format = filePath.extname.toLowerCase().replace('.', '');

                filePath.dirname = path.join(
                    renameDirectorySegment(
                        filePath.dirname,
                        paths.images.exportsDir,
                        paths.images.compressedDir
                    ),
                    format
                );
            }))
            .pipe(gulp.dest(paths.images.root));
    }

    function copySVGsFor(src) {
        return gulp
            .src(src, {
                allowEmpty: true,
                base: paths.images.root
            })
            .pipe(plumber())
            .pipe(rename(filePath => {
                filePath.dirname = renameDirectorySegment(
                    filePath.dirname,
                    paths.images.editsDir,
                    paths.images.exportsDir
                );
            }))
            .pipe(gulp.dest(paths.images.root));
    }

    function resizeImages() {
        return resizeImagesFor(paths.images.src);
    }

    function compressImages() {
        return compressImagesFor(exportsGlob());
    }

    function copySVGs() {
        return copySVGsFor(paths.images.svg);
    }

// TASK | ONE-OFF ART-DIRECTED CROPS
    function compressOneOff() {
        return gulp
            .src(paths.oneOff.src, {
                allowEmpty: true,
                encoding: false,
                base: paths.oneOff.root
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

                    imageFormats.forEach(format => {
                        const outName = `${name}.${format}`;

                        conversions.push(
                            sharp(file.contents)
                                [format](compressSettings[format])
                                .toBuffer()
                                .then(buffer => {
                                    const cloned = file.clone();
                                    cloned.contents = buffer;
                                    cloned.path = path.join(path.dirname(file.path), outName);
                                    return cloned;
                                })
                        );
                    });

                    Promise.all(conversions)
                        .then(files => {
                            files.forEach(f => this.push(f));
                            callback();
                        })
                        .catch(err => callback(err));
                }
            }))
            .pipe(gulp.dest(paths.oneOff.compressedDir));
    }

    const processImages = gulp.series(resizeImages, compressImages);

// TASK | WATCHFILES
    const imageQueue = [];
    let imageQueueBusy = false;

    function queueImage(filepath) {
        if (imageQueue.indexOf(filepath) === -1) {
            imageQueue.push(filepath);
        }

        runImageQueue();
    }

    function runImageQueue() {
        if (imageQueueBusy || !imageQueue.length) {
            return;
        }

        imageQueueBusy = true;

        processImage(imageQueue.shift(), () => {
            imageQueueBusy = false;
            runImageQueue();
        });
    }

    function processImage(filepath, done) {
        const label    = path.basename(filepath);
        const resize   = () => resizeImagesFor(filepath);
        const compress = () => compressImagesFor(exportsForSource(filepath));

        resize.displayName   = `resize:${label}`;
        compress.displayName = `compress:${label}`;

        gulp.series(resize, compress)(done);
    }

    function processSVG(filepath) {
        const copy = () => copySVGsFor(filepath);

        copy.displayName = `copy:${path.basename(filepath)}`;

        gulp.series(copy)(() => {});
    }

    function watchFiles() {
        gulp.watch(paths.sass.dir, compileCSS);
        gulp.watch(paths.js.dir, compileJS);

        gulp
            .watch(paths.images.src, watchImageSettings)
            .on('add', queueImage)
            .on('change', queueImage);

        gulp
            .watch(paths.images.svg, watchImageSettings)
            .on('add', processSVG)
            .on('change', processSVG);
    }

// EXECUTE TASKS
    // JOB 1: COMPILE SASS AND JS
        exports.compile = gulp.parallel(compileCSS, compileJS);

    // JOB 2: PROCESS AND COMPRESS IMAGES
        exports.images = gulp.series(processImages, copySVGs);

    // JOB 3: COMPRESS ONE-OFF ART-DIRECTED CROPS (NO RESIZE)
        exports.compressOneOff = compressOneOff;

    // DEFAULT: COMPILE SASS AND JS THEN WATCH FOR CHANGES
        exports.default = gulp.series(exports.compile, watchFiles);