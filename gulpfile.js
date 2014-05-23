var gulp = require('gulp');
var clean = require('gulp-clean');
var concat = require('gulp-concat');
var gulpBowerFiles = require('gulp-bower-files');

gulp.task('clean', function () {
    return gulp.src('webroot/_/build', {read: false})
        .pipe(clean());
});

gulp.task("bower-files", function(){
    gulpBowerFiles()
        .pipe(concat('vendor.js'))
        .pipe(gulp.dest("webroot/_/build"));
});
