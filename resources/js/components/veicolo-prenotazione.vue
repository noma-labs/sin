<template>
  <div>
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
          <label for="data_par">Data Partenza</label>
          <input
            type="date"
            class="form-control"
            id="data_par"
            name="data_par"
            v-model="data_partenza"
            required
          />
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="ora_par">Ora Partenza</label>
          <input
            type="time"
            class="form-control"
            id="ora_par"
            name="ora_par"
            v-model="ora_partenza"
            required
          />
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="data_arr">Data Arrivo</label>
          <input
            type="date"
            class="form-control"
            id="data_arr"
            name="data_arr"
            v-model="data_arrivo"
            required
          />
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="ora_arr">Ora Arrivo</label>
          <input
            type="time"
            class="form-control"
            id="ora_arr"
            name="ora_arr"
            v-model="ora_arrivo"
            required
          />
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label for="veicolo">Veicolo</label>
          <select class="form-control" name="veicolo">
            <option selected disabled hidden>--Seleziona veicolo--</option>
            <optgroup
              v-for="tipologia in veicoli_per_impiegotipologia"
              :label="
                tipologia.impiego_tipologia + ' (' + tipologia.count + ')'
              "
            >
              <option
                :selected="veicolo.id == veicolo_selected"
                v-for="veicolo in tipologia.veicoli"
                v-bind:value="veicolo.id"
                :disabled="hasPrenotazioni(veicolo)"
                v-bind:class="{ 'text-danger': hasPrenotazioni(veicolo) }"
              >
                {{ veicolo.nome }}
                <span v-if="hasPrenotazioni(veicolo)">
                  - {{ veicolo.prenotazioni[0].cliente.nominativo }} (
                  {{ veicolo.prenotazioni[0].data_partenza }}:{{
                    veicolo.prenotazioni[0].ora_partenza
                  }}, {{ veicolo.prenotazioni[0].data_arrivo }}:{{
                    veicolo.prenotazioni[0].ora_arrivo
                  }}
                  )
                </span>
              </option>
            </optgroup>
          </select>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    urlVeicoliPrenotazioni: {
      // url to get the veicoli with their prenotazioni
      type: String,
      required: true,
    },
    dataPartenza: {
      // AAAA-MM-GG
      type: String,
      required: true,
    },
    dataArrivo: {
      //
      type: String,
      required: true,
    },
    oraPartenza: {
      type: String,
      required: false,
    },
    oraArrivo: {
      //
      type: String,
      required: false,
    },
    veicoloSelected: {
      type: String,
    },
    idPrenotazioneToExclude: {
      // If passed, the prenotazione is exclude from the
      type: String,
    },
  },
  data() {
    return {
      veicolo_selected: this.veicoloSelected, // ID of the veicolo selected
      ora_arrivo: this.oraArrivo,
      ora_partenza: this.oraPartenza,
      data_partenza: this.dataPartenza,
      data_arrivo: this.dataArrivo,
      veicoli_per_impiegotipologia: null,
      // veicoli raggruppati per impiego e tipologia
      // [
      //   { "impiego_tipologia":	"Grosseto macchine"
      //      "count": 10
      //      "veicoli" :[
      //              {id: x, nome: y},...
      //       ]
      //  }
      //  ...
      // ]
    };
  },
  watch: {
    // whenever ora_partenza, ora_arrivo, data_partenza changes, the updateBusyVehicles() is called
    ora_partenza: function (newQuestion, oldQuestion) {
      this.updateBusyVehicles();
    },
    ora_arrivo: function (newQuestion, oldQuestion) {
      this.updateBusyVehicles();
    },
    data_partenza: function (newQuestion, oldQuestion) {
      this.updateBusyVehicles();
    },
    data_arrivo: function (newQuestion, oldQuestion) {
      this.updateBusyVehicles();
    },
  },
  mounted: function () {
    console.log("calling for prenotazioni");
    axios
      .get(this.urlVeicoliPrenotazioni, {
        params: {
          datapartenza: this.data_partenza,
          dataarrivo: this.data_arrivo,
          ora_in: this.ora_partenza + "," + this.ora_arrivo,
          except: this.idPrenotazioneToExclude,
        },
      })
      .then((response) => {
        this.veicoli_per_impiegotipologia = response.data;
      })
      .catch((e) => {
        this.errors.push(e);
      });
  },
  methods: {
    updateBusyVehicles() {
      axios
        .get(this.urlVeicoliPrenotazioni, {
          params: {
            datapartenza: this.data_partenza,
            dataarrivo: this.data_arrivo,
            ora_in: this.ora_partenza + "," + this.ora_arrivo,
            except: this.idPrenotazioneToExclude,
          },
        }) //this.data_partenza
        .then((response) => {
          this.veicoli_per_impiegotipologia = response.data;
          console.log("Veicoli con prenotazioni aggiornati");
        })
        .catch((error) => {});
    },
    hasPrenotazioni(veicolo) {
      return veicolo.prenotazioni && veicolo.prenotazioni.length > 0;
    },
  },
};
</script>
