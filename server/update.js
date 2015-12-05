var path = require("path");
var ghostPath = path.join(__dirname, "../node_modules/ghost/");
var sqlite = require(ghostPath + "node_modules/sqlite3");
var knex = require(ghostPath + "node_modules/knex")({
  client: "sqlite3",
  connection: {
    filename: path.join(__dirname, "../content/data/ghost.db")
  }
});
var bookshelf = require(ghostPath + "node_modules/bookshelf")(knex);
var Post = bookshelf.Model.extend({
  tableName: "posts"
});

// Schedule. Only in production
var env = require("./env").env;
if (env == "production") {
  var seconds = 10;
  interval = seconds * 1 * 1000;
  setInterval(function () {
    check();
  }, interval);
}

var first = 0;
var content;

function check() {
  new Post().fetchAll({
    columns: ["updated_at"]
  }).then(function (res) {
    var res = JSON.stringify(res.toJSON());
    if (res !== content && first === 1) {
      purge();
    }
    content = res;
    first = 1;
  });
}

function purge() {
  console.log("purge");
}