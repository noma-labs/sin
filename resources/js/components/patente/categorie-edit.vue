<template id="categorie">
  <div>
    <div class="row">
      <div class="col-md-3">Categoria</div>
      <div class="col-md-3">Data rilascio</div>
      <div class="col-md-3">Data scadenza</div>
      <div class="col-md-3">Restrizioni</div>
    </div>
    <div class="row" v-for="categoria in categorieAssegnate">
      <div class="col-md-3">{{ categoria.categoria }}</div>
      <div class="col-md-3">
        <date-picker
          :value="categoria.pivot.data_rilascio"
          placeholder="Selezionare una data"
          :language="language"
          :format="customFormatter"
        ></date-picker>
      </div>
      <div class="col-md-3">
        <date-picker
          :value="categoria.pivot.data_scadenza"
          placeholder="Selezionare una data"
          :language="language"
          :format="customFormatter"
        ></date-picker>
      </div>
      <!-- <div class="col-md-3">{{categoria.pivot.restrizione_codice}}	</div> -->
    </div>
    <div class="row">
      <a class="btn btn-primary col-md-4 offset-md-8" @click="open"
        >Aggiungi categoria</a
      >
    </div>
    <!-- Modal Aggiungi categoria -->
    <transition name="modal">
      <div
        class="modal-mask"
        @click="close"
        v-show="showModalAggiungiCategoria"
      >
        <div class="modal-container" @click.stop>
          <div class="modal-header">
            <h3>Aggiungi Categoria</h3>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Categoria</label>
              <select class="form-control" v-model="nuovaCategoria.id">
                <option value="-1">---Selezione categoria---</option>
                <option
                  v-for="categoria in categoriePossibili"
                  v-bind:value="categoria.id"
                >
                  {{ categoria.categoria }} - {{ categoria.descrizione }}
                </option>
              </select>
            </div>
            <div class="row">
              <div class="col-md-6">
                <label>Categoria rilasciata il:</label>
                <date-picker
                  :bootstrap-styling="true"
                  placeholder="Selezionare una data"
                  @selected="selectRilascio"
                  :language="language"
                  :format="customFormatter"
                >
                </date-picker>
              </div>
              <div class="col-md-6">
                <label>Categoria valida fino al:</label>
                <date-picker
                  :bootstrap-styling="true"
                  placeholder="Selezionare una data"
                  @selected="selectValidita"
                  :language="language"
                  :format="customFormatter"
                >
                </date-picker>
              </div>
            </div>

            <div class="form-group">
              <label lass="form-control">Restrizione</label>
              <input
                type="input"
                class="form-control"
                v-model="nuovaCategoria.restrizioni"
              />
            </div>
          </div>
          <div class="modal-footer text-right">
            <input
              class="btn btn-success"
              type="button"
              :disabled="disabledSalva"
              @click="salvaAggiungiCategoria"
              value="Salva"
            />
            <a class="btn btn-danger" href="#" role="button" @click="close"
              >Chiudi</a
            >
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import { it } from "vuejs-datepicker/dist/locale";

export default {
  props: ["numero_patente"],
  data: function () {
    return {
      language: it,
      categorieAssegnate: [],
      categoriePossibili: [],
      showModalAggiungiCategoria: false,
      nuovaCategoria: {
        id: -1,
        data_rilascio: null,
        data_validita: null,
        restrizioni: null,
      },
    };
  },
  created: function () {
    // if(!this.numero_patente == null){
    axios
      .get("/api/patente/" + this.numero_patente + "/categorie")
      .then((response) => {
        this.categorieAssegnate = response.data.categorie;
        console.log(this.categorie);
      });
    // }
  },
  computed: {
    disabledSalva: function () {
      return (
        this.nuovaCategoria.id == null ||
        this.nuovaCategoria.data_rilascio == null ||
        this.nuovaCategoria.data_validita == null
      );
    },
  },
  methods: {
    customFormatter(date) {
      return moment(date).format("YYYY-MM-DD");
    },
    getCategoriePossibili() {
      var api = "/api/patente/categorie";
      if (this.numero_patente != null)
        api =
          "/api/patente/" + this.numero_patente + "/categorie?filtro=possibili";

      axios.get(api).then((response) => {
        this.categoriePossibili = response.data;
        console.log(this.categoriePossibili);
      });
    },
    selectValidita: function (data) {
      this.nuovaCategoria.data_validita = this.customFormatter(data);
    },
    selectRilascio: function (data) {
      this.nuovaCategoria.data_rilascio = this.customFormatter(data);
    },
    salvaAggiungiCategoria: function () {
      console.log(this.nuovaCategoria);
      axios
        .post("/api/patente/" + this.numero_patente + "/categorie", {
          categoria: this.nuovaCategoria,
        })
        .then((response) => {
          console.log(response.data);
        });
    },
    open: function () {
      this.showModalAggiungiCategoria = true;
      this.getCategoriePossibili();
    },
    close: function () {
      this.showModalAggiungiCategoria = false;
    },
  },
};
</script>

<style scoped>
* {
  box-sizing: border-box;
}

.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  transition: opacity 0.3s ease;
}

.modal-container {
  width: 700px;
  margin: 40px auto 0;
  background-color: #fff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
  transition: all 0.3s ease;
  font-family: Helvetica, Arial, sans-serif;
}

.modal-header {
  background: #1d70b8;
}

.modal-footer {
  background: #1d70b8;
}

.modal-header h3 {
  margin-top: 0;
  color: #fff;
}

.modal-body {
  margin: 20px 0;
}

.text-right {
  text-align: right;
}

.form-label {
  display: block;
  margin-bottom: 1em;
}

.form-label > .form-control {
  margin-top: 0.5em;
}

.form-control {
  display: block;
  width: 100%;
  padding: 0.5em 1em;
  line-height: 1.5;
  border: 1px solid #ddd;
}

/*
	 * The following styles are auto-applied to elements with
	 * transition="modal" when their visibility is toggled
	 * by Vue.js.
	 *
	 * You can easily play with the modal transition by editing
	 * these styles.
	 */

.modal-enter {
  opacity: 0;
}

.modal-leave-active {
  opacity: 0;
}

.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}
</style>
