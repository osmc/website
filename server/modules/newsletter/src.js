var get = require("../../helpers/get");

var files = [
  "https://raw.githubusercontent.com/osmc/newsletter/master/dist/index.html",
  "https://raw.githubusercontent.com/osmc/newsletter/master/dist/css/app.css"
];

var src = function() {
  return new Promise(function (resolve, reject) {
    Promise.all(files.map(get)).then(function(res) {
      return resolve(res);
    });
  });
};

module.exports = src;
