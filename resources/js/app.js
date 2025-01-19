require("./bootstrap");

import Vue from "vue";
window.Vue = Vue; //this is important! Do not use require('vue')
Vue.config.devtools = true;

Vue.component("my-modal", require("./components/my-modal.vue").default);

// create Vue instance
const app = new Vue({
  el: "#archivio",
  data: {
    showModal: false,
  },
});


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
