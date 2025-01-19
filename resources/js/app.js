window.Popper = require("popper.js/dist/umd/popper");

try {
  window.$ = window.jQuery = require("jquery/dist/jquery");
  require("bootstrap");
} catch (e) {
  console.log(e);
}

$(function () {
  $('[data-toggle="tooltip"]').tooltip();
});

$(function () {
  // disabled all the input element in a form when a form is submitted
  $(function () {
    $("form").submit(function () {
      $(this)
        .find(":input")
        .filter(function () {
          return !this.value;
        })
        .attr("disabled", "disabled");
      return true; // ensure form still submits
    });
  });
});
