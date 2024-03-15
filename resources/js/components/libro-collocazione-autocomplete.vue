<template>
  <div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <label class="form-label">{{ title }}</label>
        <v-select
          :options="lettere"
          :debounce="500"
          :on-search="getOptions"
          :on-change="changed"
          :placeholder="placeholder"
          label="label"
        >
          <span slot="no-options">Nessuna collocazione.</span>
        </v-select>
      </div>
    </div>

    <div class="col-md-4">
      <div class="form-group">
        <label class="form-label">Numeri(*)</label>
        <select class="form-control" v-model="selectedNumeri">
          <option v-if="hasNumeri" value="" disabled selected hidden>
            Sel. numeri
          </option>
          <optgroup
            v-if="numeri.numeriAssegnati != null"
            label="Numeri assegnati"
          >
            <option
              v-for="numero in numeri.numeriAssegnati"
              v-bind:value="numero"
            >
              {{ numero }}
            </option>
          </optgroup>
          <optgroup v-if="numeri.numeriMancanti != null" label="Numeri liberi">
            <option
              v-for="numero in numeri.numeriMancanti"
              v-bind:value="numero"
            >
              {{ numero }}
            </option>
          </optgroup>
          <optgroup v-if="numeri.numeroNuovo != null" label="Nuovo Numero">
            <option v-bind:value="numeri.numeroNuovo">
              {{ numeri.numeroNuovo }}
            </option>
          </optgroup>
        </select>
      </div>
    </div>
    <input type="hidden" :name="name" v-model="selectedCollocazione" />
  </div>
</template>

<script>
import vSelect from "vue-select";

export default {
  components: { vSelect },
  props: {
    title: {
      type: String,
      default: "Collocazione - Lettere",
    },
    urlLettere: {
      type: String,
      // default: '/biblioteca/autocomplete/collocazione',
      // required: true
    },
    urlNumeriForLettere: {
      type: String,
      default: "/biblioteca/search/autocomplete/collocazione",
      // required:true
    },
    numeriRequired: {
      type: String,
      default: "false",
    },
    placeholder: {
      type: String,
      default: "Inserisci lettere collocazione...",
    },
    name: {
      type: String,
      default: "xCollocazione",
    },
    numeriAssegnati: {
      //mostra solo i numeri asseganti oppure tutti i numeri
      type: String,
      default: "true",
    },
    numeriMancanti: {
      //mostra solo i numeri asseganti oppure tutti i numeri
      type: String,
      default: "true",
    },
    numeroNuovo: {
      //mostra solo i numeri asseganti oppure tutti i numeri
      type: String,
      default: "true",
    },
  },
  data() {
    return {
      selectedLettere: "",
      selectedNumeri: "", // null
      numeri: {}, // { numeriAssegnati: [], numeriMancanti:[] numeroNuovo:[]},
      lettere: [], // list of letters (e.g. AAA, AAB, AAC )
    };
  },
  computed: {
    hasNumeri: function () {
      return Object.keys(this.numeri).length;
    },
    selectedCollocazione: function () {
      console.log(this.numeriRequired);
      if (this.numeriRequired == "true" && !this.selectedNumeri)
        // return the collocazione only if numeri has been selected
        return null;
      else return this.selectedLettere + this.pad(this.selectedNumeri, 3); // generate the XXXNNN collocazione hwere X is letter and N a number
    },
  },
  methods: {
    getOptions(search, loading) {
      loading(true);
      axios
        .get(this.urlLettere, { params: { term: search } })
        .then((response) => {
          this.lettere = response.data;
          {
          }
          loading(false);
        })
        .catch((error) => {});
    },
    changed: function (value) {
      this.selectedLettere = value.value;
      //  biblioteca/search/autocomplete/collocazione?lettere=AAA&nuovo=true&mancanti=true&
      axios
        .get(this.urlLettere, {
          params: {
            lettere: value.label,
            nuovo: this.numeroNuovo,
            mancanti: this.numeriMancanti,
            assegnati: this.numeriAssegnati,
          },
        })
        .then((response) => {
          this.numeri = response.data; //numeriAssegnati; numeriMancanti; numeroNuovo;
          loading(false);
        })
        .catch((error) => {});
    },
    // pad with zero to the left (eg., pad(2,3) = 002)
    pad: function (a, b) {
      //a // the number to convert // number of resulting characters
      if (a)
        return ([1e15] + a) // combine with large number
          .slice(-b);
      // cut leading "1"
      else return "";
    },
  },
};
</script>
