var cheerio = require("cheerio");
var get = require("../../helpers/discourse").get;

var filter = function(html) {
  $ = cheerio.load(html, {
    decodeEntities: false
  });

  $("img").map(function() {
    var img = $(this);

    // relative urls
    if (img.attr("src").substring(0,1) === "/") {
      var src = "https:" + img.attr("src");
      img.attr("src", src);
    }

    // width/height attributes
    img.attr("height", null);

    var maxwidth = 544;
    var width = img.attr("width");
    if (width > maxwidth) {
      img.attr("width", maxwidth);
    } else {
      img.attr("width", width);
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

var post = function(id) {
  return new Promise(function (resolve, reject) {
    var url = "https://discourse.osmc.tv/t/" + id + ".json";

    get(url).then(function(res) {
      var title = res.title;
      var body = res.post_stream.posts[0].cooked;
      body = filter(body);

      var obj = {
        title: title,
        body: body
      };

      return resolve(obj);
    });
  });
};

module.exports = post;
