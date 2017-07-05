'use strict';

/**
 * Modules
 */
var del = require('del');
var gulp = require('gulp');
var newer = require('gulp-newer');
var uglify = require('gulp-uglify');
var less = require('gulp-less');
var rename = require('gulp-rename');
var concat = require('gulp-concat');
var addsrc = require('gulp-add-src');

const lessOptsCompress = {
    compress: true,
    relativeUrls: true,
    strictMath: true,
    strictUnits: true
};

const bowerPath = './bower_components/';
const librariesPath = './libraries/';
const stylesPath = './styles/';
const scriptsPath = './scripts/';
const fontsPath = './fonts/';

/**
 *******************************************************
 * Libraries: selection files
 *******************************************************
 */

gulp.task('jquery', function () {
    return gulp.src(
        bowerPath + 'jquery/dist/jquery.min.js'
    )
        .pipe(newer(librariesPath + 'jquery/'))
        .pipe(gulp.dest(librariesPath + 'jquery/'));
});

gulp.task('jquery-ui', function () {
    return gulp.src(
        [
            bowerPath + 'jquery-ui/*.min.js',
            bowerPath + 'jquery-ui/themes/base/*.min.css'
        ],
        {
            base: bowerPath + 'jquery-ui/'
        }
    )
        .pipe(newer(librariesPath + 'jquery-ui/'))
        .pipe(gulp.dest(librariesPath + 'jquery-ui/'));
});

gulp.task('jquery-validation', function () {
    return gulp.src(
        bowerPath + 'jquery-validation/dist/jquery.validate.min.js'
    )
        .pipe(newer(librariesPath + 'jquery-validation/'))
        .pipe(gulp.dest(librariesPath + 'jquery-validation/'));
});

gulp.task('fancybox', function () {
    return gulp.src(
        [
            bowerPath + 'fancybox/dist/jquery.fancybox.min.js',
            bowerPath + 'fancybox/dist/jquery.fancybox.min.css'
        ], {
            base: bowerPath + 'fancybox/source/'
        }
    )
        .pipe(newer(librariesPath + 'fancybox/'))
        .pipe(gulp.dest(librariesPath + 'fancybox/'));
});

gulp.task('bootstrap', function () {
    return gulp.src(
        [
            bowerPath + 'bootstrap/dist/{css,js}/*.min.*',
            bowerPath + 'bootstrap/dist/fonts/*',
            '!**/*.map',
            '!**/npm.js'
        ],
        {
            base: bowerPath + 'bootstrap/dist/'
        }
    )
        .pipe(newer(librariesPath + 'bootstrap/'))
        .pipe(gulp.dest(librariesPath + 'bootstrap/'));
});

gulp.task('bootstrap-datetimepicker', function () {
    return gulp.src(
        bowerPath + 'eonasdan-bootstrap-datetimepicker/build/**/*.min.*',
        {
            base: bowerPath + 'eonasdan-bootstrap-datetimepicker/build/'
        }
    )
        .pipe(newer(librariesPath + 'bootstrap-datetimepicker/'))
        .pipe(gulp.dest(librariesPath + 'bootstrap-datetimepicker/'));
});

gulp.task('moment', function () {
    return gulp.src(
        bowerPath + 'moment/min/moment.min.js',
        {
            base: bowerPath + 'moment/min/'
        }
    )
        .pipe(uglify())
        .pipe(newer(librariesPath + 'moment/'))
        .pipe(gulp.dest(librariesPath + 'moment/'));
});

gulp.task('moment.ru', function () {
    return gulp.src(
        bowerPath + 'moment/locale/ru.js',
        {
            base: bowerPath + 'moment/locale/'
        }
    )
        .pipe(uglify())
        .pipe(rename('moment.ru.min.js'))
        .pipe(newer(librariesPath + 'moment/'))
        .pipe(gulp.dest(librariesPath + 'moment/'));
});

gulp.task('font-awesome', function () {
    return gulp.src([
            bowerPath + 'font-awesome/css/font-awesome.min.css',
            bowerPath + 'font-awesome/fonts/*'
        ],
        {
            base: bowerPath + 'font-awesome/'
        }
    )
        .pipe(newer(librariesPath + 'font-awesome/'))
        .pipe(gulp.dest(librariesPath + 'font-awesome/'));
});

gulp.task('metisMenu', function () {
    return gulp.src(
        bowerPath + 'metisMenu/dist/*.min.*'
    )
        .pipe(newer(librariesPath + 'metisMenu/'))
        .pipe(gulp.dest(librariesPath + 'metisMenu/'));
});

gulp.task('ace', function () {
    return gulp.src(
        bowerPath + 'ace-builds/src-min/ace.js'
    )
        .pipe(newer(librariesPath + 'ace-builds/'))
        .pipe(gulp.dest(librariesPath + 'ace-builds/'));
});

gulp.task('ace', function () {
    return gulp.src(
        bowerPath + 'ace-builds/src-min/ace.js'
    )
        .pipe(newer(librariesPath + 'ace/'))
        .pipe(gulp.dest(librariesPath + 'ace/'));
});

/**
 * Libraries: copying
 */
gulp.task('copy', gulp.parallel(
    'jquery',
    'jquery-ui',
    'jquery-validation',
    'fancybox',
    'bootstrap',
    'ace',
    'moment',
    'moment.ru',
    'bootstrap-datetimepicker',
    'font-awesome',
    'metisMenu'
));


/**
 * Styles
 */
gulp.task('styles', function (done) {
    gulp.src([
        librariesPath + 'jquery-ui/themes/base/jquery-ui.min.css',
        librariesPath + 'bootstrap/css/bootstrap.min.css',
        librariesPath + 'metisMenu/metisMenu.min.css',
        librariesPath + 'sb-admin-2/css/sb-admin-2.css',
        librariesPath + 'font-awesome/css/font-awesome.min.css',
        stylesPath + 'admin.css',
        librariesPath + 'fancybox/jquery.fancybox.css',
        librariesPath + 'bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css'
    ])
        .pipe(concat('skif-common.css'))
        //.pipe(less(lessOptsCompress))
        .pipe(gulp.dest(stylesPath));
    done();
});

/**
 * Scripts
 */

gulp.task('scripts', function (done) {
    gulp.src([
        librariesPath + 'jquery/jquery.min.js',
        librariesPath + 'jquery-ui/jquery-ui.min.js',
        librariesPath + 'bootstrap/js/bootstrap.min.js',
        librariesPath + 'jquery-validation/jquery.validate.min.js',
        librariesPath + 'fancybox/jquery.fancybox.pack.js',
        librariesPath + 'metisMenu/metisMenu.min.js',
        librariesPath + 'ace/ace.js',
        librariesPath + 'moment/moment.min.js',
        librariesPath + 'moment/moment.ru.min.js',
        librariesPath + 'bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'
    ])
        .pipe(concat('skif-common.js'))
        .pipe(gulp.dest(scriptsPath));
    done();
});


/**
 * Fonts
 */
gulp.task('fonts', function (done) {
    gulp.src([
        bowerPath + 'bootstrap/dist/fonts/*'
    ])
        .pipe(newer(fontsPath))
        .pipe(gulp.dest(fontsPath));
    done();
});


gulp.task('default', gulp.series(
    'copy',
    'styles',
    'scripts',
    'fonts',
    function (done) {
        done();
    }
));