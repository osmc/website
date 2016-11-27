var path = require("path");
var request = require("request");
var env = require("./config").env;

var purge = {
  all: function () {
    if (env != "production") {
      return;
    }
    request({
      method: "PURGE",
      uri: "http://localhost"
    }, function (error, response, body) {
      if (error) {
        console.log("PURGE REQUEST ERROR");
        console.log(error);
      }
    });
  }
};

module.exports = purge;
