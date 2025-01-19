window.Popper = require("popper.js/dist/umd/popper");

try {
  window.$ = window.jQuery = require("jquery/dist/jquery");
  require("bootstrap");
} catch (e) {}
