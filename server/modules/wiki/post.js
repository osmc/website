var _ = require("lodash");

var json = require("./json");

// if no wiki post is found in the json file, return false for custom render
var post = function (url) {
  var data = json();
  // e.g. /wiki/general/faq
  var split = url.substring(1).split("/");
  var cat = split[1];
  var post = split[2];
  var singleCat = _.find(data.categories, {
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

module.exports = post;
