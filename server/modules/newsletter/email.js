var cheerio = require("cheerio");
var minify = require("html-minifier").minify;
var juice = require("juice");

var filter = function(obj) {
  var html = obj.template;

  // content
  html = html.split("[title]").join(obj.title);
  html = html.replace("[body]", obj.body);

  // styles
  $ = cheerio.load(html, {
    decodeEntities: false
  });
  $("link").attr("rel", "stylesheet").remove();
  html = $.html();
  var style = "<style>" + obj.css + "</style>";
  html = html.replace("<!-- <style> -->", style);

  // inline css
  html = juice(html, {
    preserveImportant: true,
    removeStyleTags: false
  });

  // minify
  html = minify(html, {
    collapseWhitespace: true,
    minifyCSS: true
  });

  return html;
};

var email = function(obj) {
  return new Promise(function (resolve, reject) {
    html = filter(obj);
    return resolve(html);
  });
};

module.exports = email;
