var gulp = require('gulp'), 
    sass = require('gulp-ruby-sass'),
    concat = require('gulp-concat'),
    notify = require("gulp-notify"),
    bower = require('gulp-bower');

gulp.task('bower', function() { 
    return bower()
         .pipe(gulp.dest('./bower_components')) 
});

gulp.task('icons', function() { 
    return gulp.src('./bower_components/fontawesome/fonts/**.*') 
        .pipe(gulp.dest('../web/fonts')); 
});

gulp.task('images', function() { 
    return gulp.src('./images/**.*') 
        .pipe(gulp.dest('../web/images')); 
});

gulp.task('css', function() { 
    return sass('./sass/app.scss', {
        style: 'compressed',
	compass: true,
        loadPath: [
            './sass',
            './bower_components/fontawesome/scss',
            './bower_components/normalize.scss',
         ]
    })
    .on("error", notify.onError(function (error) {
        return "Error: " + error.message;
    }))
    .pipe(gulp.dest('../web/css')); 
});

gulp.task('js', function() {
  return gulp.src(['js/**/*.js', '!js/head/**/*.js'])
	.pipe(concat('app.js'))
	.pipe(gulp.dest('../web/js'));
});
gulp.task('js-head', function() {
    return gulp.src(['js/head/jquery-2.1.3.js', 'js/head/**/*.js'])
        .pipe(concat('head.js'))
        .pipe(gulp.dest('../web/js'));
});

gulp.task('watch', function() {
     gulp.watch('./images/**/*', ['images']); 
     gulp.watch('./sass/**/*.scss', ['css']); 
     gulp.watch('./js/**/*.js', ['js']); 
    gulp.watch('./js/head/**/*.js', ['js-head']); 
});

gulp.task('default', ['bower', 'icons', 'images', 'css', 'js', 'js-head']);

