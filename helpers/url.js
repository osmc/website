var path = require("path");
var ghost = path.join(__dirname, "../node_modules/ghost/");

var hbs = require(ghost + "node_modules/express-hbs");
var config = require(ghost + "core/server/config");

var url = function (options) {
	var absolute = options && options.hash.absolute;
	console.log(config.urlFor(this, absolute));
	return "refk";
};

module.exports = function(){  
	hbs.registerHelper("tester", function() {
		return url;
	});
};