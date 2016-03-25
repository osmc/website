var path = require("path");
var chokidar = require("chokidar");
var ghostPath = path.join(__dirname, "../../node_modules/ghost/");
var cheerio = require(ghostPath + "node_modules/cheerio");
var hbs = require(ghostPath + "node_modules/express-hbs");
var _ = require("lodash");
var env = require("../helpers/env").env;

var purge = require("../helpers/purge");
require("./get").save("products");

var json;
var file = path.join(__dirname, "../static/store/products.json");

var storeHost = "";
if (env == "production") {
  storeHost = "https://store.osmc.tv/";
} else {
  storeHost = "http://shoposmc.dev/";
}

var watcher = chokidar.watch(file);
watcher.on("change", function () {
  read();
}).on("add", function () {
  read();
});

function read() {
  var readFile = require("../helpers/readFile");
  readFile("store-products", file).then(function(res) {
    res = JSON.parse(res);
    res.products = _.sortBy(res.products, "menu_order");
    json = res;
  });
}

var product = function (url) {
  if (!json) {
    return false;
  }
  // /store/product/raspberry-pi-3-starter-kit
  var slug = url.substring(1).split("/")[2];
  var wcUrl = "https://store.osmc.tv/product/" + slug + "/";
  var product = _.find(json.products, {
    "permalink": wcUrl
  });
    
  if (product) {
    product.url = url;
    return product;
  } else {
    return false;
  }
};

var helpers = function () {
  hbs.registerHelper("store-index", function (res) {
    if (!json) {
      return;
    }
    var store = res.data.root.store = {};
    
    store.products = json.products;
    store.categories = [];
    
    var categories = _.uniq(_.flatten(_.map(json.products, "categories")));
    
    categories.forEach(function(cat, i) {
      var amount = 0;
      
      json.products.forEach(function(item, j) {
        var inCat = _.includes(item.categories, cat);
        if (inCat) {
          amount += 1;
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
  
  hbs.registerHelper("if_inArray", function(array, item, options) {
    if (_.includes(array, item)) {
      return options.fn(this);
    }
    return options.inverse(this);
  });
    
  hbs.registerHelper("if_greaterThanClass", function(array, quantity) {
    if (array.length > quantity) {
      return "greater-than-three";
    }
  });
  
  hbs.registerHelper("store-buy-url", function(res) {
    var product = res.data.root.store.product;
    var url = storeHost + "cart?add-to-cart=" + product.id;
    return url;
  });
  
  hbs.registerHelper("store-cart", function() {
    var url = storeHost + "embed_cart/";
    iframe = "<iframe src='" + url + "' frameborder='0' scrolling='no'></iframe>";
    return iframe;
  });
  
  hbs.registerHelper("firstParagraph", function(html) {
    $ = cheerio.load(html);
    return $("p").first().text();    
  });
  
};

module.exports = {
  helpers: helpers,
  product: product
};