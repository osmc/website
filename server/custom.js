var fs = require("fs");
var path = require("path");
var ghostPath = path.join(__dirname, "../node_modules/ghost/");
var hbs = require(ghostPath + "node_modules/express-hbs");
var _ = require(ghostPath + "node_modules/lodash");

var host = require("./helpers/env").host;

var helpers = function () {

  hbs.registerHelper("custom", function (option, res) {
    var relativeUrl = _.get(res, "data.root.relativeUrl");
    var blogTitle = _.get(res, "data.blog.title");
    var titleDefault = _.get(res, "data.root.post.title");
    var urlDefault = _.get(res, "data.root.post.url");
    var page = _.get(res, "data.root.pagination.page");
    var tag = _.get(res, "data.root.tag");
    var wikiPost = _.get(res, "data.root.wikiPost");
    var notFound = _.get(res, "data.root.code");
        
    var url = relativeUrl;
    var titleCustom;
    var urlCustom;
    
    if (wikiPost) {
      var singlePost = res.data.root.wikiPost;
      urlCustom = singlePost.url;
      _.set(res, "data.root.relativeUrl", urlCustom);
      _.set(res, "data.blog.title", "OSMC");
      _.set(res, "data.blog.url", host);
      titleCustom = singlePost.title + " - " + singlePost.category + " - " + blogTitle;
      
    } else if (url) {

      if (url === "/home/") {
        // Home page
        titleCustom = blogTitle;
        urlCustom = "/";

      } else if (url === "/" || url.substring(0, 6) === "/page/") {
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

module.exports = helpers;