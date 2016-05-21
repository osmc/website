var fs = require("fs");
var path = require("path");

var file = path.join(__dirname, "../../../content/themes/osmc/assets/ext/discourse.js");

var save = function(code, script) {
  var js = code + script;
  
  fs.writeFile(file, js, function (err) {
    if (err) {
      console.log("discourse.js write error");
      console.log(err);
    }
  });
};

module.exports = save;