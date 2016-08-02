var path = require("path");
var _ = require("lodash");
var cheerio = require("cheerio");
var ghostPath = path.join(__dirname, "../../../node_modules/ghost/");
var hbs = require(ghostPath + "node_modules/express-hbs");

var hostStore = require("../../helpers/config").hostStore;
var json = require("./json");

var helpers = function () {
  hbs.registerHelper("store-index", function (res) {
    var data = json();
    if (!data) {
      return;
    }

    var store = res.data.root.store = {};
    store.products = data.products;
    store.categories = [];

    var categories = _.uniq(_.flatten(_.map(data.products, "categories")));

    categories.forEach(function(cat, i) {
      var amount = 0;

      data.products.forEach(function(item, j) {
        var inCat = _.includes(item.categories, cat);
        if (inCat) {
          amount ++;
        }
      });

      var obj = {"title": cat, "amount": amount};
      store.categories.push(obj);
    });
  });

  hbs.registerHelper("store-product-url", function (res) {
    // https://store.osmc.tv/product/raspberrypi/
    var url = "/store/" + res.split("/")[3] + "/" + res.split("/")[4];
    if (url == "/store/product/vero") {
      url = "/vero";
    }
    return url;
  });

  hbs.registerHelper("store-buy-url", function(res) {
    var product = res.data.root.store.product;
    var url = hostStore + "cart?add-to-cart=" + product.id;
    return url;
  });

  hbs.registerHelper("store-cart", function() {
    var url = hostStore + "embed_cart/";
    iframe = "<iframe src='" + url + "' frameborder='0' scrolling='no'></iframe>";
    return iframe;
  });

  hbs.registerHelper("store-minicart", function() {
    var url = hostStore + "embed_cart_widget/";
    iframe = "<iframe src='" + url + "' frameborder='0' scrolling='no'></iframe>";
    return iframe;
  });

  hbs.registerHelper("firstParagraph", function(html) {
    $ = cheerio.load(html);
    return $("p").first().text();
  });
};

module.exports = helpers;
