var fs = require("fs");
var path = require("path");
var cheerio = require("cheerio");

var readFile = require("../../helpers/readFile.js");
var get = require("../../helpers/discourse").get;
var save = require("./save");

var code = function() {
  return new Promise(function (resolve, reject) {
    get("https://discourse.osmc.tv/t/auto-links/7458.json").then(function(json) {
      var html = json.post_stream.posts[0].cooked;
      $ = cheerio.load(html);
      return resolve($("code").text());
    });
  });
};

var script = readFile("discourse script", path.join(__dirname, "../../../src/assets/js/discourse.min.js"));

var autolinks = function() {
  Promise.all([code(), script]).then(function(res) {
    save(res[0], res[1]);
  });
};

autolinks();

// Schedule. Only in production
var env = require("../../helpers/env").env;
if (env == "production") {
  var minutes = 5;
  interval = minutes * 60 * 1000;
  setInterval(function () {
    autolinks();
  }, interval);
}