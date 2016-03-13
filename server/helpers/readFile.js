var fs = require("fs");

var readFile = function(name, file) {
  return new Promise(function (resolve, reject) {
    fs.readFile(file, "utf8", function(err, data) {
      if(err) {
        return reject(err)
      }
      return resolve(data)
    });
  }).then(function (data) {
    return data;
  })
  .catch(function(err) {
    console.log("readFile error: " + name);
    console.log(err);
  });
}

module.exports = readFile;