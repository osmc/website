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

var hosts = {
  host: configGhost[env].url,
  ghost: "http://localhost:2368",
  cdn: "https://blog-cdn.osmc.tv",
  get store() {
    if (env == "development") {
      return "http://local.store.osmc.tv/";
    } else {
      return "https://store.osmc.tv/";
    }
  }
};

module.exports = {
  env: env,
  hosts: hosts
};
