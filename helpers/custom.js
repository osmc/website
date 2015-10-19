var fs = require("fs");
var path = require("path");
var ghost = path.join(__dirname, "../node_modules/ghost/");

var hbs = require(ghost + "node_modules/express-hbs");
var _ = require(ghost + "node_modules/lodash");

function url(res) {
  var relativeUrl = res.data.root.relativeUrl.substring(1);
  if ( relativeUrl.substring(0, 4) !== "page" ) {
    return relativeUrl.substring(0, relativeUrl.indexOf('/'));
  } else {
    return "page";
  }
};

var pages = {
  wiki: "Wiki - OSMC",
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
  } catch(err) {
    // File not found.
    console.log("err");
  }
};

readWiki();
fs.watch(wikiPath, function(event, filename) {
  readWiki();
});

module.exports = function(){  
  
  hbs.registerHelper("wiki", function(option, res, req) {
    var data = res.data.root.relativeUrl;
    var output;
    if ( option == "body" ) {
      console.log("tes");
      console.log(data);
      output = JSON.stringify(data);
    }
    
    return output;

  });
  
  hbs.registerHelper("custom", function(option, res) {
		console.log(res);
    var blog_title = _.get(res, "data.blog.title");
		if (!blog_title) {
			blog_title = "some_title";
		}
    var page = _.get(res, "data.root.pagination.page");
    var title_default = _.get(res, "data.root.post.title");
    var title_custom = pages[url(res)];
				    
    var host = res.data.blog.url;
    var relativeUrl = res.data.root.relativeUrl;
    var url_default = relativeUrl;
    var url_custom = urls[url(res)];
		
    var output;
    if ( option == "title" ) {
      if ( title_custom ) {
        output = title_custom;
      } else if (title_default) {
        output = title_default + " - " + blog_title;
      } else if ( page ) {
        output = "page " + page + " - " + blog_title;
      }
    } else if ( option == "url" ) {
      if ( url_custom ) {
        output = host + url_custom;
      } else {
        output = host + url_default;
      }
    }		
    
    return output;

  });
  
};