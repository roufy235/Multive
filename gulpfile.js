// initialize modules
// Importing specific gulp API functions lets us write them below as series() instead of gulp.series()
const { src, dest, watch, series, parallel } = require('gulp');
// Importing all the Gulp-related packages we want to use
const sourcemaps = require('gulp-sourcemaps');
const sass = require('gulp-sass');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const replace = require('gulp-replace');
const imagemin = require("gulp-imagemin");
const cache = require("gulp-cache");

// file path variables
const allCompiledJsFilename = '_9550808079.js';
const files = {
    scssPath: 'assets/app/scss/**/*.scss',
    jsPath: 'assets/app/js/**/*.js',
    distPath: 'dist',
    imagePath: 'assets/images/**/*.+(png|jpg|gif|svg|jpeg)',
}


// Task to minify images
function imagesTask() {
    return src(files.imagePath)
        .pipe(cache(imagemin()))
        .pipe(dest('dist/images'));
}

function scssTask(){
    return src(files.scssPath)
        .pipe(sourcemaps.init()) // initialize sourcemaps first
        .pipe(sass()) // compile SCSS to CSS
        .pipe(postcss([ autoprefixer(), cssnano ])) // PostCSS plugins
        .pipe(sourcemaps.write('.')) // write sourcemaps file in current directory
        .pipe(dest(files.distPath)
        ); // put final CSS in dist folder
}

// JS task: concatenates and uglifies JS files to script.js
function jsTask(){
    return src([
        files.jsPath
        //,'!' + 'includes/js/jquery.min.js', // to exclude any specific files
    ])
        .pipe(concat(allCompiledJsFilename))
        .pipe(uglify())
        .pipe(dest(files.distPath)
        );
}

// cache busting task
function cacheBustTask(){
    const cbString = new Date().getTime().toString().trim();
    return src(['.env.example', '.env'])
        .pipe(replace( /JAVASCRIPT_VERSION_CONTROL=\d+/g, 'JAVASCRIPT_VERSION_CONTROL=' + cbString))
        .pipe(dest('.'));
}
// Watch task
function watchTask(){
    watch([files.scssPath, files.jsPath],
        {interval: 1000, usePolling: true}, //Makes docker work
        series(
            parallel(scssTask, jsTask, imagesTask),
            cacheBustTask
        )
    );
}

// Default task
exports.default = series(
    parallel(scssTask, jsTask, imagesTask),
    cacheBustTask,
    watchTask
);
