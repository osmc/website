var filters = require("./filters");

var email = function(obj) {
  return new Promise(function (resolve, reject) {
    html = filters.email(obj);
    return resolve(html);
  });
};

module.exports = email;
