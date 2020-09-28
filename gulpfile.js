var gulp = require( 'gulp' ),
	fs = require( 'fs' ),
	$ = require( 'gulp-load-plugins' )(),
	pngquant = require( 'imagemin-pngquant' );


// Sass tasks
gulp.task( 'sass', function () {
	return gulp.src( [ './assets/scss/**/*.scss' ] )
		.pipe( $.plumber( {
			errorHandler: $.notify.onError( '<%= error.message %>' )
		} ) )
		.pipe( $.sourcemaps.init( { loadMaps: true } ) )
		.pipe( $.sassGlob() )
		.pipe( $.sass( {
			errLogToConsole: true,
			outputStyle: 'compressed',
			includePaths: [
				'./assets/scss'
			]
		} ) )
		.pipe( $.autoprefixer() )
		.pipe( $.sourcemaps.write( './map' ) )
		.pipe( gulp.dest( './dist/css' ) );
} );


// Minify All
gulp.task( 'js', function () {
	return gulp.src( [ './assets/js/**/*.js' ] )
		.pipe( $.sourcemaps.init( {
			loadMaps: true
		} ) )
		.pipe( $.plumber( {
			errorHandler: $.notify.onError( '<%= error.message %>' )
		} ) )
		.pipe( $.uglify() )
		.pipe( $.sourcemaps.write( './map' ) )
		.pipe( gulp.dest( './dist/js/' ) );
} );


// JS Hint
gulp.task( 'jshint', function () {
	return gulp.src( [ './assets/js/**/*.js' ] )
		.pipe( $.jshint( './assets/.jshintrc' ) )
		.pipe( $.jshint.reporter( 'jshint-stylish' ) );
} );

// Image min
gulp.task( 'imagemin', function () {
	return gulp.src( './assets/img/**/*' )
		.pipe( $.imagemin( {
			progressive: true,
			svgoPlugins: [ { removeViewBox: false } ],
			use: [ pngquant() ]
		} ) )
		.pipe( gulp.dest( './dist/img' ) );
} );


// watch
gulp.task( 'watch', function () {
	// Make SASS
	gulp.watch( './assets/scss/**/*.scss', gulp.task( 'sass' ) );
	// JS
	gulp.watch( [ './assets/js/**/*.js' ], gulp.series( 'js', 'jshint' ) );
	// Minify Image
	gulp.watch( './assets/img/**/*', gulp.task( 'imagemin' ) );
} );

// Build
gulp.task( 'build', gulp.parallel( 'js', 'sass', 'imagemin' ) );

// Default Tasks
gulp.task( 'default', gulp.task( 'watch' ) );
