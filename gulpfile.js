var gulp = require('gulp');
var concat = require('gulp-concat');

gulp.task('scripts', function() {
    gulp.src('assets/js/src/*.js')
        .pipe(concat('script.js'))
        .pipe(gulp.dest('public/js/scripts'))

    return gulp.src('src/AdminBundle/Resources/public/js/src/*.js')
        .pipe(concat('admin-script.js'))
        .pipe(gulp.dest('src/AdminBundle/Resources/public/js'))
});