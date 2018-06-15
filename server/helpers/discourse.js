var path = require("path");
var request = require("request");

var auth = require(path.join(__dirname, "../../content/data/keys")).discourse;

var get = function(url) {
  var newUrl = url + "?api_key=" + auth.key + "&api_username=" + auth.user;
    
  return new Promise(function (resolve, reject) {
    request(newUrl, function (error, response, body) {
      if (!error && response.statusCode == 200) {
	try {
            return resolve(JSON.parse(body));
	}
	catch (e) {
	    return reject(404);
	}
      } else {
        return reject(error);
      }
    });
  });
};

module.exports = {
  get: get
};
