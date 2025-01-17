window.Popper = require("popper.js/dist/umd/popper");


try {
  window.$ = window.jQuery = require("jquery/dist/jquery");

  require("bootstrap");
} catch (e) {}

window.ui = require("jquery-ui");

// Jquery datapicker
import "jquery-ui/ui/i18n/datepicker-it.js";

$(document).ready(function () {
  $("#datepicker").datepicker();
});
