var path = require("path");
var WooCommerceAPI = require("woocommerce-api");

var auth = require(path.join(__dirname, "../../content/data/keys")).wp;

var wc = new WooCommerceAPI({
  url: "https://store.osmc.tv",
  version: "v3",
  verifySsl: false,
  consumerKey: auth.key,
  consumerSecret: auth.secret
});

module.exports = wc;