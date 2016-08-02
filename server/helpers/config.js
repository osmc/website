var path = require("path");

var flag = process.argv[2];
if (flag == "dev") {
  process.env.NODE_ENV = "development";
} else if (flag == "test") {
  process.env.NODE_ENV = "testing";
} else {
  process.env.NODE_ENV = "production";
}
var env = process.env.NODE_ENV;

// host
var configGhost = require(path.join(__dirname, "../../config.js"));
var host = configGhost[env].url;

var hostStore;
if (env == "development") {
  hostStore = "http://shoposmc.dev/";
} else {
  hostStore = "https://store.osmc.tv/";
}

module.exports = {
  env: env,
  host: host,
  hostStore: hostStore
}
