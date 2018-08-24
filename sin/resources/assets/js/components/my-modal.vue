<!-- template for the modal component -->
<template id="modal-template">
  <div>
    <transition name="modal">
        <div class="modal-mask"  @click="close" v-show="showModal">
            <div class="modal-container"  @click.stop>
                <div class="modal-header">
                    <h3>{{title}}</h3>
                </div>
                <div class="modal-body">
                    <label class="form-label">
                        Nome e Cognome
                      <input class="form-control" v-model="nome" :placeholder="placeholder">
                    </label>
                </div>
                <p class="text-success" v-show="msg">{{msg}}</p>
                <div class="modal-footer text-right">
                  <a class="btn btn-primary" role="button" @click="close()">
                      Esci
                  </a>
                  <input class="btn btn-success" type="button"  @click="aggiungi()" :disabled="isDisabled()" value="Aggiungi">
                    <!-- <a class="btn btn-primary" role="button" @click="aggiungi()" :disabled="true">
                        Aggiungi
                    </a> -->
                </div>
            </div>
        </div>
    </transition>
    <a class="btn btn-success" @click="showModal = true">{{title}}</a>
  </div>
</template>
</div>

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
    background-color: rgba(0, 0, 0, .5);
    transition: opacity .3s ease;
}

.modal-container {
    width: 300px;
    margin: 40px auto 0;
    padding: 20px 30px;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
    transition: all .3s ease;
    font-family: Helvetica, Arial, sans-serif;
}

.modal-header h3 {
    margin-top: 0;
    color: #007bff;
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

<script>
  export default {
    props: ["title","urlPost","placeholder"],
    data() {
      return{
        msg: null,
        nome: null,
        showModal:false
    }
    },
    methods: {
      isDisabled(){
        return  !this.nome;
      },
      aggiungi: function () {
        axios.post(this.urlPost,{
          nome: this.nome,
        })
        .then(response => {
         // console.log(response);
         this.msg = response.data.msg;
         // this.$parent.$emit('my-event');
         // console.log("emit event");
         // this.close()
        })
        .catch(e => {
          this.errors.push(e);
          console.log("error");
        })
      },
      close: function () {
        this.msg = null,
        this.nome = null,
        this.showModal=false;
        }
    }
  }
</script>
