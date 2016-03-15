var path = require("path");
var fs = require("fs");
var chokidar = require("chokidar");
var ghostPath = path.join(__dirname, "../node_modules/ghost/");
var hbs = require(ghostPath + "node_modules/express-hbs");
var _ = require(ghostPath + "node_modules/lodash");
var purge = require("./helpers/purge");

var loaded = false;
var json;
var filePath = path.join(__dirname, "/static/wiki.json");

var watcher = chokidar.watch(filePath);
watcher.on("change", function () {
  readWiki();
}).on("add", function () {
  readWiki();
});

function readWiki() {
  var readFile = require("./helpers/readFile.js");
  readFile("wiki", filePath).then(function(res) {
    if (JSON.stringify(json) !== JSON.stringify(res) && loaded) {
      purge.all();
    }
    
    json = JSON.parse(res);
    loaded = true;
  });
}

// if no wiki post is found in the json file, return false for custom render
var wikiPost = function (url) {
  // e.g. /wiki/general/faq
  var split = url.substring(1).split("/");
  var cat = split[1];
  var post = split[2];
  var singleCat = _.find(json.categories, {
    "slug": cat
  });
  if (singleCat) {
    var singlePost = _.find(singleCat.posts, {
      "slug": post
    });
  }
  if (singlePost) {
    return singlePost;
  } else {
    return false;
  }
};

var helpers = function () {

  hbs.registerHelper("wiki-index", function (res) {
    if (json) {
      res.data.root.wiki = json;
    }
  });
  
  hbs.registerHelper("escape", function (res) {
    return res;
  });
  
};

module.exports = {
  wikiPost: wikiPost,
  helpers: helpers
};