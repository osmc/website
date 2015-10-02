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

var theme = "content/themes/osmc/";
var style = "src/assets/style/";
var js = "src/assets/js/";
var jsDist = theme + "assets/js/";
var css = theme + "assets/css/";
var img = theme + "assets/img/";

// Start server
var cmd = exec("node index.js");
gulp.task("ghost", function() {
  cmd.stdout.on('data', function(data) {
    process.stdout.write(data);
    if (data.indexOf("Ghost is running") !=-1) {
      browserSync.init({
        proxy: "localhost:2369",
				host: "osmc.dev",
				open: "ui"
      });
    }
  });
});

// style
gulp.task("style", function () {
  return gulp.src(style + "style.scss")
    .pipe(compass({
      css: css,
      sass: style,
      image: img,
			import_path: modules
    }))
    .pipe(cssimport())
    .pipe(prefix())
    .pipe(cssmin())
    .pipe(gulp.dest(css))
    .pipe(reload({stream:true}));
});

// style comments
gulp.task("comments", function () {
  return gulp.src(style + "comments.scss")
    .pipe(compass({
      css: css,
      sass: style,
      image: img
    }))
    .pipe(cssimport())
    .pipe(prefix())
    .pipe(cssmin())
    .pipe(gulp.dest(css));
});

// minify js
gulp.task("minify", function () {
  return gulp.src([
    modules + "jquery-validation/dist/jquery.validate.js",
    js + "scripts.js"
  ])
  .pipe(uglify())
  .pipe(concat("minified.js"))
  .pipe(gulp.dest(js));
});

// js
gulp.task("js", ["minify"], function () {
  return gulp.src([
    modules + "chartist/dist/chartist.min.js",
    modules + "clappr/dist/clappr.min.js",
    js + "minified.js"
  ])
  .pipe(concat("main.js"))
  .pipe(gulp.dest(jsDist))
});

gulp.task("clean:js", ["js"], function() {
	return del(js + "minified.js");
});

gulp.task("js-reload", ["clean:js"], function () {
    reload();
});

gulp.task("reload", function () {
    reload();
});

gulp.task("default", ["ghost"], function () {
  gulp.watch([style + "**/*", !style + "comments.scss"], ["style"]);
	gulp.watch(style + "comments.*", ["comments"]);
  gulp.watch(js + "scripts.js", ["js-reload"]);
  gulp.watch(theme + "**/*.hbs", ["reload"]);
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
