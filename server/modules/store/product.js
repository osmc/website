var _ = require("lodash");

var json = require("./json");

var product = function (url) {
  var data = json();
  if (!data) {
    return false;
  }
  // /store/product/raspberry-pi-3-starter-kit
  var slug = url.substring(1).split("/")[2];
  var wcUrl = "https://my.osmc.tv/product/" + slug + "/";
  var product = _.find(data.products, {
    "permalink": wcUrl
  });

  if (product) {
    product.url = url;
    return product;
  } else {
    return false;
  }
};

module.exports = product;
