var fs = require("fs");
var path = require("path");
var ghostPath = path.join(__dirname, "../node_modules/ghost/");
var hbs = require(ghostPath + "node_modules/express-hbs");
var _ = require(ghostPath + "node_modules/lodash");

function url(res, relativeUrl)  {
  relativeUrl = relativeUrl.substring(1);
  if (relativeUrl.substring(0, 4) !== "page") {
    return relativeUrl.substring(0, relativeUrl.indexOf('/'));
  } else {
    return "page";
  }
};

var pages = {
  home: "OSMC",
  "": "Blog - OSMC"
};
var urls = {
  home: "/",
  "": "/blog"
};

var wiki;
var wikiPath = path.join(__dirname, "/wiki/wiki.json");

function readWiki() {
  try {
    wiki = JSON.parse(fs.readFileSync(wikiPath));
  } catch (err) {
    console.log("wiki.json not found");
  }
};

readWiki();

fs.watch(wikiPath, function (event, filename) {
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

var env = require("./env");
var config = require(path.join(__dirname, "../config.js"));
var liveHost = config[env].url;

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

      var section = '<section class="wiki-cat ' + cat.slug + '"><header class="wiki-cat-header"><h2 class="wiki-cat-title">' + cat.title + '</h2><span class="wiki-cat-desc">' + cat.description + '</span></header><ul class="wiki-cat-list">' + list + '</ul><div class="wiki-cat-gradient"></div><div class="wiki-cat-more"><span class="arrow">&#x27A9;</div></section>';

      html += section;
    });

    return html;

  });

  hbs.registerHelper("wiki-post", function (option, res) {
    var singlePost = res.data.root.wikiPost;
    return singlePost[option];
  });

  hbs.registerHelper("custom", function (option, res) {
    var relativeUrl = _.get(res, "data.root.relativeUrl");
    var blog_title = _.get(res, "data.blog.title");
    var title_default = _.get(res, "data.root.post.title");
    var page = _.get(res, "data.root.pagination.page");
    var host = _.get(res, "data.blog.url");

    if (relativeUrl) {
      var title_custom = pages[url(res, relativeUrl)];
      var url_custom = urls[url(res, relativeUrl)];
      var url_default = relativeUrl;
    }

    var wikiPost = _.get(res, "data.root.wikiPost");
    if (wikiPost) {
      var singlePost = res.data.root.wikiPost;
      _.set(res, "data.blog.title", "OSMC");
      _.set(res, "data.blog.url", liveHost);
      title_custom = singlePost.title + " - " + singlePost.category + " - OSMC";
      url_custom = singlePost.url;
    }

    var output;
    if (option == "title") {
      if (title_custom) {
        output = title_custom;
      } else if (title_default) {
        output = title_default + " - " + blog_title;
      } else if (page) {
        output = "page " + page + " - " + blog_title;
      }
    } else if (option == "url") {
      if (url_custom) {
        output = host + url_custom;
      } else {
        output = host + url_default;
      }
    }

    return output;

  });

};

var exports = {
  helpers: helpers,
  wikiCheck: wikiCheck
};

module.exports = exports;