var path = require("path");
var ghostPath = path.join(__dirname, "../../../node_modules/ghost/");
var hbs = require(ghostPath + "node_modules/express-hbs");
var _ = require("lodash");

var json = require("./json");

var helpers = function () {

  hbs.registerHelper("wiki-index", function (res) {
    var content = json();

    if (content) {
      _.set(res, "data.root.wiki.index", content);
    }
  });

  hbs.registerHelper("escape", function (res) {
    return res;
  });

};

module.exports = helpers;
