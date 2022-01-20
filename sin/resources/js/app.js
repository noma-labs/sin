/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');

// include vue library
// livewire-vue is needed t ouse livewire with vue - https://github.com/livewire/vue
import Vue from 'vue'
import 'livewire-vue'

window.Vue = Vue //this is important! Do not use require('vue')
Vue.config.devtools = true;

// moment for date formatting
window.moment = require('moment');

// Add vuevaludate plugin for form validation
const { validationMixin, default: Vuelidate } = require('vuelidate')
Vue.use(Vuelidate)

// global registration of components
Vue.component('autocomplete', require('./components/autocomplete.vue'));
Vue.component('my-modal', require('./components/my-modal.vue'));
Vue.component('sin-header', require('./components/partials/sin-header.vue'));

// Biblioteca components
Vue.component('search-collocazione', require('./components/libro-collocazione-autocomplete.vue'));
Vue.component('libro-editore-autore', require('./components/biblioteca/libro-editore-autore.vue'));

// Officina components
Vue.component('veicolo-create-form', require('./components/veicolo-create-form.vue'));
Vue.component('veicolo-prenotazione', require('./components/veicolo-prenotazione.vue'));
Vue.component('gomme-veicolo', require('./components/officina/gomme-veicolo.vue'));
Vue.component('gestione-filtri', require('./components/officina/gestione-filtri.vue'));

// DB nomadelfia components
Vue.component('famiglia-select', require('./components/famiglia-select.vue'));
Vue.component('azienda-edit', require('./components/azienda-edit.vue'));
Vue.component('persona-entrata', require('./components/popolazione/persona-entrata.vue'));

// Patente components
Vue.component('patente-modfica', require('./components/patente/patente-modifica.vue'))
Vue.component('patente-inserimento', require('./components/patente/patente-inserimento.vue'))

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