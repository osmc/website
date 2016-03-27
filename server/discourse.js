var fs = require("fs");
var path = require("path");
var ghostPath = path.join(__dirname, "../node_modules/ghost/");
var request = require(ghostPath + "node_modules/request");
var cheerio = require(ghostPath + "node_modules/cheerio");

var auth = require(path.join(__dirname, "../content/data/keys")).discourse;

var url = "https://discourse.osmc.tv/t/auto-links/7458.json?api_key=" + auth.key + "&api_username=" + auth.user;

// Schedule. Only in production
var env = require("./helpers/env").env;
if (env == "production") {
  var minutes = 5;
  interval = minutes * 60 * 1000;
  setInterval(function () {
    fetch();
  }, interval);
}

fetch();

function fetch() {
  request(url, function (error, response, body) {
    if (!error && response.statusCode == 200) {
      var json = JSON.parse(body);
      var html = json.post_stream.posts[0].cooked;
      $ = cheerio.load(html);
      var code = $("code").text();
      
      build(code);
    }
    if (error) {
      console.log("discourse.js fetch error");
      console.log(error);
    }
  });
}



function build(code) {
  var script = fs.readFileSync(path.join(__dirname, "../src/assets/js/discourse.min.js"), "utf-8", function (err, data) {
    return data.toString;
  });
  
  var js = code + script;
  var file = path.join(__dirname, "../content/themes/osmc/assets/discourse/main.js");
  fs.writeFile(file, js, function (err) {
    if (err) {
      console.log("discourse.js write error");
      console.log(err);
    }
  });
}