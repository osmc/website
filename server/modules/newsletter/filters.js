var cheerio = require("cheerio");
var minify = require("html-minifier").minify;
var juice = require("juice");

var post = function(html) {
  $ = cheerio.load(html);

  $("img").map(function() {
    var img = $(this);

    // relative urls
    if (img.attr("src").substring(0,1) === "/") {
      var src = "https://discourse.osmc.tv" + img.attr("src");
      img.attr("src", src);
    }

    // emoji
    if(img.hasClass("emoji")) {
      img.css("width", "16px");
    }
  });

  // image meta element
  $(".meta").remove();

  // image lightbox wrapper
  $(".lightbox-wrapper").map(function() {
    var a = $(this).find("a").clone();
    $(this).after(a);
    $(this).remove();
  });

  return $.html();
};

var email = function(obj) {
  var html = obj.template;

  // content
  html = html.split("[title]").join(obj.title);
  html = html.replace("[body]", obj.body);

  // styles
  $ = cheerio.load(html);
  $("link").attr("rel", "stylesheet").remove();
  html = $.html();
  var style = "<style>" + obj.css + "</style>";
  html = html.replace("<!-- <style> -->", style);

  // inline css
  html = juice(html);

  // minify
  html = minify(html, {
    collapseWhitespace: true,
    minifyCSS: true
  });

  return html;
};

module.exports = {
  post: post,
  email: email
};
