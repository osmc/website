var path = require("path");
var _ = require("lodash");
var ghostPath = path.join(__dirname, "../../../node_modules/ghost/");
var hbs = require(ghostPath + "node_modules/express-hbs");

var json = require("./json");

var helpers = function () {
  hbs.registerHelper("images", function (res) {
    var data = json();

    if (data) {
      _.set(res, "data.root.images", data);
    }
  });
};

module.exports = helpers;
