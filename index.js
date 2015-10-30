var ghost = require("ghost");
var fs = require("fs");
var path = require("path");
var httpProxy = require("http-proxy");
var ghostPath = path.join(__dirname, "node_modules/ghost/");
var express = require(ghostPath + "node_modules/express");
var hbs = require(ghostPath + "node_modules/express-hbs");

require("./helpers/env");

require("./helpers/custom").helpers();

require("./helpers/images")();

require("./helpers/update");

options = {
	config: path.join(__dirname, "config.js")
};
ghost(options).then(function(ghostServer) {
	ghostServer.start();
});

app = express();

// custom rendering for the wiki
var theme = __dirname + "/content/themes/osmc";
app.engine("hbs", hbs.express4({
  partialsDir: theme + "/partials"
}));
app.set("view engine", "hbs");
app.set("views", theme);

var host = "http://localhost:2368";

var proxyAll = httpProxy.createProxyServer({
  prependPath: false,
  ignorePath: false,
}).on("error", function(err, req, res) {
  res.end();
});
var proxySingle = httpProxy.createProxyServer({
  prependPath: true,
  ignorePath: true,
}).on("error", function(err, req, res) {
  res.end();
});

app.all("/", function(req, res){
  var url = host + "/home";
  proxySingle.web(req, res, {target: url});
});

app.all("/blog", function(req, res){
  var url = host + "/";
  proxySingle.web(req, res, {target: url});
});

app.all("/page/1", function(req, res){
  res.redirect("/blog");
});

app.all("/wiki", function(req, res){
  var url = host + req.url;
  proxySingle.web(req, res, {target: url});
});

app.get("/wiki/:var(general|raspberry-pi|vero)?", function(req, res) {
	res.redirect("/wiki");
});

var wiki = require("./helpers/custom").wikiCheck;
app.get("/wiki/*", function(req, res) {
  var content = wiki(req.url);
  if (content) {
    res.render("page-wiki-post.hbs", {wikiPost: content});
  } else {
    proxySingle.web(req, res, {target: host + "/404"});
  }
});

app.all("/author/*", function(req, res){
  res.redirect("/blog");
});

app.use("/content/themes/osmc/library/images/email", express.static(theme + "/assets/mail"));

app.all("/*", function(req, res){
  var url = host + req.url;
  proxyAll.web(req, res, {target: url});
});

app.listen(2369);