var gulp = require('gulp'),
    sass = require('gulp-ruby-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    concat = require('gulp-concat'),
    notify = require("gulp-notify"),
    bower = require('gulp-bower'),
    rename = require('gulp-rename'),
    uglify = require('gulp-uglify'),

    fs = require('fs'),
    path = require('path'),
    merge = require('merge-stream');

function getFolders(dir) {
    return fs.readdirSync(dir)
        .filter(function (file) {
            return fs.statSync(path.join(dir, file)).isDirectory();
        });
}

gulp.task('bower', function () {
    return bower()
        .pipe(gulp.dest('./bower_components'));
});

gulp.task('icons', function () {
    return gulp.src('./bower_components/fontawesome/fonts/**.*')
        .pipe(gulp.dest('../web/fonts'));
});

gulp.task('images', function () {
    return gulp.src('./images/**.*')
        .pipe(gulp.dest('../web/images'));
});

gulp.task('css', function () {
    return sass('./sass/app.scss', {
        style: 'compressed',
        compass: true,
        loadPath: [
            './sass',
            './bower_components/fontawesome/scss',
            './bower_components/normalize.scss'
        ]
    })
        .on("error", notify.onError(function (error) {
            return "Error: " + error.message;
        }))
        .pipe(autoprefixer())
        .pipe(gulp.dest('../web/css'));
})
;

gulp.task('js', function () {
    var folders = getFolders('js');

    var tasks = folders.map(function (folder) {
        return gulp.src(path.join('js', folder, '/**/*.js'))
            .pipe(concat(folder + '.js'))
            .pipe(gulp.dest('../web/js'))
            .pipe(uglify())
            .pipe(rename(folder + '.min.js'))
            .pipe(gulp.dest('../web/js'))
    });

    return merge(tasks);
});

gulp.task('watch', function () {
    gulp.watch('./images/**/*', ['images']);
    gulp.watch('./sass/**/*.scss', ['css']);
    gulp.watch('./js/**/*.js', ['js']);
    gulp.watch('./js/head/**/*.js', ['js-head']);
});

gulp.task('default', ['bower', 'icons', 'images', 'css', 'js']);

