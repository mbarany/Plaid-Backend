var gulp = require('gulp');
var clean = require('gulp-clean');
var concat = require('gulp-concat');
var gulpBowerFiles = require('gulp-bower-files');

var WEB_ROOT = 'web';
var BUILD_ROOT = WEB_ROOT + '/_/build';

gulp.task('clean', function () {
    return gulp.src(BUILD_ROOT, {read: false})
        .pipe(clean());
});

gulp.task('bower-files', function(){
    gulpBowerFiles()
        .pipe(concat('vendor.js'))
        .pipe(gulp.dest(BUILD_ROOT));
});
