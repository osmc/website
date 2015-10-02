var gulp = require("gulp");
var del = require("del");
var compass = require("gulp-compass");
var cssmin = require("gulp-cssmin");
var prefix = require("gulp-autoprefixer");
var concat = require("gulp-concat");
var uglify = require("gulp-uglify");
var cssimport = require("gulp-cssimport");
var browserSync = require("browser-sync");
var reload = browserSync.reload;

var path = require("path");
var exec = require('child_process').exec;

var modules = "node_modules/";

var cmd = exec("node server.js");
gulp.task("ghost", function() {
  cmd.stdout.on('data', function(data) {
    process.stdout.write(data);
    
    if (data.indexOf("Ghost is running") !=-1) {
      browserSync.init({
        proxy: "localhost:2369"
      });
    }
  });
});

// style
gulp.task("style", function () {
  return gulp.src("assets/style/style.scss")
    .pipe(compass({
      css: "assets/css",
      sass: "assets/style",
      image: "assets/img"
    }))
    .pipe(cssimport())
    .pipe(prefix())
    .pipe(cssmin())
    .pipe(gulp.dest("assets/css"))
    .pipe(reload({stream:true}));
});

// style comments
gulp.task("comments", function () {
  return gulp.src("assets/style/comments.scss")
    .pipe(compass({
      css: "assets/css",
      sass: "assets/style",
      image: "assets/img"
    }))
    .pipe(cssimport())
    .pipe(prefix())
    .pipe(cssmin())
    .pipe(gulp.dest("assets/css"));
});

// minify js
gulp.task("minify", function () {
  return gulp.src([
    modules + "jquery-validation/dist/jquery.validate.js",
    "assets/js/scripts.js"
  ])
  .pipe(uglify())
  .pipe(concat("minified.js"))
  .pipe(gulp.dest("assets/js"));
});

// js
gulp.task("js", ["minify"], function () {
  return gulp.src([
    modules + "chartist/dist/chartist.min.js",
    modules + "clappr/dist/clappr.min.js",
    "assets/js/minified.js"
  ])
  .pipe(concat("main.js"))
  .pipe(gulp.dest("assets/js"))
});

gulp.task("clean:js", ["js"], function() {
	return del("assets/js/minified.js");
});

gulp.task("js-reload", ["clean:js"], function () {
    reload();
});

gulp.task("reload", function () {
    reload();
});

gulp.task("default", ["ghost"], function () {
  gulp.watch(["assets/style/**/*", "!assets/style/comments.scss"], ["style"]);
	gulp.watch("assets/style/comments.*", ["comments"]);
  gulp.watch("assets/js/scripts.js", ["js-reload"]);
  gulp.watch("**/*.hbs", ["reload"]);
});

process.on('SIGINT', function () {
  killNode();
});
process.on('uncaughtException', function () {
  killNode();
});
function killNode() {
  exec('killall node', function (err, stdout, stderr) {
    process.stdout.write(stdout);
    process.exit(); 
  });
};