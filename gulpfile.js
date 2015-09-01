var gulp = require('gulp');
var rename = require('gulp-rename');
var compass = require('gulp-compass');
var cssmin = require('gulp-cssmin');
var prefix = require('gulp-autoprefixer');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var browserSync = require('browser-sync');
var rev = require('gulp-rev');
var inject = require('gulp-inject');
var del = require('del');
var reload = browserSync.reload;

var bower = 'bower_components/';
var custom = 'custom_components/';
var dist = 'content/themes/osmc/';

gulp.task('browser-sync', function() {
  browserSync({
    proxy: "http://osmc.dev"
  });
});

gulp.task('re', function () {
  gulp.src(dist + 'header.php')
  .pipe(inject(
    gulp.src(dist + "library/style/style.css", {read: false}), 
    {
      transform: function (filepath) {
        return '<link type="text/css" rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>' + filepath + '">';
      },
      ignorePath: dist,
      addRootSlash: true  
  }
  ))
  .pipe(gulp.dest(dist));
});

// style
gulp.task('style', function () {
  return gulp.src(dist + 'library/style/style.scss')
    .pipe(compass({
      css: dist + 'library/style',
      sass: dist + 'library/style',
      import_path: bower
    }))
    .pipe(prefix())
    .pipe(cssmin())
    .pipe(gulp.dest(dist + "library/style"))
    .pipe(gulp.dest(dist))
    .pipe(reload({stream:true}));
    
});

// minify js
gulp.task('js', function () {
  return gulp.src([
    bower + "jquery-validation/dist/jquery.validate.min.js",
    custom + "clappr/dist/clappr.min.js",
    bower + "chartist/dist/chartist.min.js",
    dist + "library/js/scripts.js"
  ])
  .pipe(uglify())
  .pipe(concat('scripts.min.js'))
  .pipe(gulp.dest(dist + 'library/js'));
});

gulp.task('js-reload', ['js'], function () {
  browserSync.reload();
});
gulp.task('html-reload', function () {
  browserSync.reload();
});

gulp.task("discourse", function() {
  return gulp.src([
    bower + "jquery.dfp/jquery.dfp.min.js",
    "discourse/scripts.js"
    ])
  .pipe(uglify())
  .pipe(concat("scripts.min.js"))
  .pipe(gulp.dest("discourse"));
});

gulp.task('default', ['browser-sync', 're', 'js', 'style', 'discourse'], function () {
  gulp.watch(dist + "**/*.php", ["html-reload"]);
  gulp.watch(dist + "library/style/**/*.scss", ['style']);
  gulp.watch(dist + "library/js/scripts.js", ['js-reload']);
  gulp.watch("discourse/script.js", ['discourse']);
});


gulp.task('clean', ['style'], function (cb) {
  del([
    dist + "library/style/css/**"
  ], cb);
});

gulp.task('revision', ["clean"], function () {
  return gulp.src(dist + 'library/style/style.css')
  .pipe(rev())
  .pipe(gulp.dest(dist + 'library/style/css'));
});

gulp.task('injection', ['revision'], function () {
  gulp.src(dist + 'header.php')
  .pipe(inject(
    gulp.src(dist + "library/style/css/style-*.css", {read: false}), 
    {
      transform: function (filepath) {
        return '<link type="text/css" rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>' + filepath + '">';
      },
      ignorePath: dist,
      addRootSlash: true  
  }
  ))
  .pipe(gulp.dest(dist));
});

gulp.task("build", ["injection", 'browser-sync', 'js', "discourse"], function () {
  gulp.watch(dist + "library/style/**/*.scss", ['injection']);
  gulp.watch("discourse/script.js", ['discourse']);
});

