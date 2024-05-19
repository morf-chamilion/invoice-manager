const glob = require("glob");

// Keenthemes' plugins
var componentJs = glob.sync(`resources/theme/src/js/components/*.js`) || [];
var coreLayoutJs = glob.sync(`resources/theme/src/js/layout/*.js`) || [];

module.exports = [...componentJs, ...coreLayoutJs];
