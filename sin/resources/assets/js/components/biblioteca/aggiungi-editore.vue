<template>
    <div class="row">
    <div class="col-md-8">
        <label for="xEditore" class="control-label">Editore/i (*)</label>
        <v-select
          :options="options"
          :debounce="500"
          :on-change="changed"
          :on-search="getOptions"
          :placeholder="placeholder"
          label="label"
          :multiple="multiple"
          v-model="item"
          :disabled="disabled"
          >
          <span slot="no-options">{{msgNoOptions}}</span>
        </v-select>
        <input type="hidden" :name="name" :value="sentvalue" >
    </div>

    <div class="col-md-4">
        <label>&nbsp;</label>
        <a class="btn btn-success" @click="openModal">{{title}}</a>
     </div> 
     <transition name="modal">
        <div class="modal-mask"  @click="closeModal" v-show="showModal">
            <div class="modal-container"  @click.stop>
                <div class="modal-header">
                    <h3>{{title}}</h3>
                </div>
                <div class="modal-body">
                    <label class="form-label">
                        Nome e Cognome
                      <input class="form-control" v-model="nome" :placeholder="modalPlaceholder">
                    </label>
                </div>
                <p class="text-success" v-show="msg">{{msg}}</p>
                <div class="modal-footer text-right">
                  <a class="btn btn-danger" role="button" @click="closeModal()">
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
  </div>
</template>

<script>
  import vSelect from "vue-select"
  export default {
    components: {vSelect},
    props: {
        title: { // string into button and modal title
            type: String,
            default: "Nuovo Editore"
        },
        modalPlaceholder: { // placeholder in the input field
            type: String,
            default:"Esempio: MOndadori"
        },
      selected:{ //object containing the selected items of the form ({id:name}) (e.g. {"275":"A. A. TARKOVSKIJ","374":"A. J. CRONIN","399":"A. MANZINO"}
        type:Object,
        default: () => ({}),
      },
      apiBibliotecaEditori:{ //api.biblioteca.editori
          type:String
      },
      apiBibliotecaEditoriCreate: { //api.biblioteca.editori.create:  create a new editore
          type:String
      },
      multiple: { //if "true" allow to select multiple choiches
        type: Boolean,
        default:false
      },
     placeholder: { // placeholder in the input field
       type: String,
       default:"Inserisci ..."
     },
     msgNoOptions : { //message to show whith no data
       type: String,
       default:"Nessun risultato ottenuto..."
     },
     name:{ // name of the input sent to the server
       type: String,
       required: false
     },
     url: {  // url to get the data
        type: String,
        required: false
       },
     /**
      * Disable the entire component.
      * @type {Boolean}
      */
     disabled: {
       type: Boolean,
       default: false
     },
   },
    data() {
      return {

        options: [],
        // item shown in the input field
        item: (!_.isEmpty(this.selected)) ? this.fromObjectsToValueLabels(this.selected) : [],
        // values to be sent
        sentvalue: null,
        //for modal
		showModal: false,  // if true modal is shown
        msg: null, // message receives form the server wjen a new editpre is inserted
        nome: null,
        }
      },
    methods: {
        getOptions(search, loading) {
            loading(true);
            axios.get(this.apiBibliotecaEditori, { params: { term: search } })
                .then(response => {
                    this.options = response.data;
                    loading(false)
                })
                .catch(error => {});
        },
        fromObjectsToValueLabels: function(el){
            /* Transform an object into an array of object of the type {"value":x, "label":y}
            *  e.g. input: {"275":"A. A. TARKOVSKIJ",399":"A. MANZINO"}
            *      output: [{"value":275,"label":"A. A. TARKOVSKIJ"},{"value":399},"label":"A. MAnzino"]
            */
            // console.log(el);
            var label_to_value = [];
            console.log(el);
            _.forOwn(el, function(value, key) {
                label_to_value.push({"label":value, "value":key})
            });
            if(this.multiple) return label_to_value
            else return label_to_value[0]
        },
        changed: function(selectedValues){
            // selectedValues: [ [label: "A.C. GRAFICHE" value: 19], []]
            if(this.multiple) // if multiple=true, selectedValues is an array
            this.sentvalue = _.map(selectedValues,"value"); // from ["value":1, "value":2] to [1,2,3,4]
            else // multiple=false than selectedValues is an object
            this.sentvalue = selectedValues.value;
         },
        isDisabled(){
            return  !this.nome;
        },
        aggiungi: function () {
            axios.post(this.apiBibliotecaEditoriCreate,{
            nome: this.nome,
            })
            .then(response => {
                console.log(response.data);
                this.msg = response.data.msg;
                if(response.data.err == 0){
                    this.item.push({"label": response.data.data.editore,"value":response.data.data.id});
                    this.closeModal();
                    this.sentvalue
                }
            })
            .catch(e => {
            this.errors.push(e);
            console.log("error");
            })
        },
        openModal: function(){
            this.showModal = true;
        },
        closeModal: function(){
            this.showModal = false;
        }
    
    }
  }
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
	    background-color: rgba(0, 0, 0, .5);
	    transition: opacity .3s ease;
	}

	.modal-container {
	    width: 700px;
	    margin: 40px auto 0;
	    background-color: #fff;
	    box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
	    transition: all .3s ease;
	    font-family: Helvetica, Arial, sans-serif;
	}

	.modal-header {
		background: #1D70B8;
	}

	.modal-footer {
		background: #1D70B8;
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