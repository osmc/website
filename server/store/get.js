var fs = require("fs");
var path = require("path");
var mkdirp = require("mkdirp");
var wc = require("./api");

// create static directory
mkdirp.sync(path.join(__dirname, "../static/store"));

var get = function(item) {
  return new Promise(function (resolve, reject) {
    wc.get(item, function(err, data, res) {
      if (err) {
        return reject(err);
      }
      return resolve(data);
    });
  });
};

var save = function(item) {
  get(item).then(function(res) {
    var content = JSON.stringify(JSON.parse(res.body), null, 2);
    var file = path.join(__dirname, "../static/store/" + item + ".json");
    fs.writeFile(file, content, function (err) {
      if (err) {
        console.log("wc write error");
        console.log(err);
      }
    });
    
  }).catch(function(err) {
    console.log("wc get error");
    console.log(err);
  });
};

// Schedule. Only in production
var env = require("../helpers/env").env;
if (env == "production") {
  var minutes = 30;
  interval = minutes * 60 * 1000;
  setInterval(function () {
    save("products");
  }, interval);
}

module.exports = {
  save: save
};