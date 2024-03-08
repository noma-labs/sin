<template>
  <div class="row">
    <div class="col-md-8">
      <div v-if="msgDisplay" class="alert alert-warning" role="alert">
        {{ msg }}
        <button
          type="button"
          class="close"
          aria-label="Close"
          @click="closeMsg()"
        >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <table class="table table-hover table-bordered table-sm">
        <thead class="thead-inverse">
          <tr>
            <th width="30%">Codice</th>
            <th width="10%">Tipo</th>
            <th width="30%">Note</th>
            <th width="20%">Operazioni</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(f, i) in filtri" :key="f.id">
            <td>{{ f.codice }}</td>
            <td>{{ f.tipo }}</td>
            <td>{{ f.note }}</td>
            <td>
              <div class="row">
                <div class="col">
                  <button
                    class="btn btn-warning btn-sm btn-block"
                    @click="showModal = true"
                  >
                    Modifica
                  </button>
                  <!-- <my-modal modal-title="Modifica Filtro" button-style="btn-block btn-warning btn-sm" button-title="Modifica"></my-modal> -->
                </div>
                <div class="col">
                  <button
                    type="button"
                    class="btn btn-danger btn-sm btn-block"
                    @click="elimina(f.id, i)"
                  >
                    Elimina
                  </button>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <!-- modal modifica filtro -->
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      ulrFiltri: "/api/officina/filtri",
      urlElimina: "/api/officina/filtri/elimina",
      ulrTipi: "/api/officina/filtri/tipi",
      filtri: {},
      tipi: [],
      msg: "",
      msgDisplay: false,
      modificaOp: false,
      cod: "",
      not: "",
      tip: "",
    };
  },
  mounted() {
    axios.get(this.ulrFiltri).then((response) => {
      this.filtri = response.data;
    });
    axios.get(this.ulrTipi).then((response) => {
      this.tipi = response.data;
    });
  },
  methods: {
    modifica: function (id, index) {
      // this.modificaOp = true;
      // this.cod = this.filtri[index].codice;
      // this.not = this.filtri[index].note;
      console.log(id, index);
    },
    elimina: function (id, index) {
      axios
        .delete(this.urlElimina, { data: { filtro: id } })
        .then((response) => {
          if (response.data.status == "success") {
            this.msg = response.data.msg;
            this.msgDisplay = true;
          } else if (response.data.status == "error") {
            this.msg = response.data.msg;
            this.msgDisplay = true;
          }
        });
      this.filtri.splice(index, 1);
      this.msg = "Filtro eliminato: codice " + this.filtri[index].codice;
      this.msgDisplay = true;
    },
    closeMsg: function () {
      this.msgDisplay = false;
      this.msg = "";
    },
  },
};
</script>

<style></style>
