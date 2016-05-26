var post = require("./post");
var src = require("./src");
var email = require("./email");

var route = function(id) {
  return new Promise(function (resolve, reject) {
    Promise.all([post(id), src()]).then(function(res) {
      var obj = {
        title: res[0].title,
        body: res[0].body,
        template: res[1][0],
        css: res[1][1]
      };

      return email(obj);
    }).then(function(res) {
      return resolve(res);
    });
  });
};

module.exports = route;
