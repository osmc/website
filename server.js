var path = require("path");
var ghostRoot = path.join(__dirname, "../../../");
var express = require(ghostRoot + "node_modules/express");
var httpProxy = require("http-proxy");
app = express();

// Start ghost
process.env.NODE_ENV = "production";
require(ghostRoot + "index.js");

var host = "http://localhost:2368";

var proxyAll = httpProxy.createProxyServer({
  prependPath: false,
  ignorePath: false,
});
var proxySingle = httpProxy.createProxyServer({
  prependPath: true,
  ignorePath: true,
});

app.all("/", function(req, res){
  var url = host + "/home";
  proxySingle.web(req, res, {target: url});
});

app.all("/blog", function(req, res){
  var url = host;
  proxySingle.web(req, res, {target: url});
});

app.all("/wiki/*", function(req, res){
  res.oldWriteHead = res.writeHead;
  res.writeHead = function(statusCode, headers) {
    res.oldWriteHead("200", headers);
  }
  
  var url = host + req.url;
  proxySingle.web(req, res, {target: url});
});

app.get("/server.js", function(req, res){  
  var url = host + "/404";
  proxySingle.web(req, res, {target: url});
});

app.all("/*", function(req, res){  
  var url = host + req.url;
  proxyAll.web(req, res, {target: url});
});

app.listen(2369);