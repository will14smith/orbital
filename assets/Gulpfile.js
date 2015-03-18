var gulp = require('gulp'),     
    sass = require('gulp-ruby-sass'),
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

gulp.task('css', function() { 
    return sass('./sass/app.scss', {
        style: 'compressed',
	compass: true,
        loadPath: [
            './sass',
            './bower_components/fontawesome/scss',
         ]
    })
    .on("error", notify.onError(function (error) {
        return "Error: " + error.message;
    }))
    .pipe(gulp.dest('../web/css')); 
});

gulp.task('watch', function() {
     gulp.watch('./sass/**/*.scss', ['css']); 
});

gulp.task('default', ['bower', 'icons', 'css']);

