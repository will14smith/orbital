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

gulp.task('icons', ['bower'], function () {
    return gulp.src('./bower_components/fontawesome/fonts/**.*')
        .pipe(gulp.dest('../web/fonts'));
});

gulp.task('images', function () {
    return gulp.src('./images/**.*')
        .pipe(gulp.dest('../web/images'));
});

gulp.task('css', ['bower', 'icons'], function () {
    return sass('./sass/', {
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

function processJsFiles(files, folder) {
    return gulp.src(files)
        .pipe(concat(folder + '.js'))
        .pipe(gulp.dest('../web/js'))
        .pipe(uglify())
        .pipe(rename(folder + '.min.js'))
        .pipe(gulp.dest('../web/js'))
        .on("error", notify.onError(function (error) {
            return "Error: " + error.message;
        }));
}

gulp.task('js:vendors', ['bower'], function() {
    return processJsFiles([
        './bower_components/modernizr/modernizr.js',
        './bower_components/jquery/dist/jquery.js',
        './bower_components/socket.io-client/socket.io.js',
        './bower_components/mithril/mithril.js'
    ], 'vendors');
});

gulp.task('js:app', function () {
    var folders = {
        'head': [],
        'app': [],
        'scoring': []
    };


    var tasks = Object.keys(folders).map(function (value) {
        var folder = value;
        var paths = [];

        folders[value].forEach(function(file) {
            paths.push(path.join('js', folder, file));
        });

        paths.push(path.join('js', folder, '/**/*.js'));

        return processJsFiles(paths, folder);
    });

    return merge(tasks);
});

gulp.task('js', ['js:vendors', 'js:app']);

gulp.task('watch', ['images', 'css', 'js'], function () {
    gulp.watch('./images/**/*', ['images']);
    gulp.watch('./sass/**/*.scss', ['css']);
    gulp.watch('./js/**/*.js', ['js:app']);
});

gulp.task('default', ['images', 'css', 'js']);

