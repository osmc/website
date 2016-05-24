var cheerio = require("cheerio");

var get = require("../../helpers/get");
var names = require("./names");

var devices = function(html, host) {
  $ = cheerio.load(html);
  var array = [];

  $("a").each(function() {
    var val = $(this).text();
    if (val.indexOf("versions_") === -1) {
      return;
    }

    var obj = {
      title: names[val.toLowerCase()],
      url: host + val
    };

    array.push(obj);
  });

  return array;
};

var files = function(res) {
  return new Promise(function (resolve, reject) {
    var data = res;
    data.files = [];

    get(data.url).then(function(res) {
      var lines = res.split("\n").slice(0, -1);

      var objects = lines.map(function(item) {
        return new Promise(function (resolve, reject) {

          var title = item.split(" ")[0];
          var url = item.split(" ")[1];
          var md5Url = url.slice(0, -6) + "md5";

          get(md5Url).then(function(md5) {
            md5 = md5.split(" ")[0];

            var obj = {
              title: title,
              url: url,
              md5: md5
            };

            return resolve(obj);

          });
        });
      });

      Promise.all(objects).then(function(array) {
        array.forEach(function(obj) {
          data.files.push(obj);
        });

        return resolve(data);
      });

    });
  });
};

module.exports = {
  devices: devices,
  files: files
};
