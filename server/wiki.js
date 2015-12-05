var path = require("path");
var fs = require("fs");
var chokidar = require("chokidar");
var ghostPath = path.join(__dirname, "../node_modules/ghost/");
var hbs = require(ghostPath + "node_modules/express-hbs");
var _ = require(ghostPath + "node_modules/lodash");
var purge = require("./purge");


var loaded = false;
var wiki;
var wikiPath = path.join(__dirname, "/static/wiki.json");

function readWiki() {
  try {
    var newWiki = JSON.parse(fs.readFileSync(wikiPath));

    // if wiki changes, purge
    if (JSON.stringify(wiki) !== JSON.stringify(newWiki) && loaded) {
      purge.all();
    }
    wiki = newWiki;
    loaded = true;

  } catch (err) {
    console.log(err);
    console.log("wiki.json not found. Run the wiki script");
  }
};

var watcher = chokidar.watch(wikiPath);
watcher.on("change", function () {
  readWiki();
});
watcher.on("add", function () {
  readWiki();
});

// if no wiki post is found in the json file, return false for custom render
var wikiCheck = function (wikiUrl) {
  // e.g. /wiki/general/faq
  var split = wikiUrl.substring(1).split("/");
  var cat = split[1];
  var post = split[2];
  var singleCat = _.find(wiki.categories, {
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

var liveHost = require("./env").host;

var helpers = function () {

  hbs.registerHelper("wiki-index", function (option, res) {
    var categories = wiki.categories;

    var html = "";
    categories.forEach(function (cat) {

      var list = "";

      cat.posts.forEach(function (post) {
        var div = '<li><a data-name="' + post.title + '" href="' + post.url + '">' + post.title + '</a></li>';
        list += div;
      });

      var section = '<section class="wiki-cat ' + cat.slug + '"><header class="wiki-cat-header"><h2 class="wiki-cat-title">' + cat.title + '</h2><span class="wiki-cat-desc">' + cat.description + '</span></header><ul class="wiki-cat-list">' + list + '</ul></section>';

      html += section;
    });

    return html;

  });

  hbs.registerHelper("wiki-post", function (option, res) {
    var singlePost = res.data.root.wikiPost;
    return singlePost[option];
  });
};

module.exports = {
  wikiCheck: wikiCheck,
  helpers: helpers
};