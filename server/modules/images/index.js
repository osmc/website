var fs = require("fs");
var path = require("path");
var ghostPath = path.join(__dirname, "../../../node_modules/ghost/");
var hbs = require(ghostPath + "node_modules/express-hbs");

var host = "http://download.osmc.tv/installers/";
var helpers = require("./helpers")();
var get = require("../../helpers/get");
var filters = require("./filters");

var run = function() {
  get(host).then(function(res) {
    var devices = filters.devices(res, host);
    var files = devices.map(filters.files);

    return Promise.all(files);
  }).then(function(res) {
    var file = path.join(__dirname, "../../static/images.json");
    var json = JSON.stringify(res, null, 2);

    fs.writeFile(file, json, function (err) {
      if (err) {
        console.log("images write error");
        console.log(err);
      }
    });
  });
};

run();

// Schedule. Only in production
var env = require("../../helpers/env").env;
if (env == "production") {
  var minutes = 15;
  interval = minutes * 60 * 1000;
  setInterval(function () {
    run();
  }, interval);
}
