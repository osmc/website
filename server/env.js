var path = require("path");

var flag = process.argv[2];
if (flag != "dev") {
  process.env.NODE_ENV = "production";
  var env = "production";
} else {
  var env = "development";
}

// host
var config = require(path.join(__dirname, "../config.js"));
var liveHost = config[env].url;

module.exports = {
  env: env,
  host: liveHost
}