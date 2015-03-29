var gulp = require('gulp'),
    sass = require('gulp-ruby-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    concat = require('gulp-concat'),
    notify = require("gulp-notify"),
    bower = require('gulp-bower'),
    rename = require('gulp-rename'),
    uglify = require('gulp-uglify'),

    path = require('path'),
    merge = require('merge-stream');

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
    var folders = {
        'head': ['modernizr*.js', 'jquery-*.js'],
        'app': []
    };

    var tasks = Object.keys(folders).map(function (value) {
        var folder = value;

        var paths = [];

        folders[value].forEach(function(file) {
            paths.push(path.join('js', folder, file));
        });

        paths.push(path.join('js', folder, '/**/*.js'));
        
        return gulp.src(paths)
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

