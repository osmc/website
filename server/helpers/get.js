var request = require("request");

var get = function(url) {
  return new Promise(function (resolve, reject) {
    request(url, function (error, response, body) {
      if (!error && response.statusCode == 200) {
        return resolve(body);
      } else {
        return reject(error);
      }
    });
  });
};

module.exports = get;
