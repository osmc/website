var fs = require("fs");
var path = require("path");
var ghostPath = path.join(__dirname, "../node_modules/ghost/");
var hbs = require(ghostPath + "node_modules/express-hbs");
var request = require(ghostPath + "node_modules/request");
var chokidar = require("chokidar");
var _ = require("lodash");

var host = "http://download.osmc.tv/installers/";
var versionsUrl = host + "versions_";
var imagesUrl = host + "diskimages/";

var names = {
  rbp1: "Raspberry Pi 1 / Zero",
  rbp2: "Raspberry Pi 2 / 3",
  vero1: "Vero",
  vero2: "Vero 2",
  appletv: "Apple TV 1"
};

// Schedule. Only in production
var env = require("./helpers/env").env;
if (env == "production") {
  var minutes = 15;
  interval = minutes * 60 * 1000;
  setInterval(function () {
    fetch();
  }, interval);
}

// on load
fetch();

var html = "";

var imagelist = path.join(__dirname, "/static/imagelist.html");

var watcher = chokidar.watch(imagelist);
watcher.on("change", function () {
  readImagelist();
}).on("add", function () {
  readImagelist();
});

function readImagelist() {
  var readFile = require("./helpers/readFile.js");
  readFile("images", imagelist).then(function(res) {
    html = res;
  });
}

var files = [];
var items = [];

function fetch() {
  
  var count = 0;

  var itemCount = 0;

  for (var key in names) {
    itemCount++;

    if (key === "rbp1") {
      key = "rbp";
    }

    if (key === "appletv") {
      var newkey = key;
    } else  {
      var newkey = key.toUpperCase();
    }
    
    request(versionsUrl + newkey, function (error, response, body) {
      count++;
      if (!error && response.statusCode == 200) {
        var nSplit = body.split("\n");
        
        nSplit.forEach(function (item, i) {
          var spaceSplit = item.split(" ");

          var filename = spaceSplit[0];
          var url = spaceSplit[1];
          if (url) {
            var file = url.split("/").pop();
            items.push({
              file: file,
              filename: filename
            });
          }
        });
      }
      
      if (count === itemCount) {
        process();
      }

    });

  }
}

function process() {
  var itemsCount = items.length;
  var count = 0;

  items.forEach(function (item, i) {
    
    var file = item.file;
    var filename = item.filename;

    var split = file.split("_");
    var id = split[2];
    var md5Url = imagesUrl + file.split(".")[0] + ".md5";
    
    // get md5 string
    request(md5Url, function (error, response, body) {
      if (!error && response.statusCode == 200) {

        var md5 = body.split("  ")[0];

        // so we can sort by date
        var unixDate = new Date(filename.replace("-", ".")).getTime();

        files.push({
          id: id,
          name: names[id],
          unixDate: unixDate,
          filename: filename,
          url: imagesUrl + file,
          md5: md5
        });

        count++;
        
        if (count === itemsCount) {
          buildHtml();
        }
      } else {
        console.log("md5-error");
        console.log(md5Url);
      }
    });
  });
};

function buildHtml() {
  
  var content = "";

  // device ids that have images
  var ids = _.map(_.uniq(files, "id"), "id");

  // loop names for sorting (instead of ids)
  for (var key in names) {

    // check if id exist
    if (ids.indexOf(key) > -1) {

      // find objects with correct id and sort by unixDate
      var images = _.sortBy(_.filter(files, {
        id: key
      }), "unixDate").reverse();

      var list = "";

      images.forEach(function (file) {
        var div = "<tr><td><a href='" + file.url + "'>" + file.filename + "</a></td><td>" + file.md5 + "</td></tr>";
        list += div;
      });

      var header = "<tr><th>Release</th><th>Checksum (MD5)</th></tr>";
      content += "<section class='download-table " + key + "'><header><h3 class='download-table-title'>" + images[0].name + "</h3></header><table>" + header + list + "</table></section>";

    }

  };

  fs.writeFile(imagelist, content, function (err) {});

  // reset
  files = [];
  items = [];
};

module.exports = function () {
  hbs.registerHelper("images", function (option, res) {
    if (html) {
      return html;
    }
  });
};
