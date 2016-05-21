var path = require("path");
var chokidar = require("chokidar");

var readFile = require("../../helpers/readFile");
var purge = require("../../helpers/purge");
var filePath = path.join(__dirname, "../../static/wiki.json");

var loaded = false;
var json;

var load = function() {
  readFile("wiki", filePath).then(function(res) {
    res = JSON.parse(res);
    
    if (JSON.stringify(json) !== JSON.stringify(res) && loaded) {
      purge.all();
    }
    
    json = res;
    loaded = true;
  });
}

var watcher = chokidar.watch(filePath);
watcher.on("change", function () {
  load();
}).on("add", function () {
  load();
});

var output = function() {
  return json;
};

module.exports = output;