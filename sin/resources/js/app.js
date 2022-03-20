/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');

// include vue library
import Vue from 'vue'
// livewire-vue is needed to use livewire with vue - https://github.com/livewire/vue
// require('livewire-vue')

window.Vue = Vue //this is important! Do not use require('vue')
Vue.config.devtools = true;

// moment for date formatting
window.moment = require('moment');

// Add vuevaludate plugin for form validation
const { validationMixin, default: Vuelidate } = require('vuelidate')
Vue.use(Vuelidate)

// global registration of components
Vue.component('autocomplete', require('./components/autocomplete.vue').default);
Vue.component('my-modal', require('./components/my-modal.vue').default);
Vue.component('sin-header', require('./components/partials/sin-header.vue').default);

// Biblioteca components
Vue.component('search-collocazione', require('./components/libro-collocazione-autocomplete.vue').default);
Vue.component('libro-editore-autore', require('./components/biblioteca/libro-editore-autore.vue').default);

// Officina components
Vue.component('veicolo-create-form', require('./components/veicolo-create-form.vue').default);
Vue.component('veicolo-prenotazione', require('./components/veicolo-prenotazione.vue').default);
Vue.component('gomme-veicolo', require('./components/officina/gomme-veicolo.vue').default);
Vue.component('gestione-filtri', require('./components/officina/gestione-filtri.vue').default);

// DB nomadelfia components
Vue.component('famiglia-select', require('./components/famiglia-select.vue').default);
Vue.component('azienda-edit', require('./components/azienda-edit.vue').default);
Vue.component('persona-entrata', require('./components/popolazione/persona-entrata.vue').default);

// Patente components
Vue.component('patente-modfica', require('./components/patente/patente-modifica.vue').default);
Vue.component('patente-inserimento', require('./components/patente/patente-inserimento.vue').default);

// add DatePicker components https://github.com/charliekassel/vuejs-datepicker
import Datepicker from 'vuejs-datepicker';
// set the language fo all the date-picker component
import { it } from 'vuejs-datepicker/dist/locale';

Datepicker.computed.translation = function() {
    return it;
}

Vue.component('date-picker', Datepicker);

// create Vue instance
const app = new Vue({
    el: '#archivio',
    data: {
        showModal: false
    }
});


$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});