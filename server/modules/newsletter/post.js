var get = require("../../helpers/discourse").get;
var filters = require("./filters");

var post = function(id) {
  return new Promise(function (resolve, reject) {
    var url = "https://discourse.osmc.tv/t/" + id + ".json";

    get(url).then(function(res) {
      var title = res.title;
      var body = res.post_stream.posts[0].cooked;
      body = filters.post(body);

      var obj = {
        title: title,
        body: body
      };

      return resolve(obj);
    });
  });
};

module.exports = post;
