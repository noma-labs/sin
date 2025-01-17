window._ = require("lodash");

window.Popper = require("popper.js/dist/umd/popper");

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

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
