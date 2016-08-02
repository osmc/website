var ghost = require("ghost");
var path = require("path");
var mkdirp = require("mkdirp");
var purge = require("./server/helpers/purge").all;
var ghostPath = path.join(__dirname, "node_modules/ghost/");
var express = require(ghostPath + "node_modules/express");
app = express();

options = {
	config: path.join(__dirname, "config.js")
};
ghost(options).then(function(ghostServer) {
	ghostServer.start();
	purge(); // varnish
});

// create static directories
mkdirp.sync(path.join(__dirname, "/server/static"));
mkdirp.sync(path.join(__dirname, "/content/themes/osmc/assets/ext"));

require("./server/custom")();
require("./server/modules/images");
require("./server/modules/wiki");
require("./server/modules/autolinks");
require("./server/modules/store");
require("./server/modules/newsletter");
require("./server/routes");

var port = 2369;
var env = require("./server/helpers/config").env;
if (env == "testing") {
	port = 80;
}

app.listen(port);
