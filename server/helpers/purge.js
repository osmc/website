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
      uri: "http://159.253.212.250"
    }, function (error, response, body) {
      if (error) {
        console.log("PURGE REQUEST ERROR");
        console.log(error);
      }
    });
  }
};

module.exports = purge;
