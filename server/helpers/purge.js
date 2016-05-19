var path = require("path");
var request = require("request");
var env = require("./env").env;

var purge = {
  all: function () {
    if (env == "development") {
      return;
    }
    request({
      method: "PURGE",
      uri: "https://osmc.tv"
    }, function (error, response, body) {
      if (error) {
        console.log("PURGE REQUEST ERROR");
        console.log(error);
      }
    });
  }
};

module.exports = purge;