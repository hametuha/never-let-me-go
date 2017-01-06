var gulp        = require('gulp'),
    fs          = require('fs'),
    $           = require('gulp-load-plugins')(),
    pngquant    = require('imagemin-pngquant'),
    eventStream = require('event-stream');


// Sass tasks
gulp.task('sass', function () {
  return gulp.src(['./assets/scss/**/*.scss'])
    .pipe($.plumber({
      errorHandler: $.notify.onError('<%= error.message %>')
    }))
    .pipe($.sourcemaps.init({loadMaps: true}))
    .pipe($.sassBulkImport())
    .pipe($.sass({
      errLogToConsole: true,
      outputStyle    : 'compressed',
      includePaths   : [
        './assets/scss'
      ]
    }))
    .pipe($.autoprefixer({browsers: ['last 2 version', '> 5%']}))
    .pipe($.sourcemaps.write('./map'))
    .pipe(gulp.dest('./dist/css'));
});


// Minify All
gulp.task('js', function () {
  return gulp.src(['./assets/js/**/*.js'])
    .pipe($.sourcemaps.init({
      loadMaps: true
    }))
    .pipe($.uglify())
    .on('error', $.util.log)
    .pipe($.sourcemaps.write('./map'))
    .pipe(gulp.dest('./dist/js/'));
});


// JS Hint
gulp.task('jshint', function () {
  return gulp.src(['./assets/js/**/*.js'])
    .pipe($.jshint('./assets/.jshintrc'))
    .pipe($.jshint.reporter('jshint-stylish'));
});

// Build libraries
gulp.task('copyLib', function () {
  // return eventStream.merge(
  //
  //   // Copy LigatureSymbols
  //   gulp.src([
  //     './src/fonts/**/*'
  //   ])
  //     .pipe(gulp.dest('./assets/fonts/')),
  //
  //   // Copy JS Cookie
  //   gulp.src([
  //     './node_modules/js-cookie/src/js.cookie.js'
  //   ])
  //     .pipe($.uglify())
  //     .pipe(gulp.dest('./assets/js/'))
  //
  // );
});

// Image min
gulp.task('imagemin', function () {
  return gulp.src('./assets/img/**/*')
    .pipe($.imagemin({
      progressive: true,
      svgoPlugins: [{removeViewBox: false}],
      use        : [pngquant()]
    }))
    .pipe(gulp.dest('./dist/img'));
});


// watch
gulp.task('watch', function () {
  // Make SASS
  gulp.watch('./assets/scss/**/*.scss', ['sass']);
  // JS
  gulp.watch(['./assets/js/**/*.js'], ['js', 'jshint']);
  // Minify Image
  gulp.watch('./assets/img/**/*', ['imagemin']);
});


// Build
gulp.task('build', ['copyLib', 'js', 'sass', 'imagemin']);

// Default Tasks
gulp.task('default', ['watch']);
