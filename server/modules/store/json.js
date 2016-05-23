var path = require("path");
var _ = require("lodash");
var chokidar = require("chokidar");

var readFile = require("../../helpers/readFile");

var file = path.join(__dirname, "../../static/store/products.json");
var json;

var load = function() {
  readFile("store-products", file).then(function(res) {
    res = JSON.parse(res);
    res.products = _.sortBy(res.products, "menu_order");
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
