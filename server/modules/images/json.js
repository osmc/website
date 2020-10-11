var path = require("path");
var chokidar = require("chokidar");

var readFile = require("../../helpers/readFile");

var file = path.join(__dirname, "../../static/images.json");
var json;

function replacer(key, value) {
   if (typeof value === 'string') {
   newvalue = value.replace("http://download.osmc.tv", "https://ftp.fau.de/osmc/osmc/download/");
   return newvalue;
   }
   return value;
}

var load = function() {
  readFile("images", file).then(function(res) {
    res = JSON.parse(res);
    json = JSON.stringify(res, replacer);
    json = JSON.parse(json);
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
