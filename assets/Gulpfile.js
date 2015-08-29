var gulp = require('gulp'),
    sass = require('gulp-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    concat = require('gulp-concat'),
    notify = require("gulp-notify"),
    rename = require('gulp-rename'),
    uglify = require('gulp-uglify'),
    path = require('path'),
    merge = require('merge-stream');

gulp.task('icons', function () {
    return gulp.src('./node_modules/font-awesome/fonts/*')
        .pipe(gulp.dest('../web/fonts'));
});

gulp.task('images', function () {
    return gulp.src('./images/*')
        .pipe(gulp.dest('../web/images'));
});

gulp.task('css', function () {
    return gulp.src(['./sass/app.scss', './sass/pdf.scss'])
      .pipe(sass({
          compass: true,
          includePaths: [
              './sass',
              './node_modules/font-awesome/scss',
              './node_modules/normalize.scss'
          ]
      }).on('error', sass.logError))
      .pipe(autoprefixer())
      .pipe(gulp.dest('../web/css'));
});

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

gulp.task('js:vendors', function() {
    return processJsFiles([
        './node_modules/jquery/dist/jquery.js',
        './node_modules/socket.io-client/socket.io.js',
        './node_modules/mithril/mithril.js'
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

gulp.task('watch', ['images', 'icons', 'css', 'js'], function () {
    gulp.watch('./images/**/*', ['images']);
    gulp.watch('./sass/**/*.scss', ['css']);
    gulp.watch('./js/**/*.js', ['js:app']);
});

gulp.task('default', ['images', 'icons', 'css', 'js']);
