var ghost = require("ghost");
var path = require("path");
var express = require("express");
var httpProxy = require("http-proxy");

var flag = process.argv[2];
if ( flag != "dev" ) {
	process.env.NODE_ENV = "production";
}

require('./helpers/custom')();

options = {
	config: path.join(__dirname, 'config.js')
};
ghost(options).then(function(ghostServer) {
	ghostServer.start();
});

app = express();

var host = "http://localhost:2368";

var proxyAll = httpProxy.createProxyServer({
  prependPath: false,
  ignorePath: false,
}).on('error', function(err, req, res) {
  res.end();
});
var proxySingle = httpProxy.createProxyServer({
  prependPath: true,
  ignorePath: true,
}).on('error', function(err, req, res) {
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
  var url = host + "/home";
  res.redirect("/blog");
});

app.all("/wiki", function(req, res){
  var url = host + req.url;
  proxySingle.web(req, res, {target: url});
});

app.all("/wiki/*", function(req, res){
  var url = host + "/wikipost";
  proxySingle.web(req, res, {target: url});
});

app.all("/*", function(req, res){
  var url = host + req.url;
  proxyAll.web(req, res, {target: url});
});

app.listen(2369);