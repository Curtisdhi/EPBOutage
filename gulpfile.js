var gulp = require('gulp');
var bower = require('gulp-bower');
var runSequence = require('run-sequence');
var autoprefixer = require('gulp-autoprefixer');
var concat = require('gulp-concat');
var concatCss = require('gulp-concat-css');
var cleanCss = require('gulp-clean-css');
var plumber = require('gulp-plumber');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');
var util = require('gulp-util');
var watch = require('gulp-watch');
var del = require('del');
var stripJsComments = require('gulp-strip-comments');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');

var buildDir = 'web/build/';
var deployDir = 'web/dist/';

var plumberOptions = {
    errorHandler: function (err) {
        console.log('\033[31m!!! Attention! Plumber error !!!\033[0m')
        console.log(err);
        this.emit('end');
    }
};

gulp.task('default', function(cb) {
    runSequence('build', 'deploy', 'watch', cb);
});

gulp.task('prod', function(cb) {
    util.env.production = true;
    runSequence('build', 'deploy', cb);
});

// ////////////////////////////////////////////////
// Build  Tasks
// // /////////////////////////////////////////////

gulp.task('build', function(cb) {
    //bower needs to run first
    runSequence(['build:bower', 'build:clean'], ['build:js', 'build:css'], cb);
});

gulp.task('build:bower', function() {
  //install bower components
  return bower();
});

gulp.task('build:clean', function(cb) {
    del([
       deployDir +'build/**',
    ]).then(function() { cb(); });
});

// ////////////////////////////////////////////////
// Build javascript Tasks
// // /////////////////////////////////////////////
gulp.task('build:js', ['build:js:vendors', 'build:js:core']);

gulp.task('build:js:core', function() {
  return gulp.src(['assets/core/js/*.js',
            'assets/core/js/**/*.js'
        ])
        .pipe(plumber(plumberOptions))
        .pipe(concat('core.min.js'))
        .pipe(util.env.production ? stripJsComments() : util.noop())
        .pipe(util.env.production ? uglify() : util.noop())
        .pipe(gulp.dest(buildDir +'js/'));
});

gulp.task('build:js:vendors', function() {
    //don't use min version for dev (helps during debugging)
    return gulp.src(['assets/vendors/jquery/dist/jquery.js',
        'assets/vendors/lodash/dist/lodash.js',
        'assets/vendors/tether/dist/js/tether.js',
        'assets/vendors/bootstrap/dist/js/bootstrap.js',
        'assets/vendors/bootstrap-toggle/js/bootstrap-toggle.js'
        ])
        .pipe(plumber())
        .pipe(concat('vendor.min.js'))
        .pipe(util.env.production ? stripJsComments() : util.noop())
        .pipe(util.env.production ? uglify() : util.noop())
        .pipe(gulp.dest(buildDir + 'js/'));
});

// ////////////////////////////////////////////////
// Build css Tasks
// // /////////////////////////////////////////////
gulp.task('build:css', ['build:css:vendors', 'build:scss:core']);

gulp.task('build:scss:core', function() {
    return gulp.src(['assets/core/scss/**/*.scss', 'assets/core/scss/*.scss'])
        .pipe(plumber(plumberOptions))
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(concatCss("core.min.css"))
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(util.env.production ? cleanCss({keepSpecialComments: 0}) : util.noop())
        .pipe(gulp.dest(buildDir +'css/'));

});

gulp.task('build:css:vendors', function() {
    return gulp.src(['assets/vendors/tether/dist/css/tether.css',
            'assets/vendors/bootstrap/dist/css/bootstrap.css',
            'assets/vendors/font-awesome/css/font-awesome.css',
            'assets/vendors/bootstrap-toggle/css/bootstrap-toggle.css',
            'assets/vendors/animate.css/animate.css',
            'assets/vendors/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css'
        ])
        .pipe(plumber())
        .pipe(concatCss("vendor.min.css", {rebaseUrls: false}))
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(util.env.production ? cleanCss({keepSpecialComments: 0}) : util.noop())
        .pipe(gulp.dest(buildDir +'css/'));
});

// ////////////////////////////////////////////////
// Watch Tasks
// // /////////////////////////////////////////////

gulp.task('watch', function() {
    gulp.watch(['assets/core/js/*.js', 'assets/core/js/**/*.js'], function(cb) {
        runSequence(['build:js:core'], ['deploy:build'], cb);
    });
    gulp.watch(['assets/core/css/*.css', 'assets/core/scss/**/*.scss'], function(cb) {
        runSequence(['build:scss:core'], ['deploy:build'], cb);
    });
});

// ////////////////////////////////////////////////
// Deploy Tasks
// // /////////////////////////////////////////////

gulp.task('deploy', function(cb) {
    runSequence('deploy:clean', ['deploy:build', 'deploy:fonts',
     'deploy:images', 'deploy:misc'], cb);
});

gulp.task('deploy:clean', function(cb) {
    del([
       deployDir +'css/**',
       deployDir +'js/**',
       deployDir +'images/**',
       deployDir +'fonts/**',
       deployDir +'files/**',
       deployDir +'favicon.ico',
    ]).then(function() { cb(); });
});

gulp.task('deploy:build', function() {
    return gulp.src([buildDir +'/**/*'])
        .pipe(gulp.dest(deployDir));
});

gulp.task('deploy:fonts', function() {
    return gulp.src(['assets/vendors/font-awesome/fonts/*',
        'assets/vendors/bootstrap/fonts/*'
        ])
        .pipe(gulp.dest(deployDir +'fonts/'));
});

gulp.task('deploy:images', function() {
    //css images
  return gulp.src(['assets/core/img/*',
            'assets/core/img/**/*'
        ])
        .pipe(gulp.dest(deployDir +'images/'));
});

gulp.task('deploy:misc', function() {

    return gulp.src(['assets/client/files/*',
            'assets/client/files/**/*'])
        .pipe(gulp.dest(deployDir +'files'));
});
