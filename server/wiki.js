var path = require("path");
var fs = require("fs");
var chokidar = require("chokidar");
var ghostPath = path.join(__dirname, "../node_modules/ghost/");
var hbs = require(ghostPath + "node_modules/express-hbs");
var _ = require(ghostPath + "node_modules/lodash");
var purge = require("./helpers/purge");

var loaded = false;
var json;
var html;
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
    wikiIndex();
  });
}

function wikiIndex() {
  var categories = json.categories;
  var content = "";
      
  categories.forEach(function (cat) {
    var list = "";

    cat.posts.forEach(function (post) {
      var div = '<li><a data-name="' + post.title + '" href="' + post.url + '">' + post.title + '</a></li>';
      list += div;
    });

    var section = '<section class="wiki-cat ' + cat.slug + '"><header class="wiki-cat-header"><h2 class="wiki-cat-title">' + cat.title + '</h2><span class="wiki-cat-desc">' + cat.description + '</span></header><ul class="wiki-cat-list">' + list + '</ul></section>';
    
    content += section;
  });
  
  html = content;
};

// if no wiki post is found in the json file, return false for custom render
var wikiPostCheck = function (url) {
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

  hbs.registerHelper("wiki-index", function (res, option) {
    if (html) {
      return html;
    }
  });

  hbs.registerHelper("wiki-post", function (option, res) {
    var post = _.get(res, "data.root.wikiPost");
    if (post) {
      return post[option];
    }
  });
};

module.exports = {
  wikiPostCheck: wikiPostCheck,
  helpers: helpers
};