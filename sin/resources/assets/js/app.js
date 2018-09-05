/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');

// include vue library
window.Vue = require('vue');
Vue.config.devtools = true;

window.moment = require('moment');

// Add vuevaludate plugin for form validation
const { validationMixin, default: Vuelidate } = require('vuelidate')
Vue.use(Vuelidate)

// global registration of components
// Application components
Vue.component('autocomplete', require('./components/autocomplete.vue'));
Vue.component('modal', require('./components/my-modal.vue'));
Vue.component('sin-header', require('./components/partials/sin-header.vue'));
// Vue.component('my-modal', require('./components/modal2.vue'));
// Biblioteca components
Vue.component('search-collocazione', require('./components/libro-collocazione-autocomplete.vue'));
// Officina components
Vue.component('veicolo-create-form', require('./components/veicolo-create-form.vue'));
Vue.component('veicolo-prenotazione', require('./components/veicolo-prenotazione.vue'));
// nomadelfia components
Vue.component('famiglia-select', require('./components/famiglia-select.vue'));
Vue.component('azienda-edit', require('./components/azienda-edit.vue'));

// add DatePicker components https://github.com/charliekassel/vuejs-datepicker
import Datepicker from 'vuejs-datepicker';

Vue.component('date-picker', Datepicker);

// create Vue instance
const app = new Vue({
    el: '#archivio',
    data: {
      showModal: false
    }
});


$(document).ready(function(){
	$('[data-toggle="tooltip"]').tooltip(); 
});