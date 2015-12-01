var fs = require("fs");
var path = require("path");

try {
	var token = require(path.join(__dirname, "tokens.js")).slackin;
} catch (err) {
	var token;
};

var options = {
	token: token,
	interval: 15000,
	org: "osmc-chat",
	silent: true
};

if (token) {	
	require("slackin")(options).listen(2370);
}