var path = require("path");

var env;

var flag = process.argv[2];
if (flag != "dev") {
  process.env.NODE_ENV = "production";
  env = "production";
} else {
  env = "development";
}

// host
var config = require(path.join(__dirname, "../../config.js"));
var host = config[env].url;

var hostStore = "";
if (env == "production") {
  hostStore = "https://store.osmc.tv/";
} else {
  hostStore = "http://shoposmc.dev/";
}

module.exports = {
  env: env,
  host: host,
  hostStore: hostStore
}
