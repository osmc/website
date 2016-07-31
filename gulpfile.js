var gulp = require("gulp");
var clean = require("gulp-clean");
var sass = require("gulp-sass");
var prefix = require("gulp-autoprefixer");
var concat = require("gulp-concat");
var include = require("gulp-include");
var uglify = require("gulp-uglify");
var browserSync = require("browser-sync");
var nodemon = require("gulp-nodemon");
var reload = browserSync.reload;

var path = require("path");
var exec = require("child_process").exec;

var modules = "node_modules/";

var theme = "content/themes/osmc/";
var style = "src/assets/style/";
var styleDist = theme + "assets/css/";
var js = "src/assets/js/";
var jsTmp = "src/assets/js/tmp/";
var jsDist = theme + "assets/js/";
var img = theme + "assets/img/";


var onError = function (err) {
  console.log(err);
  this.emit("end");
};

gulp.task("sync", function() {
  browserSync.init({
    proxy: "localhost:2369",
    open: "ui",
    notify: false
  });
});

gulp.task("ghost", function() {
  nodemon({
    script: "index.js",
    args: ["dev"],
    ext: "js",
    ignore: ["src/**/*", "content/**/*", "server/static/**/*", "gulpfile.js"]
  });
});

gulp.task("move-css", function() {
  var files = ["src/assets/style/lib/outdatedbrowser.min.css"];
  gulp.src(files)
  .pipe(gulp.dest(styleDist));
});

gulp.task("move-js", function() {
  var files = ["./node_modules/iframe-resizer/js/iframeResizer.contentWindow.min.js"];
  gulp.src(files)
  .pipe(gulp.dest(jsDist));
});

gulp.task("move", ["move-css", "move-js"]);

// style

var sassOpts = {
  outputStyle: "compressed",
  includePaths: [
    "./node_modules/"
  ]
};

gulp.task("style-main", function () {
  var files = style + "style.scss";
  return gulp.src(files)
    .pipe(sass(sassOpts))
    .on("error", onError)
    .pipe(prefix())
    .pipe(gulp.dest(styleDist))
    .pipe(reload({
      stream: true
    }));
});

// style comments
gulp.task("style-ext", ["style-main"], function () {
  return gulp.src([style + "comments.scss", style + "store.scss"])
    .pipe(sass(sassOpts))
    .on("error", onError)
    .pipe(prefix())
    .pipe(gulp.dest(styleDist));
});

gulp.task("style", ["style-ext"]);

// Discourse
gulp.task("discourse", function() {
  return gulp.src(js + "discourse.js")
  .pipe(include())
  .pipe(uglify())
  .pipe(concat("discourse.min.js"))
  .pipe(gulp.dest(js));
});

// js
gulp.task("js-lib", function () {
  return gulp.src(js + "lib.js")
    .pipe(include())
    .on("error", onError)
    .pipe(concat("lib.js"))
    .pipe(gulp.dest(jsTmp));
});

gulp.task("js-main", ["js-lib"], function () {
  return gulp.src(js + "main.js")
    .pipe(include())
    .pipe(uglify())
    .on("error", onError)
    .pipe(concat("main.js"))
    .pipe(gulp.dest(jsTmp));
});

gulp.task("js", ["js-main"], function () {
  var js = [jsTmp + "lib.js", jsTmp + "main.js"];
  return gulp.src(js)
    .pipe(clean())
    .pipe(concat("main.js"))
    .pipe(gulp.dest(jsDist));
});

gulp.task("js-reload", ["js"], function () {
  reload();
});

gulp.task("reload", function () {
  reload();
});

gulp.task("default", ["ghost", "style", "discourse", "js", "move", "sync"], function () {
  gulp.watch([style + "**/*"], ["style"]);
  gulp.watch(js + "**/*.js", ["js-reload"]);
  gulp.watch(theme + "**/*.hbs", ["reload"]);
  gulp.watch(js + "discourse.js", ["discourse"]);
});

gulp.task("build", ["style", "discourse", "js", "move"]);
