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

$(document).ready(function () {
  $('[data-toggle="tooltip"]').tooltip();
});
