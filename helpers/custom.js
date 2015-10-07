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

module.exports = function(){  
  
  hbs.registerHelper("wiki", function(res) {
    var relativeUrl = res.data.root.relativeUrl;
        
    if ( relativeUrl.substring(1, 5) == "wiki" ) {
      return true;
    } else {
      return true;
    }
  });
  
  hbs.registerHelper("custom", function(option, res) {
    var blog_title = res.data.blog.title;
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