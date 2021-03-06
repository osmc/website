var path = require("path");
var ghostPath = path.join(__dirname, "../../../node_modules/ghost/");
var hbs = require(ghostPath + "node_modules/express-hbs");
var _ = require("lodash");

var json = require("./json");

var helpers = function () {

  hbs.registerHelper("wiki-index", function (res) {
    var data = json();

    if (data) {
      _.set(res, "data.root.wiki.index", data);
    }
  });

  hbs.registerHelper("escape", function (res) {
    return res;
  });

};

module.exports = helpers;
