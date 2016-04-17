var ghost = require("ghost");
var path = require("path");
var mkdirp = require("mkdirp");
var ghostPath = path.join(__dirname, "node_modules/ghost/");
var express = require(ghostPath + "node_modules/express");
app = express();

options = {
	config: path.join(__dirname, "config.js")
};
ghost(options).then(function(ghostServer) {
	ghostServer.start();
});

// create static directories
mkdirp.sync(path.join(__dirname, "/server/static"));
mkdirp.sync(path.join(__dirname, "/content/themes/osmc/assets/ext"));

require("./server/custom")();
require("./server/wiki").helpers();
require("./server/images")();
require("./server/discourse");
require("./server/store").helpers();
require("./server/routes");

app.listen(2369);
