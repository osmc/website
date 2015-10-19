var flag = process.argv[2];
if ( flag != "dev" ) {
  process.env.NODE_ENV = "production";
  var env = "production";
} else {
  var env = "development";
}

module.exports = env;