<template>
  <div>
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
          <span slot="no-options">{{msg}}</span>
    </v-select>
    <input type="hidden" :name="name" :value="sentvalue" >
  </div>
</template>


<script>
  import vSelect from "vue-select"
  export default {
    components: {vSelect},
    props: {
      selected:{ //object containing the selected items of the form ({id:name}) (e.g. {"275":"A. A. TARKOVSKIJ","374":"A. J. CRONIN","399":"A. MANZINO"}
        type:Object,
        default: () => ({}),
      },
      multiple: { //if "true" allow to select multiple choiches
        type: Boolean,
        default:false
      },
     placeholder: { // placeholder in the input field
       type: String,
       default:"Inserisci ..."
     },
     msg : { //message to show whith no data
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
        // list of the available options [{"value":x, "label":y}, {}]to be shown in the dropdown menu
        options: [],
        // item shown in the input field
        item: (!_.isEmpty(this.selected)) ? this.fromObjectsToValueLabels(this.selected) : null,
        // values to be sent
        sentvalue: null,
        }
      },
    methods: {
      addItem(){
        // TODO: add an item to the list of selected object
        // call this function after adding a Nuovo Autore or Nuovo editore after receiving an event...
        console.log("received event");
        // this.item :  [{label:"A. CIOCI E RIZIA GUARNIERI", value:323}]
        // if(this.multiple) // if mulitple=true, item is an array
        //   this.item[this.item.length] = item;
        // else // multiple=false than item is an object
        //   this.item = item;
        // console.log(item +" added");
      },
      getOptions(search, loading) {
        loading(true)
        axios.get(this.url, { params: { term: search } })
              .then(response => {
                this.options = response.data;
                loading(false)
              })
              .catch(error => {});
            },
      changed: function(selectedValues){
        if(this.multiple) // if mulitple=true, selectedValues is an array
          this.sentvalue = _.map(selectedValues,"value"); // from ["value":1, "value":2] to [1,2,3,4]
        else // multiple=false than selectedValues is an object
          this.sentvalue = selectedValues.value;
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
    }
  }
</script>
