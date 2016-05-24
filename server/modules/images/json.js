var path = require("path");
var chokidar = require("chokidar");

var readFile = require("../../helpers/readFile");

var file = path.join(__dirname, "../../static/images.json");
var json;

var load = function() {
  readFile("images", file).then(function(res) {
    res = JSON.parse(res);
    json = res;
  });
}

var watcher = chokidar.watch(file);
watcher.on("change", function () {
  load();
}).on("add", function () {
  load();
});

var output = function() {
  return json;
};

module.exports = output;
