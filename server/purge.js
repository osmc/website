var path = require("path");
var ghostPath = path.join(__dirname, "../node_modules/ghost/");
var request = require(ghostPath + "node_modules/request");

var purge = {
	all: function() {		
		request({
			method: "PURGE",
			uri: "https://osmc.tv"
		}, function(error, response, body){
			if (error) {
				console.log("PURGE REQUEST ERROR");
  			console.log(error);
			}
		});
	}
};

module.exports = purge;