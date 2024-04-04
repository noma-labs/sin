<template>
  <div class="card card-mod">
    <!-- <img class="card-img-top" src="..." alt="Card image cap"> -->
    <div class="card-header card-header-mod">
      <h3 class="card-title">Tipi Di Gomme</h3>
    </div>
    <div class="card-body card-body-mod">
      <ul class="list-group list-group-flush">
        <li
          v-for="(g, i) in gomme_local"
          v-bind:key="g.id"
          class="list-group-item"
        >
          <div class="row">
            <div class="col-sm-10">
              {{ g.codice }}
            </div>
            <div class="col-sm-2">
              <button
                type="button"
                @click="eliminaGomma(i, g.id)"
                class="btn btn-danger btn-sm"
              ></button>
            </div>
          </div>
        </li>
      </ul>
      <br />
      <my-modal
        modal-title="Aggiungi Tipo Gomma"
        button-title="Aggiungi Gomma"
        button-style="btn-block btn-warning"
      >
        <template v-slot:modal-body-slot>
          <button
            class="btn btn-warning"
            type="button"
            @click="toggleMode"
            v-bind:disabled="esistente"
          >
            Esistente
          </button>
          <button
            class="btn btn-warning"
            type="button"
            @click="toggleMode"
            v-bind:disabled="nuova"
          >
            Nuova
          </button>
          <form method="POST" id="form-aggiungi-gomma">
            <div class="row">
              <div class="col-md-6">
                <label for="codice">Codice Gomma</label>
                <input
                  v-if="nuova"
                  name="codice"
                  type="text"
                  id="codice"
                  v-model="codice_nuova_gomma"
                  class="form-control"
                  placeholder="es. 195/70 r15"
                />
                <select
                  v-if="esistente"
                  class="form-control"
                  name="codice"
                  type="text"
                  v-model.lazy="codice_esistente_gomma"
                >
                  <option value="" disabled hidden>--Seleziona--</option>
                  <option
                    v-for="g in lista_gomme"
                    v-bind:key="g.id"
                    v-bind:value="g.id"
                  >
                    {{ g.codice }}
                  </option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="note">Note</label>
                <input
                  type="text"
                  name="note"
                  id="note"
                  class="form-control"
                  v-model="note"
                  placeholder="Nota Facoltativa..."
                />
              </div>
            </div>
          </form>
          <br />
          <div v-if="error" class="alert alert-danger">{{ errorMsg }}</div>
        </template>
        <template v-slot:modal-button>
          <button type="button" class="btn btn-success" @click="nuovaGomma">
            Salva
          </button>
        </template>
      </my-modal>
    </div>
  </div>
</template>

<script>
export default {
  props: ["gomme", "veicolo", "token"],
  data() {
    return {
      gomme_local: this.gomme,
      nuova: true,
      esistente: false,
      urlEliminaGomma: "/api/officina/gomme/elimina",
      urlGomme: "/api/officina/gomme",
      urlNuovaGomma: "/api/officina/gomme/nuova",
      lista_gomme: "",
      codice_nuova_gomma: "",
      codice_esistente_gomma: "",
      note: "",
      errorMsg: "",
      error: false,
    };
  },
  mounted: function () {
    var dataprep = {
      url: this.urlGomme,
      method: "GET",
    };
    axios(dataprep).then((response) => {
      this.lista_gomme = response.data;
    });
  },
  methods: {
    aggiornaArray: function (key) {
      this.gomme_local.splice(key, 1);
    },
    eliminaGomma: function (key, gomma_local) {
      var resp;
      // elimino la relationship gomma-veicolo dal db
      var dataprep = {
        method: "POST",
        url: this.urlEliminaGomma,
        data: {
          veicolo: this.veicolo,
          gomma: gomma_local,
        },
      };
      axios(dataprep).then((response) => {
        if (response.data[0] == "success") {
          console.log("ciao");
          //elimina la gomma dall'array
          this.aggiornaArray(key);
        }
      });
    },
    toggleMode: function () {
      if (this.nuova) {
        this.nuova = false;
        this.esistente = true;
        this.codice_nuova_gomma = "";
        this.codice_esistente_gomma = "";
        this.note = "";
      } else if (this.esistente) {
        this.nuova = true;
        this.esistente = false;
        this.codice_nuova_gomma = "";
        this.codice_esistente_gomma = "";
        this.note = "";
      }
    },
    nuovaGomma: function () {
      this.error = false;
      var dataprep = {
        url: this.urlNuovaGomma,
        method: "POST",
        header: { "X-CSRF-TOKEN": this.token },
        data: {
          gomma_id: this.codice_esistente_gomma,
          nuovo_codice: this.codice_nuova_gomma,
          note: this.note,
          veicolo_id: this.veicolo,
        },
      };
      axios(dataprep).then((response) => {
        if (response.data["status"] == "error") {
          this.errorMsg = response.data["msg"];
          this.error = true;
        } else {
          location.reload();
        }
      });
    },
  },
};
</script>

<style></style>
