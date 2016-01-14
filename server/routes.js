var fs = require("fs");
var path = require("path");
var httpProxy = require("http-proxy");
var ghostPath = path.join(__dirname, "../node_modules/ghost/");
var express = require(ghostPath + "node_modules/express");
var hbs = require(ghostPath + "node_modules/express-hbs");
app = express();

// custom rendering for the wiki
var theme = path.join(__dirname, "../content/themes/osmc");

app.engine("hbs", hbs.express4({
  partialsDir: theme + "/partials"
}));
app.set("view engine", "hbs");
app.set("views", theme);

// proxy settings
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

var host = "http://localhost:2368";

app.use("/content/themes/osmc/library/images/email", express.static(theme + "/assets/mail"));
app.use("/assets/images", express.static(theme + "/assets/img/lightbox"));

app.all("/", function(req, res){
  var url = host + "/home";
  proxySingle.web(req, res, {target: url});
});

app.all("/home", function(req, res){
  var url = host + "/404";
  proxySingle.web(req, res, {target: url});
});

app.all("/blog", function(req, res){
  var url = host + "/";
  proxySingle.web(req, res, {target: url});
});

app.all("/blog/page/1", function(req, res){
  res.redirect("/blog");
});

app.all("/blog/page/:page", function(req,res) {
	var url = host + "/page/" + req.params.page;
  proxySingle.web(req, res, {target: url});
});

app.all("/wiki", function(req, res){
  var url = host + req.url;
  proxySingle.web(req, res, {target: url});
});

app.get("/wiki/:var(general|raspberry-pi|vero)?", function(req, res) {
	res.redirect("/wiki");
});

app.get("/help/wiki/*", function(req, res) {
	res.redirect("/wiki");
});

var wiki = require("./wiki").wikiCheck;
app.get("/wiki/*", function(req, res) {
  var content = wiki(req.url);
  if (content) {
    res.render("page-wiki-post.hbs", {wikiPost: content});
  } else {
    proxySingle.web(req, res, {target: host + "/404"});
  }
});

app.get("/download/**/*", function(req, res){
  res.redirect("/download");
});

app.get("/author/*", function(req, res){
  res.redirect("/blog");
});

app.get("/status/wiki", function(req, res) {
	res.sendFile(path.join(__dirname, "/static", "wiki-status.html"));
});

app.get("/about/corporate/eula", function(req, res) {
	res.redirect("/corporate-and-legal/#eula");
});

app.get("/assets/discourse/discourse.js", function(req, res) {
  res.sendFile(__dirname + "/static/discourse.js");
});

app.get("/shop", function(req, res){
  res.redirect("https://store.osmc.tv");
});

app.all("/*", function(req, res){
  var url = host + req.url;
  proxyAll.web(req, res, {target: url});
});