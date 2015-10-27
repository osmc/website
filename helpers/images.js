var fs = require("fs");
var path = require("path");
var ghostPath = path.join(__dirname, "../node_modules/ghost/");
var hbs = require(ghostPath + "node_modules/express-hbs");
var _ = require(ghostPath + "node_modules/lodash");
var cheerio = require(ghostPath + "node_modules/cheerio");
var request = require(ghostPath + "node_modules/request");
var chokidar = require("chokidar");

var host = "http://singapore.mirror.osmc.tv/osmc/download/installers/diskimages/";

var names = {
  rbp1: "Raspberry Pi 1",
  rbp2: "Raspberry Pi 2",
  vero1: "Vero",
  appletv: "Apple TV 1"
};

html = "";
var files = [];


// Schedule. Only in production
var env = require("./env");
if ( env == "production" ) {
  var minutes = 15;
  interval = minutes * 60 * 1000;
  setInterval(function() {
    fetch();
  }, interval);
}

var imagelist = path.join(__dirname, "/static/imagelist.html");

var watcher = chokidar.watch(imagelist);
watcher.on("change", function() {
  readImagelist();
});

readImagelist();
function readImagelist() {
  try {
    html = fs.readFileSync(imagelist);
  } catch (err) {
    console.log("imagelist.html not found... ಠ_ಠ");
    console.log("But don't worry!");
    console.log("Downloading right now  ｡◕‿◕｡");
    fetch();
  }
};

function fetch() {
  files = [];
  
  request(host, function (error, response, body) {
    if (!error && response.statusCode == 200) {
      $ = cheerio.load(body);

      var urls = [];
      $("a").each(function (i, item) {
        var url = item.attribs.href;

        // only img files
        if (url.substring(0, 8) == "OSMC_TGT" && url.split(".")[1] == "img") {
          urls.push(url);
        }
      });

      var urlsCount = urls.length;
      var count = 0;
      urls.forEach(function (url, i) {

        var split = url.split("_");
        var id = split[2];
        var date = split[3].substring(0, split[3].indexOf("."));
        var md5Url = host + url.split(".")[0] + ".md5";
        // get md5 string
        request(md5Url, function (error, response, body) {
          if (!error && response.statusCode == 200) {

            var md5 = body.split("  ")[0];
            console.log(md5);

            // so we can sort by date
            var newDate = date.substring(0, 4) + "." + date.substring(4, 6) + "." + date.substring(6, 8)
            var unixDate = new Date(newDate).getTime();

            files.push({
              id: id,
              name: names[id],
              unixDate: unixDate,
              date: newDate,
              url: host + url,
              md5: md5
            });

            count += 1;

            if (count === urlsCount) {
              buildHtml();
            }
          }
        });
      });
    }
  });
};

function buildHtml() {

  var content = "";

  // device ids that have images
  var ids = _.pluck(_.uniq(files, "id"), "id");

  // loop names for sorting (instead of ids)
  for (var key in names) {

    // check if id exist
    if (ids.indexOf(key) > -1) {
      
      // find objects with correct id and sort by unixDate
      var images = _.sortBy(_.where(files, {
        id: key
      }), "unixDate").reverse();
      
      var list = "";

      images.forEach(function (file) {
        var div = "<tr><td><a href='" + file.url + "'>" + file.date + "</a></td><td>" + file.md5 + "</td></tr>";
        list += div;
      });

      var header = "<tr><th>Release</th><th>Checksum (MD5)</th></tr>";
      content += "<section class='table " + key + "'><header><h2>" + images[0].name + "</h2></header><table>" + header + list + "</table></section>";

    }

  };
  
  fs.writeFile(imagelist, content, function(err) {
  }); 

};

module.exports = function () {
  hbs.registerHelper("images", function (option, res) {
    return html;
  });
};