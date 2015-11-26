var fs = require("fs");
var path = require("path");
var ghostPath = path.join(__dirname, "../node_modules/ghost/");
var hbs = require(ghostPath + "node_modules/express-hbs");
var _ = require(ghostPath + "node_modules/lodash");
var mkdirp = require("mkdirp");
var chokidar = require("chokidar");

// create static directory
mkdirp.sync(path.join(__dirname, "/static"), function (err) {});

var wiki;
var wikiPath = path.join(__dirname, "/static/wiki.json");

readWiki();
function readWiki() {
  try {
    wiki = JSON.parse(fs.readFileSync(wikiPath));
  } catch (err) {
    console.log("wiki.json not found");
  }
};

var watcher = chokidar.watch(wikiPath);
watcher.on("change", function() {
  readWiki();
});
watcher.on("add", function() {
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

      var section = '<section class="wiki-cat ' + cat.slug + '"><header class="wiki-cat-header"><h2 class="wiki-cat-title">' + cat.title + '</h2><span class="wiki-cat-desc">' + cat.description + '</span></header><ul class="wiki-cat-list">' + list + '</ul></section>';

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
    var blogTitle = _.get(res, "data.blog.title");
    var titleDefault = _.get(res, "data.root.post.title");
		var urlDefault = _.get(res, "data.root.post.url");
    var page = _.get(res, "data.root.pagination.page");
		var tag = _.get(res, "data.root.tag");
    var host = _.get(res, "data.blog.url");
		var wikiPost = _.get(res, "data.root.wikiPost");
		var notFound = _.get(res, "data.root.code");
		
		var url = relativeUrl;
		var titleCustom;
		var urlCustom;
										
		if (url) {
						
			if (url === "/home/") {
				// Home page
				titleCustom = blogTitle;
				urlCustom = "/";

			} else if (url === "/" || url.substring(0,6) === "/page/") {
				// Blog and pagination
				if (page > 1) {
					titleCustom = "Blog - " + page + " - " + blogTitle;
					urlCustom = "/blog/page/" + page;
				} else {
					titleCustom = "Blog - " + blogTitle;
					urlCustom = "/blog";
				}

			} else if (tag) {
				// Tag page and pagination
				if (page > 1) {
					titleCustom = tag.name + " - " + page + " - " + blogTitle;
				} else {
					titleCustom = tag.name + " - " + blogTitle;
				}
				
			} else if (notFound) {
				titleCustom = "404 - " + blogTitle;
				urlCustom = url;
			}
			
		} else if (wikiPost) {
			//Wiki post
			var singlePost = res.data.root.wikiPost;
      _.set(res, "data.blog.title", "OSMC");
      _.set(res, "data.blog.url", liveHost);
      titleCustom = singlePost.title + " - " + singlePost.category + " - " + blogTitle;
      urlCustom = singlePost.url;
			
		}
		
    var output;
		
    if (option == "title") {
      if (titleCustom) {
        output = titleCustom;
			} else {
				output = titleDefault + " - " + blogTitle;
			}
    }
		
		if (option == "url") {
      if (urlCustom) {
        output = host + urlCustom;
      } else {
				output = host + urlDefault;
      }
    }

    return output;

  });
	
	// disable comments on draft
	hbs.registerHelper("draft", function (res) {
		var status = _.get(res, "data.root.post.status");
		var script = "<script>var draft = true;</script>";
		
		if (status === "draft") {
			return script;
		}
  });

};

var exports = {
  helpers: helpers,
  wikiCheck: wikiCheck
};

module.exports = exports;