const gulp = require( 'gulp' );
const $ = require( 'gulp-load-plugins' )();
const pngquant = require( 'imagemin-pngquant' );
const webpack = require( 'webpack-stream' );
const webpackBundle = require( 'webpack' );
const named = require( 'vinyl-named' );
const { dumpSetting } = require( '@kunoichi/grab-deps' );


// グローバルフラグ
let plumber = true;

// エラーフラグを変更
gulp.task( 'noplumber', ( done ) => {
	plumber = false;
	done();
} );

// Package jsx.
gulp.task( 'js', function () {
	let task = gulp.src( './assets/js/**/*.js' );
	if ( plumber ) {
		task = task.pipe( $.plumber( {
			errorHandler: $.notify.onError( '<%= error.message %>' )
		} ) );
	}
	return task
		.pipe( named( ( file ) => {
			return file.relative.replace(/\.[^\.]+$/, '');
		} ) )
		.pipe( webpack( require( './webpack.config.js' ), webpackBundle ) )
		.pipe( gulp.dest( './dist/js' ) );
} );

// Image min
gulp.task( 'build:image', function () {
	return gulp.src( './assets/img/**/*' )
		.pipe( $.imagemin( {
			progressive: true,
			svgoPlugins: [ { removeViewBox: false } ],
			use: [ pngquant() ]
		} ) )
		.pipe( gulp.dest( './dist/img' ) );
} );

// Dump wp-dependencies.json
gulp.task( 'dump', ( done ) => {
	dumpSetting( 'dist' );
	done();
} );

// Build commands.
gulp.task( 'build:js', gulp.series( 'noplumber', 'js' ) );
gulp.task( 'build', gulp.parallel( 'build:js', 'build:image' ) );
